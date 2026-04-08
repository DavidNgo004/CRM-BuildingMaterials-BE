<?php

namespace App\Repositories;

use App\Models\Import;

class ImportRepository
{
    public function paginate($perPage = 15, $search = null)
    {
        $query = Import::with(['user', 'details.product.supplier']);

        if ($search) {
            $query->where('code', 'like', '%' . $search . '%')
                ->orWhereHas('details.product.supplier', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
        }

        return $query->latest()->paginate($perPage);
    }

    public function find($id)
    {
        return Import::with(['user', 'details.product.supplier'])->find($id);
    }

    /**
     * Tạo mới một phiếu nhập hàng. 
     */
    public function create(array $data)
    {
        return Import::create($data);
    }

    /**
     * Cập nhật total price sau khi đã insert
     */
    public function updateTotals(Import $import)
    {
        $total = $import->details()->sum('total_price');

        return $import->update([
            'total_price' => $total,
            'grand_total' => $total
        ]);
    }
}