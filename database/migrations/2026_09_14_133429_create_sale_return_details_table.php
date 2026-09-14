<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Database connection.
     *
     * @var string
     */
    protected $connection = 'pos';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_return_details', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('sale_return_id')
                ->constrained('sale_returns', 'id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignUlid('product_id')
                ->constrained('products', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_return_details');
    }
};
