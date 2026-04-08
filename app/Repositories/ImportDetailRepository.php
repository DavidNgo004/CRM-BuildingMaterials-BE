<?php

namespace App\Repositories;

use App\Models\ImportDetail;

class ImportDetailRepository
{
    public function create(array $data)
    {
        return ImportDetail::create($data);
    }
}