<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('category', 100)->nullable();
            $table->string('brand', 100)->nullable();
            $table->decimal('price', 15, 0);
            $table->integer('stock')->default(0);
            $table->string('image_url', 255)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->enum('order_type', ['online', 'in_store'])->default('online');
            $table->decimal('total_amount', 15, 0);
            $table->decimal('discount_amount', 15, 0)->default(0);
            $table->decimal('final_amount', 15, 0);
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained();
            $table->string('service_name', 200)->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
    }
};