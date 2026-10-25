<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * ورود/ثبت‌نام مستقیم بدون کد تایید
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'phone'     => 'required|regex:/^09\d{9}$/',
            'full_name' => 'nullable|string|max:150',
            'referral'  => 'nullable|string|max:20',
        ]);

        $referrer = null;
        if ($request->referral) {
            $referrer = Customer::where('referral_code', $request->referral)->first();
        }

        $customer = Customer::firstOrCreate(
            ['phone' => $request->phone],
            [
                'full_name'      => $request->full_name ?? 'کاربر جدید',
                'password_hash'  => Hash::make(Str::random(32)),
                'referral_code'  => Customer::generateReferralCode(),
                'referred_by_id' => $referrer?->id,
            ]
        );

        // اگر مشتری از قبل وجود داشت ولی نامش "کاربر جدید" بود و الان نام داده
        if ($request->full_name && $customer->full_name === 'کاربر جدید') {
            $customer->update(['full_name' => $request->full_name]);
        }

        $token = $customer->createToken('mobile')->plainTextToken;

        return response()->json([
            'success'  => true,
            'token'    => $token,
            'customer' => $this->formatCustomer($customer),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success'  => true,
            'customer' => $this->formatCustomer($request->user()),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name'  => 'sometimes|string|max:150',
            'email'      => 'sometimes|email|max:150',
            'birth_date' => 'sometimes|date',
        ]);

        $request->user()->update($data);

        return response()->json([
            'success'  => true,
            'customer' => $this->formatCustomer($request->user()->fresh()),
        ]);
    }

    private function formatCustomer(Customer $customer): array
    {
        $level = config("loyalty.levels.{$customer->loyalty_level}");
        $nextLevel = config("loyalty.levels." . ($customer->loyalty_level + 1));

        return [
            'id'            => $customer->id,
            'full_name'     => $customer->full_name,
            'phone'         => $customer->phone,
            'email'         => $customer->email,
            'birth_date'    => $customer->birth_date?->format('Y-m-d'),
            'referral_code' => $customer->referral_code,
            'loyalty_level' => $customer->loyalty_level,
            'level_info'    => $level,
            'next_level'    => $nextLevel,
            'total_points'  => $customer->total_points,
            'total_visits'  => $customer->total_visits,
            'total_spent'   => (float) $customer->total_spent,
            'created_at'    => $customer->created_at->format('Y-m-d'),
        ];
    }
}