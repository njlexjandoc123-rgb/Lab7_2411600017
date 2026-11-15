<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        DB::table('products')
            ->select('brand')
            ->whereNotNull('brand')
            ->where('brand', '<>', '')
            ->distinct()
            ->orderBy('brand')
            ->get()
            ->each(function ($product): void {
                DB::table('brands')->insertOrIgnore([
                    'name' => $product->brand,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
