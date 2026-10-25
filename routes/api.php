<?php

use App\Http\Controllers\Api\{
    AuthController,
    DiscountController,
    VisitController,
    ReportController,
    CustomerController,
    ProductController,
    OrderController,
    CartController,
};
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ============ ورود مستقیم ============
    Route::post('auth/login', [AuthController::class, 'login']);

    // ============ محصولات (عمومی) ============
    Route::get('products',          [ProductController::class, 'index']);
    Route::get('products/filters',  [ProductController::class, 'filters']);
    Route::get('products/{product}',[ProductController::class, 'show']);

    // ============ نیازمند لاگین ============
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::put('me', [AuthController::class, 'updateProfile']);

        // تخفیف
        Route::post('discount/calculate', [DiscountController::class, 'calculate']);
        Route::get('discount/my-coupons', [DiscountController::class, 'myCoupons']);

        // مراجعات
        Route::post('visits',          [VisitController::class, 'store']);
        Route::get('visits/my-history',[VisitController::class, 'myHistory']);
        Route::post('points/convert',  [VisitController::class, 'convertPoints']);

        // سبد خرید
        Route::get('cart',                   [CartController::class, 'index']);
        Route::post('cart/add',              [CartController::class, 'add']);
        Route::put('cart/update/{productId}',[CartController::class, 'update']);
        Route::delete('cart/remove/{productId}', [CartController::class, 'remove']);
        Route::delete('cart/clear',          [CartController::class, 'clear']);
        
        // سفارش‌ها
        Route::post('orders',          [OrderController::class, 'store']);
        Route::get('orders/my-orders', [OrderController::class, 'myOrders']);
        Route::get('orders/{order}',   [OrderController::class, 'show']);

        // گزارش‌ها (ادمین)
        Route::prefix('reports')->group(function () {
            Route::get('dashboard',  [ReportController::class, 'dashboard']);
            Route::get('visits',     [ReportController::class, 'visits']);
            Route::get('customers',  [ReportController::class, 'customers']);
            Route::get('promotions', [ReportController::class, 'promotions']);
        });

        Route::get('customers',            [CustomerController::class, 'index']);
        Route::get('customers/{customer}', [CustomerController::class, 'show']);
    });
});