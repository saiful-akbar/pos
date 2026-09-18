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
        Schema::create('monthly_stock_opname_details', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('monthly_stock_opname_id')
                ->constrained('monthly_stock_opnames')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignUlid('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->integer('system_stock');
            $table->integer('physical_stock');
            $table->integer('difference');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_stock_opname_details');
    }
};
