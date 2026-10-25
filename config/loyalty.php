<?php

return [
    'visit_tiers' => [
        ['visits' => 2, 'percent' => 5],
        ['visits' => 3, 'percent' => 10],
        ['visits' => 4, 'percent' => 15],
        ['visits' => 5, 'percent' => 20],
        ['visits' => 6, 'percent' => 25],
    ],

    'amount_tiers' => [
        ['amount' => 2000000,  'percent' => 5],
        ['amount' => 5000000,  'percent' => 10],
        ['amount' => 10000000, 'percent' => 15],
    ],

    'birthday' => [
        'percent' => 20,
        'max_discount' => 500000,
    ],

    'points' => [
        'per_amount' => 10000,
        'convert_rate' => 100,
        'convert_value' => 50000,
    ],

    'levels' => [
        1 => ['name' => 'عادی',      'icon' => '⚪', 'min_visits' => 0,  'min_spent' => 0],
        2 => ['name' => 'نقره‌ای',   'icon' => '🥈', 'min_visits' => 3,  'min_spent' => 4000000],
        3 => ['name' => 'طلایی',     'icon' => '🥇', 'min_visits' => 6,  'min_spent' => 10000000],
        4 => ['name' => 'پلاتینیوم', 'icon' => '💎', 'min_visits' => 10, 'min_spent' => 20000000],
    ],

    'referral' => [
        'points_reward' => 100,
        'coupon_percent' => 10,
        'coupon_expire_months' => 3,
    ],
];