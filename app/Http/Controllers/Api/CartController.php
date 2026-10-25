<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * دریافت سبد خرید کاربر جاری
     */
    public function index(Request $request): JsonResponse
    {
        $items = Cart::with('product')
            ->where('customer_id', $request->user()->id)
            ->get()
            ->map(fn($c) => [
                'id'         => $c->product->id,
                'name'       => $c->product->name,
                'price'      => (float) $c->product->price,
                'quantity'   => $c->quantity,
                'stock'      => $c->product->stock,
                'brand'      => $c->product->brand,
                'category'   => $c->product->category,
            ]);

        return response()->json(['success' => true, 'data' => $items]);
    }

    /**
     * افزودن محصول به سبد
     */
    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($product->stock < $data['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'موجودی کافی نیست',
            ], 422);
        }

        $cart = Cart::firstOrNew([
            'customer_id' => $request->user()->id,
            'product_id'  => $product->id,
        ]);

        $newQty = ($cart->quantity ?? 0) + $data['quantity'];
        if ($newQty > $product->stock) $newQty = $product->stock;

        $cart->quantity = $newQty;
        $cart->save();

        return response()->json(['success' => true, 'message' => 'به سبد اضافه شد']);
    }

    /**
     * به‌روزرسانی تعداد
     */
    public function update(Request $request, $productId): JsonResponse
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::where('customer_id', $request->user()->id)
            ->where('product_id', $productId)
            ->firstOrFail();

        $product = Product::findOrFail($productId);
        $qty = min($data['quantity'], $product->stock);
        $cart->update(['quantity' => $qty]);

        return response()->json(['success' => true]);
    }

    /**
     * حذف یک محصول
     */
    public function remove(Request $request, $productId): JsonResponse
    {
        Cart::where('customer_id', $request->user()->id)
            ->where('product_id', $productId)
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * خالی کردن سبد
     */
    public function clear(Request $request): JsonResponse
    {
        Cart::where('customer_id', $request->user()->id)->delete();
        return response()->json(['success' => true]);
    }
}