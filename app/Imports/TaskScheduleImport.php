<?php

namespace App\Imports;

use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Task;
use App\Support\AttendancePolicy;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TaskScheduleImport implements SkipsEmptyRows, ToCollection, WithHeadingRow
{
    protected array $errors = [];

    public function __construct(protected Task $task) {}

    public function headingRow(): int
    {
        return 8;
    }

    public function collection(Collection $rows): void
    {
        $assignedEmployeeIds = $this->task->employees()
            ->pluck('employees.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        DB::transaction(function () use ($rows, $assignedEmployeeIds) {
            foreach ($rows as $index => $row) {
                $excelRow = $index + 9;

                $employeeId = (int) ($row['employee_id'] ?? 0);
                $taskId = (int) ($row['task_id'] ?? 0);

                $date = $this->parseDate(
                    $row['tanggal'] ?? null,
                    $excelRow
                );

                if ($taskId !== $this->task->id) {
                    $this->errors[] =
                        "Baris {$excelRow}: Task ID tidak sesuai dengan tugas yang sedang di-import.";

                    continue;
                }

                if (! in_array($employeeId, $assignedEmployeeIds, true)) {
                    $this->errors[] =
                        "Baris {$excelRow}: karyawan tidak terdaftar di tugas ini.";

                    continue;
                }

                if (! $date) {
                    continue;
                }

                /*
                 * 1. LIBUR
                 */
                if ($this->isOffDay($row)) {
                    Schedule::where('task_id', $this->task->id)
                        ->where('employee_id', $employeeId)
                        ->whereDate('date', $date)
                        ->delete();

                    continue;
                }

                /*
                 * 2. CUTI APPROVED
                 */
                if (AttendancePolicy::hasApprovedLeaveOnDate($employeeId, $date)) {
                    Schedule::where('task_id', $this->task->id)
                        ->where('employee_id', $employeeId)
                        ->whereDate('date', $date)
                        ->delete();

                    continue;
                }

                /*
                 * 3. HARI KERJA NORMAL
                 * Baru cari Shift
                 */
                $shift = $this->resolveShift($row, $excelRow);

                if (! $shift) {
                    $this->errors[] =
                        "Baris {$excelRow}: isi Shift ID atau Nama Shift.";

                    continue;
                }

                /*
                 * 4. SIMPAN JADWAL
                 */
                Schedule::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'task_id' => $this->task->id,
                        'date' => $date,
                    ],
                    [
                        'shift_id' => $shift->id,
                        'source' => 'manual',
                    ]
                );
            }

            if (! empty($this->errors)) {
                throw ValidationException::withMessages([
                    'schedule_file' => $this->errors,
                ]);
            }
        });
    }

    private function parseDate($value, int $excelRow): ?string
    {
        if (! $value) {
            $this->errors[] = "Baris {$excelRow}: tanggal wajib diisi.";

            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::instance(Date::excelToDateTimeObject($value))->toDateString();
            }

            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            $this->errors[] = "Baris {$excelRow}: format tanggal tidak valid.";

            return null;
        }
    }

    private function resolveShift($row, int $excelRow): ?Shift
    {
        $shiftId = trim((string) ($row['shift_id'] ?? ''));
        $shiftName = trim((string) ($row['nama_shift'] ?? ''));

        if ($shiftId !== '' && is_numeric($shiftId)) {
            $shift = $this->task->Shift->firstWhere('id', (int) $shiftId);

            if (! $shift) {
                $this->errors[] = "Baris {$excelRow}: Shift ID tidak ditemukan di tugas ini.";
            }

            return $shift;
        }

        if ($shiftName !== '') {
            $shift = $this->task->Shift->first(
                fn ($item) => strtolower(trim($item->name)) === strtolower($shiftName)
            );

            if (! $shift) {
                $this->errors[] = "Baris {$excelRow}: Nama Shift tidak ditemukan di tugas ini.";
            }

            return $shift;
        }

        return null;
    }

    private function isOffDay($row): bool
    {
        $shiftId = strtoupper(trim((string) ($row['shift_id'] ?? '')));
        $shiftName = strtoupper(trim((string) ($row['nama_shift'] ?? '')));

        return $shiftId === 'LIBUR' || $shiftName === 'LIBUR';
    }
}
