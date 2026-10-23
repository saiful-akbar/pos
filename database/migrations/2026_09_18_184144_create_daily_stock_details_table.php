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
        Schema::create('daily_stock_details', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('daily_stock_id')
                ->constrained('daily_stocks')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignUlid('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignUlid('unit_id')
                ->constrained('units')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('multiples_quantity', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_stock_details');
    }
};
