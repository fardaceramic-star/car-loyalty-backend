<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $promotions = [
            ['title' => 'تخفیف معرفی دوستان', 'type' => 'referral', 'discount_percent' => 10, 'max_discount' => 300000, 'is_active' => true],
            ['title' => 'تخفیف تولد', 'type' => 'birthday', 'discount_percent' => 20, 'max_discount' => 500000, 'is_active' => true],
            ['title' => 'اعتبار هدیه امتیاز', 'type' => 'points', 'discount_amount' => 0, 'is_active' => true],
            ['title' => 'تخفیف ویژه تابستان', 'type' => 'seasonal', 'discount_percent' => 15, 'max_discount' => 400000, 'is_active' => true, 'starts_at' => now(), 'ends_at' => now()->addMonths(3)],
        ];

        foreach ($promotions as $p) {
            Promotion::updateOrCreate(['title' => $p['title']], $p);
        }
    }
}