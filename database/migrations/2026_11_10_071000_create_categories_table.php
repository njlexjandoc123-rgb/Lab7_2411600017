<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        DB::table('products')
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->distinct()
            ->orderBy('category')
            ->get()
            ->each(function ($product): void {
                DB::table('categories')->insertOrIgnore([
                    'name' => $product->category,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
