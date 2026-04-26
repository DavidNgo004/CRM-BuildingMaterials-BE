<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            // Row 1: Example data
            [
                'Xi măng Hà Tiên', // Tên sản phẩm
                '1', // Nhà cung cấp ID
                'Bao', // Đơn vị tính
                '75000', // Giá nhập
                '80000', // Giá bán
                '100', // Tồn kho
                '20', // Định mức tồn
                '1', // Trạng thái (1: Đang bán, 0: Ngừng bán)
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Tên sản phẩm',
            'Nhà cung cấp ID',
            'Đơn vị tính',
            'Giá nhập',
            'Giá bán',
            'Tồn kho',
            'Định mức tồn',
            'Trạng thái',
        ];
    }
}
