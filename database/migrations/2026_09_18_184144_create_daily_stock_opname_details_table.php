<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_stock_opname_details', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('daily_stock_opname_id')
                ->constrained('daily_stock_opnames')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignUlid('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('system_stock', 15, 2);
            $table->decimal('physical_stock', 15, 2);
            $table->decimal('difference', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_stock_opname_details');
    }
};
