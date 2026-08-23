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
        Schema::create('payments', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('sale_id')
                ->constrained('sales', 'id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->enum('payment_method', ['cash', 'qris', 'transfer', 'debit', 'credit']);
            $table->string('reference_number', 100)->nullable()->comment('Debit or credit card number');
            $table->dateTime('paid_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
