<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointTransaction extends Model
{
    public $timestamps = false;

    protected $fillable = ['customer_id', 'points', 'reason', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
}