<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class EmployeeReportExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = collect();

        foreach ($this->employees as $employee) {
            $data->push([
                'Nama' => ucwords($employee->fullname),
                'Divisi' => ucwords($employee->division->name ?? '-'),
                'Gender' => ucwords($employee->gender),
                'No Telepon' => $employee->phone,
                'Email' => $employee->email,
                'Status' => ucwords($employee->status),
                'Cuti' => $employee->leave_total ?: 0,
                'Gaji' => 'Rp. ' . number_format($employee->salary_total, 0, ',', '.'),
                'Absen' => $employee->absent_total ?: 0,
            ]);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Nama',
            'Divisi',
            'Gender',
            'No Telepon',
            'Email',
            'Status',
            'Cuti',
            'Gaji',
            'Absen',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Insert 7 rows at the beginning for header
                $sheet->insertNewRowBefore(1, 7);
                
                // Company name and address - Row 1
                $sheet->setCellValue('A1', 'PT. MEGAJAYA SARANA NUSANTARA');
                $sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'B83E48']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'border' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'B83E48'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(20);
                
                // Address - Row 2
                $sheet->setCellValue('A2', 'Gedung Sarana Square Lt.3A Jl. Tebet Barat, Jakarta');
                $sheet->getStyle('A2:I2')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '555555']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(15);
                
                // Contact info - Row 3
                $sheet->setCellValue('A3', 'Email: megajayasarananusantara@gmail.com | Telp: 0811227337');
                $sheet->getStyle('A3:I3')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'border' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E0E0E0'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(15);
                
                // Empty row - Row 4
                $sheet->getRowDimension(4)->setRowHeight(8);
                
                // Title - Row 5
                $sheet->setCellValue('A5', 'LAPORAN DATA KARYAWAN');
                $sheet->getStyle('A5:I5')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'B83E48']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'border' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'B83E48'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension(5)->setRowHeight(18);
                
                // Date - Row 6
                $date = now()->format('d-m-Y H:i:s');
                $sheet->setCellValue('A6', 'Dicetak pada: ' . $date);
                $sheet->getStyle('A6:I6')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '777777']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);
                $sheet->getRowDimension(6)->setRowHeight(15);
                
                // Empty row - Row 7
                $sheet->getRowDimension(7)->setRowHeight(5);
                
                // ===== STYLING SETELAH INSERT ROWS =====
                
                // Header table styling (row 8) - WARNA MERAH HANYA DI SINI
                $sheet->getStyle('A8:I8')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'B83E48'],
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 12,
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
                
                // Body styling - TANPA WARNA/FILL, HANYA BORDER
                $dataStartRow = 9;
                $dataEndRow = $dataStartRow + $this->employees->count() - 1;
                
                $sheet->getStyle('A' . $dataStartRow . ':I' . $dataEndRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_NONE,
                    ],
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
                    'font' => [
                        'size' => 11,
                        'color' => ['rgb' => '000000'],
                    ],
                ]);
                
                // Center align specific columns
                $sheet->getStyle('A:C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('D:I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Merge cells for header
                $sheet->mergeCells('A1:I1');
                $sheet->mergeCells('A2:I2');
                $sheet->mergeCells('A3:I3');
                $sheet->mergeCells('A5:I5');
                $sheet->mergeCells('A6:I6');
                
                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(20);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(12);
                $sheet->getColumnDimension('G')->setWidth(10);
                $sheet->getColumnDimension('H')->setWidth(18);
                $sheet->getColumnDimension('I')->setWidth(10);
            }
        ];
    }
}
