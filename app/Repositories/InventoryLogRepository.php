<?php

namespace App\Repositories;

use App\Models\InventoryLog;

class InventoryLogRepository
{
    public function create(array $data)
    {
        return InventoryLog::create($data);
    }
}