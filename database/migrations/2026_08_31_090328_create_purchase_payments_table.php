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
        Schema::create('purchase_payments', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('purchase_id')
                ->constrained('purchases')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignUlid('user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'transfer', 'credit', 'debit']);
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puchase_payments');
    }
};
