<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'باتری خودرو ۶۰ آمپر',
                'category' => 'باتری',
                'brand' => 'سپاهان',
                'price' => 4500000,
                'stock' => 25,
                'description' => 'باتری ۶۰ آمپر با گارانتی ۱۸ ماهه',
            ],
            [
                'name' => 'دینام پراید اصلی',
                'category' => 'دینام و استارت',
                'brand' => 'ایساکو',
                'price' => 3200000,
                'stock' => 12,
                'description' => 'دینام اصلی پراید با گارانتی',
            ],
            [
                'name' => 'استارت سمند EF7',
                'category' => 'دینام و استارت',
                'brand' => 'ایساکو',
                'price' => 2800000,
                'stock' => 8,
                'description' => 'استارت سمند EF7 اصل',
            ],
            [
                'name' => 'لامپ LED جلو H4',
                'category' => 'روشنایی',
                'brand' => 'فیلیپس',
                'price' => 850000,
                'stock' => 40,
                'description' => 'لامپ LED با نور سفید ۶۰۰۰K',
            ],
            [
                'name' => 'بوق خودرو دوگانه',
                'category' => 'بوق و علامت',
                'brand' => 'بوش',
                'price' => 420000,
                'stock' => 30,
                'description' => 'بوق دوگانه با صدای بلند',
            ],
            [
                'name' => 'دوربین دنده عقب',
                'category' => 'دوربین و سنسور',
                'brand' => 'XTRONS',
                'price' => 1200000,
                'stock' => 15,
                'description' => 'دوربین دنده عقب با دید در شب',
            ],
            [
                'name' => 'سنسور دنده عقب ۴ چشم',
                'category' => 'دوربین و سنسور',
                'brand' => 'استیل',
                'price' => 950000,
                'stock' => 20,
                'description' => 'سنسور دنده عقب ۴ چشم با نمایشگر',
            ],
            [
                'name' => 'فیوز خودرو ۳۰ آمپر',
                'category' => 'فیوز و رله',
                'brand' => 'بوش',
                'price' => 35000,
                'stock' => 100,
                'description' => 'فیوز تیغه‌ای ۳۰ آمپر',
            ],
            [
                'name' => 'رله ۴ پایه',
                'category' => 'فیوز و رله',
                'brand' => 'بوش',
                'price' => 85000,
                'stock' => 80,
                'description' => 'رله ۴ پایه ۱۲ ولت',
            ],
            [
                'name' => 'کویل آتش خودرو',
                'category' => 'سیستم جرقه',
                'brand' => 'ایساکو',
                'price' => 780000,
                'stock' => 18,
                'description' => 'کویل آتش پراید و تیبا',
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['name' => $p['name']], $p);
        }
    }
}