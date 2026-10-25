<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    protected $fillable = [
        'customer_id', 'visit_type', 'amount',
        'discount_applied', 'final_amount', 'points_earned', 'description',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'discount_applied' => 'decimal:0',
        'final_amount' => 'decimal:0',
    ];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
}