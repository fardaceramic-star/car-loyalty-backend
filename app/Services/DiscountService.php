<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerPromotion;
use App\Models\Visit;
use App\Models\Promotion;
use App\Models\PointTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DiscountService
{
    private array $visitTiers;
    private array $amountTiers;

    public function __construct()
    {
        $this->visitTiers = config('loyalty.visit_tiers');
        $this->amountTiers = config('loyalty.amount_tiers');
    }

    public function calculate(Customer $customer, float $amount, ?string $couponCode = null): array
    {
        $discounts = [];

        $visitPercent = $this->getVisitPercent($customer->total_visits);
        if ($visitPercent > 0) {
            $discounts[] = [
                'type'    => 'visit_based',
                'label'   => 'تخفیف مراجعه ' . ($customer->total_visits + 1) . 'ام',
                'percent' => $visitPercent,
                'amount'  => (int) floor($amount * $visitPercent / 100),
            ];
        }

        $amountPercent = $this->getAmountPercent($amount);
        if ($amountPercent > 0) {
            $discounts[] = [
                'type'    => 'amount_based',
                'label'   => 'تخفیف خرید عمده',
                'percent' => $amountPercent,
                'amount'  => (int) floor($amount * $amountPercent / 100),
            ];
        }

        if ($this->isBirthdayMonth($customer)) {
            $bPercent = config('loyalty.birthday.percent');
            $bCap     = config('loyalty.birthday.max_discount');
            $bAmount  = min((int) floor($amount * $bPercent / 100), $bCap);
            $discounts[] = [
                'type'    => 'birthday',
                'label'   => 'تخفیف تولد',
                'percent' => $bPercent,
                'amount'  => $bAmount,
            ];
        }

        if ($couponCode) {
            $coupon = $this->getCouponDiscount($customer, $couponCode, $amount);
            if ($coupon) $discounts[] = $coupon;
        }

        return $this->merge($discounts, $amount);
    }

    private function getVisitPercent(int $visits): int
    {
        $percent = 0;
        foreach ($this->visitTiers as $tier) {
            if ($visits + 1 >= $tier['visits']) $percent = $tier['percent'];
        }
        return $percent;
    }

    private function getAmountPercent(float $amount): int
    {
        $percent = 0;
        foreach ($this->amountTiers as $tier) {
            if ($amount >= $tier['amount']) $percent = $tier['percent'];
        }
        return $percent;
    }

    private function isBirthdayMonth(Customer $customer): bool
    {
        return $customer->birth_date && now()->month === $customer->birth_date->month;
    }

    private function getCouponDiscount(Customer $customer, string $code, float $amount): ?array
    {
        $cp = CustomerPromotion::with('promotion')
            ->where('customer_id', $customer->id)
            ->where('coupon_code', $code)
            ->where('is_used', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$cp) return null;

        $p = $cp->promotion;
        $amt = 0;

        if ($p->discount_percent > 0) {
            $amt = (int) floor($amount * $p->discount_percent / 100);
            if ($p->max_discount) $amt = min($amt, (int) $p->max_discount);
        } elseif ($p->discount_amount > 0) {
            $amt = (int) $p->discount_amount;
        }

        return [
            'type'      => 'coupon',
            'label'     => $p->title,
            'percent'   => (float) $p->discount_percent,
            'amount'    => $amt,
            'coupon_id' => $cp->id,
        ];
    }

    private function merge(array $discounts, float $amount): array
    {
        $exclusive = collect($discounts)->filter(fn($d) =>
            in_array($d['type'], ['visit_based', 'amount_based', 'birthday'])
        );
        $coupons = collect($discounts)->filter(fn($d) => $d['type'] === 'coupon');

        $best = $exclusive->sortByDesc('amount')->first();
        $total = ($best['amount'] ?? 0) + $coupons->sum('amount');
        $total = min($total, $amount);

        $items = [];
        if ($best) $items[] = $best;
        foreach ($coupons as $c) $items[] = $c;

        return [
            'items'            => $items,
            'excluded'         => $exclusive->filter(fn($d) => $best && $d !== $best)->values()->all(),
            'total_discount'   => $total,
            'final_amount'     => $amount - $total,
            'discount_percent' => $amount > 0 ? round($total * 100 / $amount, 2) : 0,
        ];
    }

    public function recordVisit(Customer $customer, array $data): array
    {
        return DB::transaction(function () use ($customer, $data) {
            $amount   = (float) $data['amount'];
            $discount = (float) ($data['discount_applied'] ?? 0);
            $final    = $amount - $discount;
            $points   = (int) floor($final / config('loyalty.points.per_amount'));

            Visit::create([
                'customer_id'      => $customer->id,
                'visit_type'       => $data['type'],
                'amount'           => $amount,
                'discount_applied' => $discount,
                'final_amount'     => $final,
                'points_earned'    => $points,
                'description'      => $data['description'] ?? null,
            ]);

            $newVisits = $customer->total_visits + 1;
            $newPoints = $customer->total_points + $points;
            $newSpent  = (float) $customer->total_spent + $final;
            $newLevel  = $this->calcLevel($newVisits, $newSpent);
            $levelUpgraded = $newLevel > $customer->loyalty_level;

            $customer->update([
                'total_visits'  => $newVisits,
                'total_points'  => $newPoints,
                'total_spent'   => $newSpent,
                'loyalty_level' => $newLevel,
            ]);

            if ($points > 0) {
                PointTransaction::create([
                    'customer_id' => $customer->id,
                    'points'      => $points,
                    'reason'      => 'خرید/خدمت',
                    'created_at'  => now(),
                ]);
            }

            if (!empty($data['coupon_id'])) {
                CustomerPromotion::where('id', $data['coupon_id'])
                    ->update(['is_used' => true, 'used_at' => now()]);
            }

            if ($newVisits === 1 && $customer->referred_by_id) {
                $this->rewardReferrer($customer);
            }

            return [
                'new_visits'     => $newVisits,
                'new_points'     => $newPoints,
                'new_level'      => $newLevel,
                'points_earned'  => $points,
                'level_upgraded' => $levelUpgraded,
                'level_info'     => config("loyalty.levels.{$newLevel}"),
            ];
        });
    }

    private function rewardReferrer(Customer $referred): void
    {
        $referrer = Customer::find($referred->referred_by_id);
        if (!$referrer) return;

        $promo = Promotion::where('type', 'referral')->where('is_active', true)->first();
        if ($promo) {
            CustomerPromotion::create([
                'customer_id'  => $referrer->id,
                'promotion_id' => $promo->id,
                'coupon_code'  => 'REF' . strtoupper(Str::random(6)),
                'expires_at'   => now()->addMonths(config('loyalty.referral.coupon_expire_months')),
            ]);
        }

        $referrer->increment('total_points', config('loyalty.referral.points_reward'));
    }

    public function calcLevel(int $visits, float $spent): int
    {
        $levels = config('loyalty.levels');
        $level = 1;
        foreach ($levels as $id => $info) {
            if ($visits >= $info['min_visits'] || $spent >= $info['min_spent']) {
                $level = $id;
            }
        }
        return $level;
    }

    public function convertPoints(Customer $customer, int $points): array
    {
        if ($customer->total_points < $points) {
            throw new \Exception('امتیاز کافی نیست');
        }

        $value = ($points / config('loyalty.points.convert_rate'))
               * config('loyalty.points.convert_value');

        DB::transaction(function () use ($customer, $points, $value) {
            $customer->decrement('total_points', $points);

            PointTransaction::create([
                'customer_id' => $customer->id,
                'points'      => -$points,
                'reason'      => 'تبدیل به اعتبار ' . number_format($value) . ' تومان',
                'created_at'  => now(),
            ]);

            $promo = Promotion::firstOrCreate(
                ['title' => 'اعتبار هدیه امتیاز'],
                ['type' => 'points', 'discount_amount' => $value, 'is_active' => true]
            );

            CustomerPromotion::create([
                'customer_id'  => $customer->id,
                'promotion_id' => $promo->id,
                'coupon_code'  => 'PTS' . strtoupper(Str::random(6)),
                'expires_at'   => now()->addMonths(6),
            ]);
        });

        return ['points_used' => $points, 'credit_value' => $value];
    }
}