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
        Schema::create('cart_details', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('cart_id')
                ->constrained('carts', 'id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignUlid('product_id')
                ->constrained('products', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('cost_price', 15, 2);
            $table->decimal('discount', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_details');
    }
};
