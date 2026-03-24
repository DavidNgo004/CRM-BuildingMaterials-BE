<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Export extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'user_id',
        'customer_id',
        'total_price',
        'discount_amount',
        'grand_total',
        'status',
        'note',
    ];

    /**
     * Get the user that created the export.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the customer for the export.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the details for the export.
     */
    public function details()
    {
        return $this->hasMany(ExportDetail::class);
    }
}
