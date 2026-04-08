<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportExcelData implements ToCollection
{
    protected $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function collection(Collection $rows)
    {
        ($this->callback)($rows);
    }
}
