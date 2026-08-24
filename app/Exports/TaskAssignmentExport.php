<?php

namespace App\Exports;

use App\Models\LeaveRequest;
use App\Models\Task;
use App\Support\AttendancePolicy;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TaskAssignmentExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    public function __construct(protected Task $task, protected Carbon $weekStart, protected Carbon $weekEnd)
    {
    }

    public function collection()
    {
        $location = $this->task->location->first();
        $shiftReference = $this->shiftReferenceText();
        $leaveDates = $this->leaveDatesByEmployee();

        $rows = new Collection();
        $rowNumber = 1;

        foreach ($this->task->employees as $employee) {
            for ($date = $this->weekStart->copy(); $date->lte($this->weekEnd); $date->addDay()) {
                $isOnLeave = in_array(
                    $date->toDateString(),
                    $leaveDates[$employee->id] ?? [],
                    true
                );

                $rows->push([
                    'No' => $rowNumber++,
                    'Task ID' => $this->task->id,
                    'Employee ID' => $employee->id,
                    'NIK' => $employee->nik ?? '-',
                    'Nama Karyawan' => ucwords($employee->fullname),
                    'Divisi' => ucwords($employee->division->name ?? '-'),
                    'Posisi' => ucwords($employee->position->name ?? '-'),
                    'Tanggal' => $date->format('Y-m-d'),
                    'Hari' => $date->translatedFormat('l'),
                    'Nama Shift' => $isOnLeave ? 'CUTI' : '',
                    'Shift ID' => $isOnLeave ? 'CUTI' : '',
                    'Jam Masuk' => '',
                    'Jam Keluar' => '',
                    'Keterangan' => $isOnLeave ? 'Karyawan sedang cuti' : '',
                    'Nama Tugas' => ucwords($this->task->name),
                    'Lokasi' => $location->name ?? '-',
                    'Latitude' => $location->latitude ?? '-',
                    'Longitude' => $location->longitude ?? '-',
                    'Radius Meter' => $location->radius ?? '-',
                    'Referensi Shift' => $shiftReference,
                ]);
            }
        }

        if ($rows->isEmpty()) {
            $rows->push([
                'No' => '',
                'Task ID' => $this->task->id,
                'Employee ID' => '',
                'NIK' => '',
                'Nama Karyawan' => 'Belum ada karyawan yang di-assign',
                'Divisi' => '',
                'Posisi' => '',
                'Tanggal' => $this->weekStart->format('Y-m-d'),
                'Hari' => $this->weekStart->translatedFormat('l'),
                'Nama Shift' => '',
                'Shift ID' => '',
                'Jam Masuk' => '',
                'Jam Keluar' => '',
                'Keterangan' => '',
                'Nama Tugas' => ucwords($this->task->name),
                'Lokasi' => $location->name ?? '-',
                'Latitude' => $location->latitude ?? '-',
                'Longitude' => $location->longitude ?? '-',
                'Radius Meter' => $location->radius ?? '-',
                'Referensi Shift' => $shiftReference,
            ]);
        }

        return $rows;
    }

    private function setShiftFormulas(Worksheet $sheet, int $row, int $referenceEndRow): void
    {
        $lookupRange = '$V$2:$Y$'.$referenceEndRow;

        $sheet->setCellValue("K{$row}", '=IF(J'.$row.'="","",IFERROR(VLOOKUP(J'.$row.','.$lookupRange.',2,FALSE),""))');
        $sheet->setCellValue("L{$row}", '=IF(OR(J'.$row.'="",J'.$row.'="LIBUR",J'.$row.'="CUTI"),"",IFERROR(VLOOKUP(J'.$row.','.$lookupRange.',3,FALSE),""))');
        $sheet->setCellValue("M{$row}", '=IF(OR(J'.$row.'="",J'.$row.'="LIBUR",J'.$row.'="CUTI"),"",IFERROR(VLOOKUP(J'.$row.','.$lookupRange.',4,FALSE),""))');
    }

    private function applyDropdown(Worksheet $sheet, string $cell, string $options): void
    {
        $validation = $sheet->getCell($cell)->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowDropDown(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle('Pilihan tidak valid');
        $validation->setError('Pilih nilai dari daftar yang tersedia.');
        $validation->setFormula1($options);
    }

    private function shiftReferenceText(): string
    {
        $shiftSummary = $this->task->Shift->map(function ($shift) {
            return $shift->id.' = '.$shift->name.' ('.$shift->start_time.'-'.$shift->end_time.')';
        });

        return $shiftSummary
            ->push('LIBUR = hari libur / tidak masuk')
            ->push('CUTI = karyawan sedang cuti approved/confirmed')
            ->implode(' | ');
    }

    private function leaveDatesByEmployee(): array
    {
        $employeeIds = $this->task->employees->pluck('id');
        $leaveDates = [];

        $leaves = LeaveRequest::whereIn('employee_id', $employeeIds)
            ->whereIn('status', AttendancePolicy::APPROVED_LEAVE_STATUSES)
            ->whereDate('start_date', '<=', $this->weekEnd->toDateString())
            ->whereDate('end_date', '>=', $this->weekStart->toDateString())
            ->get();

        foreach ($leaves as $leave) {
            $start = Carbon::parse($leave->start_date)->max($this->weekStart);
            $end = Carbon::parse($leave->end_date)->min($this->weekEnd);

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $leaveDates[$leave->employee_id][] = $date->toDateString();
            }
        }

        return $leaveDates;
    }

    private function formatDate($date): string
    {
        return $date ? Carbon::parse($date)->format('d-m-Y') : '-';
    }

    public function headings(): array
    {
        return [
            'No',
            'Task ID',
            'Employee ID',
            'NIK',
            'Nama Karyawan',
            'Divisi',
            'Posisi',
            'Tanggal',
            'Hari',
            'Nama Shift',
            'Shift ID',
            'Jam Masuk',
            'Jam Keluar',
            'Keterangan',
            'Nama Tugas',
            'Lokasi',
            'Latitude',
            'Longitude',
            'Radius Meter',
            'Referensi Shift',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, 7);
                $lastColumn = 'T';

                $sheet->setCellValue('A1', 'PT. MEGAJAYA SARANA NUSANTARA');
                $sheet->setCellValue('A2', 'Gedung Sarana Square Lt.3A Jl. Tebet Barat, Jakarta');
                $sheet->setCellValue('A3', 'Email: megajayasarananusantara@gmail.com | Telp: 0811227337');
                $sheet->setCellValue('A5', 'TEMPLATE JADWAL KERJA MINGGUAN');
                $sheet->setCellValue('A6', 'Tugas: '.ucwords($this->task->name).' | Periode: '.$this->weekStart->format('d-m-Y').' s/d '.$this->weekEnd->format('d-m-Y').' | Dicetak: '.now()->format('d-m-Y H:i:s'));
                $sheet->setCellValue('A7', 'Isi kolom Nama Shift untuk setiap karyawan dan tanggal. Shift ID, Jam Masuk, dan Jam Keluar akan terisi otomatis. Referensi shift: '.$this->shiftReferenceText());

                foreach ([1, 2, 3, 5, 6, 7] as $row) {
                    $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
                }

                $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'B83E48']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle("A2:{$lastColumn}3")->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '555555']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle("A5:{$lastColumn}7")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'B83E48']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
                ]);

                $sheet->getStyle("A8:{$lastColumn}8")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'B83E48'],
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'border' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $dateCount = $this->weekStart->diffInDays($this->weekEnd) + 1;
                $dataRowCount = max(1, $this->task->employees->count() * $dateCount);
                $dataEndRow = 8 + $dataRowCount;
                $sheet->getStyle("A8:{$lastColumn}{$dataEndRow}")->applyFromArray([
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'border' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                $sheet->setCellValue('V1', 'Nama Shift');
                $sheet->setCellValue('W1', 'Shift ID');
                $sheet->setCellValue('X1', 'Jam Masuk');
                $sheet->setCellValue('Y1', 'Jam Keluar');

                $referenceRow = 2;
                foreach ($this->task->Shift as $shift) {
                    $sheet->setCellValue("V{$referenceRow}", $shift->name);
                    $sheet->setCellValue("W{$referenceRow}", $shift->id);
                    $sheet->setCellValue("X{$referenceRow}", $shift->start_time);
                    $sheet->setCellValue("Y{$referenceRow}", $shift->end_time);
                    $referenceRow++;
                }

                $sheet->setCellValue("V{$referenceRow}", 'LIBUR');
                $sheet->setCellValue("W{$referenceRow}", 'LIBUR');
                $sheet->setCellValue("X{$referenceRow}", '');
                $sheet->setCellValue("Y{$referenceRow}", '');
                $referenceEndRow = $referenceRow;
                $referenceRow++;

                $sheet->setCellValue("V{$referenceRow}", 'CUTI');
                $sheet->setCellValue("W{$referenceRow}", 'CUTI');
                $sheet->setCellValue("X{$referenceRow}", '');
                $sheet->setCellValue("Y{$referenceRow}", '');
                $referenceEndRow = $referenceRow;

                for ($row = 9; $row <= $dataEndRow; $row++) {
                    $this->applyDropdown($sheet, "J{$row}", '$V$2:$V$'.$referenceEndRow);
                    $this->setShiftFormulas($sheet, $row, $referenceEndRow);
                }

                $sheet->freezePane('A9');
                $sheet->getStyle('A:C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('H:M')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('N:T')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('J9:J'.$dataEndRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8F5E9'],
                    ],
                ]);
                $sheet->getStyle('J9:M'.$dataEndRow)
                    ->getProtection()
                    ->setLocked(false);
                $sheet->getStyle('N9:N'.$dataEndRow)
                    ->getProtection()
                    ->setLocked(false);
                $sheet->getColumnDimension('K')->setVisible(false);
                foreach (['V', 'W', 'X', 'Y'] as $column) {
                    $sheet->getColumnDimension($column)->setVisible(false);
                }
                $sheet->getRowDimension(7)->setRowHeight(34);
            },
        ];
    }
}
