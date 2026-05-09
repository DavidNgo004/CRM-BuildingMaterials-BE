<?php

namespace App\Repositories;

use App\Models\Supplier;

class SupplierRepository
{
    public function paginate($perPage = 15, $search = null, $status = null)
    {
        $query = Supplier::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getAll($status = null)
    {
        $query = Supplier::query();
        
        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->latest()->get();
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
