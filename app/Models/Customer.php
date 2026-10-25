<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'full_name', 'phone', 'email', 'birth_date', 'password_hash',
        'referral_code', 'referred_by_id', 'loyalty_level',
        'total_points', 'total_visits', 'total_spent', 'is_active',
    ];

    protected $hidden = ['password_hash'];

    protected $casts = [
        'birth_date' => 'date',
        'total_spent' => 'decimal:0',
        'is_active' => 'boolean',
    ];

    public function visits(): HasMany { return $this->hasMany(Visit::class); }
    public function promotions(): HasMany { return $this->hasMany(CustomerPromotion::class); }
    public function orders(): HasMany { return $this->hasMany(Order::class); }
    public function appointments(): HasMany { return $this->hasMany(Appointment::class); }
    public function referrer(): BelongsTo { return $this->belongsTo(Customer::class, 'referred_by_id'); }
    public function referrals(): HasMany { return $this->hasMany(Customer::class, 'referred_by_id'); }
    public function pointTransactions(): HasMany { return $this->hasMany(PointTransaction::class); }

    public static function generateReferralCode(): string
    {
        do {
            $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        } while (self::where('referral_code', $code)->exists());
        return $code;
    }
}