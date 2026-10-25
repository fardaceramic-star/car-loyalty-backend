<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    protected $fillable = [
        'title', 'description', 'type', 'discount_percent',
        'discount_amount', 'min_visit_count', 'min_amount',
        'max_discount', 'is_active', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:0',
        'max_discount' => 'decimal:0',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function customerPromotions(): HasMany { return $this->hasMany(CustomerPromotion::class); }
}