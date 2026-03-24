<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'export_id',
        'product_id',
        'quantity',
        'unit_price',
        'import_price',
        'total_price',
    ];

    /**
     * Get the export that owns the detail.
     */
    public function export()
    {
        return $this->belongsTo(Export::class);
    }

    /**
     * Get the product associated with the export detail.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
