<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'name',
        'unit',
        'import_price',
        'sell_price',
        'stock',
        'reorder_level',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function importDetails()
    {
        return $this->hasMany(ImportDetail::class);
    }

    public function exportDetails()
    {
        return $this->hasMany(ExportDetail::class);
    }
}
