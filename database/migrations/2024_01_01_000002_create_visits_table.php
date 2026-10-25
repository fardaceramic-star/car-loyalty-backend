<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->enum('visit_type', ['service', 'purchase', 'repair']);
            $table->decimal('amount', 15, 0);
            $table->decimal('discount_applied', 15, 0)->default(0);
            $table->decimal('final_amount', 15, 0);
            $table->integer('points_earned')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['customer_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};