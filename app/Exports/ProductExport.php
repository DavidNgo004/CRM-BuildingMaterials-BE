<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::all();
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

    /**
    * @param mixed $product
    */
    public function map($product): array
    {
        return [
            $product->name,
            $product->supplier_id,
            $product->unit,
            $product->import_price,
            $product->sell_price,
            $product->stock,
            $product->reorder_level,
            $product->status,
        ];
    }
}
