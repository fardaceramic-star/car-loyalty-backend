<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerPromotion;
use App\Services\DiscountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function __construct(private DiscountService $service) {}

    public function calculate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|numeric|min:0',
            'coupon_code' => 'nullable|string|max:30',
        ]);

        $customer = Customer::findOrFail($data['customer_id']);
        $result = $this->service->calculate($customer, (float) $data['amount'], $data['coupon_code'] ?? null);

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function myCoupons(Request $request): JsonResponse
    {
        $coupons = CustomerPromotion::with('promotion')
            ->where('customer_id', $request->user()->id)
            ->where('is_used', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get()
            ->map(fn($cp) => [
                'id'         => $cp->id,
                'code'       => $cp->coupon_code,
                'title'      => $cp->promotion->title,
                'percent'    => (float) $cp->promotion->discount_percent,
                'amount'     => (float) $cp->promotion->discount_amount,
                'expires_at' => $cp->expires_at?->format('Y-m-d'),
            ]);

        return response()->json(['success' => true, 'data' => $coupons]);
    }
}