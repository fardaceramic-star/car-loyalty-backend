<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'customer_id', 'order_type', 'total_amount',
        'discount_amount', 'final_amount', 'status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:0',
        'discount_amount' => 'decimal:0',
        'final_amount' => 'decimal:0',
    ];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
}