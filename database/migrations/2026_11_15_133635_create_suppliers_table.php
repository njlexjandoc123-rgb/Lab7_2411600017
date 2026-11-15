<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone', 40)->nullable();
            $table->text('address')->nullable();
            $table->string('payment_terms', 100)->default('COD');
            $table->unsignedSmallInteger('lead_time_days')->default(7);
            $table->decimal('minimum_order_amount', 12, 2)->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
