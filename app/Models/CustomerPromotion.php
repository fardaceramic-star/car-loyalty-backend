<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerPromotion extends Model
{
    protected $fillable = [
        'customer_id', 'promotion_id', 'coupon_code',
        'is_used', 'used_at', 'expires_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function promotion(): BelongsTo { return $this->belongsTo(Promotion::class); }
}