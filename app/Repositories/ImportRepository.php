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
}
