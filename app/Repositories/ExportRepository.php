<?php

namespace App\Repositories;

use App\Models\Export;

class ExportRepository
{
    public function paginate($perPage = 15, $search = null)
    {
        $query = Export::with('customer', 'user', 'details.product');
        if ($search) {
            $query->where('code', 'LIKE', "%{$search}%");
        }
        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function find($id)
    {
        return Export::with('customer', 'user', 'details.product')->findOrFail($id);
    }
}
