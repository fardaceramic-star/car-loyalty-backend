<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->enum('type', ['visit_based','amount_based','referral','birthday','points','coupon','seasonal']);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 0)->default(0);
            $table->integer('min_visit_count')->default(0);
            $table->decimal('min_amount', 15, 0)->default(0);
            $table->decimal('max_discount', 15, 0)->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('promotion_id')->constrained()->cascadeOnDelete();
            $table->string('coupon_code', 30)->unique()->nullable();
            $table->boolean('is_used')->default(false);
            $table->dateTime('used_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
            $table->index(['customer_id', 'is_used']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_promotions');
        Schema::dropIfExists('promotions');
    }
};