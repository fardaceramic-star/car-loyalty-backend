<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\DiscountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private DiscountService $discountService) {}

    /**
     * ثبت سفارش جدید
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'coupon_code'    => 'nullable|string|max:30',
        ]);

        $customer = $request->user();

        return DB::transaction(function () use ($data, $customer) {
            // محاسبه جمع کل
            $totalAmount = 0;
            $itemsData = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "موجودی {$product->name} کافی نیست",
                    ], 422);
                }

                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                ];
            }

            // محاسبه تخفیف
            $discountResult = $this->discountService->calculate(
                $customer,
                $totalAmount,
                $data['coupon_code'] ?? null
            );

            $finalAmount = $discountResult['final_amount'];
            $discountAmount = $discountResult['total_discount'];

            // ثبت سفارش
            $order = Order::create([
                'customer_id'     => $customer->id,
                'order_type'      => 'online',
                'total_amount'    => $totalAmount,
                'discount_amount' => $discountAmount,
                'final_amount'    => $finalAmount,
                'status'          => 'pending',
            ]);

            // ثبت آیتم‌ها + کاهش موجودی
            foreach ($itemsData as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);

                Product::where('id', $item['product_id'])
                    ->decrement('stock', $item['quantity']);
            }

            // ثبت مراجعه (برای امتیاز و سطح)
            $this->discountService->recordVisit($customer, [
                'type'             => 'purchase',
                'amount'           => $totalAmount,
                'discount_applied' => $discountAmount,
                'description'      => 'سفارش آنلاین #' . $order->id,
            ]);

            return response()->json([
                'success' => true,
                'data'    => [
                    'order_id'        => $order->id,
                    'total_amount'    => $totalAmount,
                    'discount_amount' => $discountAmount,
                    'final_amount'    => $finalAmount,
                    'status'          => $order->status,
                ],
                'message' => 'سفارش با موفقیت ثبت شد',
            ]);
        });
    }

    /**
     * لیست سفارش‌های من
     */
    public function myOrders(Request $request): JsonResponse
    {
        $orders = Order::with('items.product')
            ->where('customer_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $orders->items(),
        ]);
    }

    /**
     * جزئیات یک سفارش
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->customer_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'دسترسی ندارید'], 403);
        }

        $order->load('items.product');

        return response()->json(['success' => true, 'data' => $order]);
    }
}