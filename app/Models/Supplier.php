<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'tax_code',
        'phone',
        'email',
        'address',
        'status',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            if (empty($supplier->code)) {
                $supplier->code = 'NCC-' . strtoupper(Str::random(6)); // VD: NCC-A1B2C3
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id', 'id');
    }
}
