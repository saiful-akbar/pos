<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->ulid('id');

            $table->foreignUlid('product_id')
                ->constrained('products', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignUlid('unit_id')
                ->constrained('units', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('multiples_quantity', 15, 2)->default(1);
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
