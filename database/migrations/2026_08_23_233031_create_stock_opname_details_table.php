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
        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('stock_opname_id')
                ->constrained('stock_opnames')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignUlid('product_id')
                ->constrained('products')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

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
        Schema::dropIfExists('stock_opname_details');
    }
};
