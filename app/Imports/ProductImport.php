<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class ProductImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Try to find the product by name to update, otherwise create a new one
        $product = Product::where('name', $row['ten_san_pham'])->first();

        $data = [
            'name' => $row['ten_san_pham'],
            'supplier_id' => $row['nha_cung_cap_id'],
            'unit' => $row['don_vi_tinh'],
            'import_price' => $row['gia_nhap'],
            'sell_price' => $row['gia_ban'],
            'stock' => isset($row['ton_kho']) ? $row['ton_kho'] : 0,
            'reorder_level' => isset($row['dinh_muc_ton']) ? $row['dinh_muc_ton'] : null,
            'status' => isset($row['trang_thai']) ? $row['trang_thai'] : 1,
            'updated_by' => Auth::id(),
        ];

        if ($product) {
            $product->update($data);
            return null; // Return null so ToModel doesn't try to create a new model instance if we already updated
        }

        return new Product($data);
    }
}
