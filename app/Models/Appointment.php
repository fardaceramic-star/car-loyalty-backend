<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'customer_id', 'service_type', 'preferred_date',
        'preferred_time', 'status', 'notes',
    ];

    protected $casts = ['preferred_date' => 'date'];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
}