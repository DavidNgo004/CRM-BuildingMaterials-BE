<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Import extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'total_price',
        'discount_amount',
        'grand_total',
        'status',
        'note',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($import) {
            if (empty($import->code)) {
                $import->code = 'PN-' . strtoupper(Str::random(6)); // VD: PN-A1B2C3
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(ImportDetail::class);
    }
}
