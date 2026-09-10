<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Database connection
     *
     * @var string
     */
    protected $connection = 'pos';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_cart_details', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('sale_cart_id')
                ->constrained('sale_carts', 'id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignUlid('product_id')
                ->constrained('products', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('purchase_price')->default(0);
            $table->unsignedInteger('selling_price')->default(0);
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('subtotal')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_cart_details');
    }
};
