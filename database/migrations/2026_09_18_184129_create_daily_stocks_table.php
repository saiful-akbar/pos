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
        Schema::create('daily_stocks', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')
                ->constrained('users', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('date');
            $table->decimal('total_product', 15, 2)->default(0);
            $table->decimal('total_quantity', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_stocks');
    }
};
