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
        Schema::create('products', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('category_id')
                ->constrained('categories', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignUlid('unit_id')
                ->constrained('units', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('code', 50)->unique();
            $table->string('barcode', 50)->unique();
            $table->text('name')->nullable()->fulltext()->comment('product name or description');
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->integer('current_stock')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
