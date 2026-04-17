<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ImportTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Dữ liệu mẫu minh họa (2 hàng ví dụ).
     */
    public function array(): array
    {
        return [
            ['Xi măng Hà Tiên PCB40', 100, 85000],
            ['Thép xây dựng D10',     50,  18500],
        ];
    }

    /**
     * Dòng tiêu đề (hàng 1) – sẽ bị bỏ qua khi import.
     */
    public function headings(): array
    {
        return [
            'Tên sản phẩm',
            'Số lượng',
            'Đơn giá nhập (VNĐ)',
        ];
    }

    /**
     * Style cho header row.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'FFFFFF'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Độ rộng cột (pixels).
     */
    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 15,
            'C' => 25,
        ];
    }
}
