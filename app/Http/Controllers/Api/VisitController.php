<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Visit;
use App\Services\DiscountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function __construct(private DiscountService $service) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_id'      => 'required|exists:customers,id',
            'type'             => 'required|in:service,purchase,repair',
            'amount'           => 'required|numeric|min:0',
            'discount_applied' => 'nullable|numeric|min:0',
            'coupon_id'        => 'nullable|exists:customer_promotions,id',
            'description'      => 'nullable|string',
        ]);

        $customer = Customer::findOrFail($data['customer_id']);
        $result = $this->service->recordVisit($customer, $data);

        return response()->json([
            'success' => true,
            'data'    => $result,
            'message' => $result['level_upgraded']
                ? 'تبریک! سطح وفاداری شما ارتقا یافت!'
                : 'مراجعه با موفقیت ثبت شد.',
        ]);
    }

    public function myHistory(Request $request): JsonResponse
    {
        $visits = Visit::where('customer_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $visits->items(),
            'meta'    => [
                'current_page' => $visits->currentPage(),
                'total'        => $visits->total(),
                'last_page'    => $visits->lastPage(),
            ],
        ]);
    }

    public function convertPoints(Request $request): JsonResponse
    {
        $data = $request->validate(['points' => 'required|integer|min:100']);

        try {
            $result = $this->service->convertPoints($request->user(), $data['points']);
            return response()->json([
                'success' => true,
                'data'    => $result,
                'message' => "{$data['points']} امتیاز تبدیل شد",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}