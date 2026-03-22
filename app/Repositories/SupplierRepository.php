<?php

namespace App\Repositories;

use App\Models\Supplier;

class SupplierRepository
{
    public function paginate($perPage = 15, $search = null)
    {
        $query = Supplier::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
        }

        return $query->latest()->paginate($perPage);
    }

    public function getAll()
    {
        return Supplier::latest()->get();
    }

    public function find($id)
    {
        return Supplier::find($id);
    }

    public function create($data)
    {
        return Supplier::create($data);
    }

    public function update($supplier, $data)
    {
        $supplier->update($data);
        return $supplier;
    }

    public function delete($supplier)
    {
        return $supplier->delete();
    }
}
