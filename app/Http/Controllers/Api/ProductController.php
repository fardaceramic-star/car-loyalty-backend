<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * لیست محصولات با فیلتر و جستجو
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::where('is_active', true);

        // جستجو
        if ($request->search) {
            $q = $request->search;
            $query->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%")
                   ->orWhere('brand', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // فیلتر دسته‌بندی
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // فیلتر برند
        if ($request->brand) {
            $query->where('brand', $request->brand);
        }

        // مرتب‌سازی
        $sort = $request->sort ?? 'newest';
        match ($sort) {
            'cheapest' => $query->orderBy('price', 'asc'),
            'expensive' => $query->orderBy('price', 'desc'),
            default => $query->orderByDesc('created_at'),
        };

        $products = $query->paginate(12);

        return response()->json([
            'success' => true,
            'data'    => $products->items(),
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    /**
     * جزئیات یک محصول
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $product,
        ]);
    }

    /**
     * لیست دسته‌بندی‌ها و برندها (برای فیلتر)
     */
    public function filters(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'categories' => Product::where('is_active', true)
                    ->distinct()->pluck('category')->filter()->values(),
                'brands' => Product::where('is_active', true)
                    ->distinct()->pluck('brand')->filter()->values(),
            ],
        ]);
    }
}