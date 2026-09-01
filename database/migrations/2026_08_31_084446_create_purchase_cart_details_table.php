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
        Schema::create('purchase_cart_details', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('purchase_cart_id')
                ->constrained('purchase_carts')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignUlid('product_id')
                ->constrained('products')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('selling_price')->default(0);
            $table->unsignedInteger('subtotal_price')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_cart_details');
    }
};
