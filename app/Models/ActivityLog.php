<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false; // chỉ có created_at

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'entity_type',
        'entity_id',
        'old_data',
        'new_data',
        'description',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'old_data'   => 'array',
        'new_data'   => 'array',
        'created_at' => 'datetime',
    ];

    // ── Quan hệ ──────────────────────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Hằng số action ───────────────────────────────────────────────────────
    const CREATE_IMPORT   = 'CREATE_IMPORT';
    const UPDATE_IMPORT   = 'UPDATE_IMPORT';
    const APPROVE_IMPORT  = 'APPROVE_IMPORT';
    const COMPLETE_IMPORT = 'COMPLETE_IMPORT';
    const CANCEL_IMPORT   = 'CANCEL_IMPORT';
    const DELETE_IMPORT   = 'DELETE_IMPORT';

    const CREATE_EXPORT   = 'CREATE_EXPORT';
    const APPROVE_EXPORT  = 'APPROVE_EXPORT';
    const CANCEL_EXPORT   = 'CANCEL_EXPORT';
    const DELETE_EXPORT   = 'DELETE_EXPORT';

    const CREATE_PRODUCT  = 'CREATE_PRODUCT';
    const UPDATE_PRODUCT  = 'UPDATE_PRODUCT';
    const DELETE_PRODUCT  = 'DELETE_PRODUCT';

    const CREATE_EXPENSE  = 'CREATE_EXPENSE';
    const UPDATE_EXPENSE  = 'UPDATE_EXPENSE';
    const DELETE_EXPENSE  = 'DELETE_EXPENSE';
}
