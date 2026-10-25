<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 150);
            $table->string('phone', 15)->unique();
            $table->string('email', 150)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('password_hash');
            $table->string('referral_code', 20)->unique();
            $table->foreignId('referred_by_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->tinyInteger('loyalty_level')->default(1);
            $table->integer('total_points')->default(0);
            $table->integer('total_visits')->default(0);
            $table->decimal('total_spent', 15, 0)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['loyalty_level', 'total_visits']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};