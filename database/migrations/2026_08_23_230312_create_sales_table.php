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
        Schema::create('sales', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')
                ->constrained('users', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignUlid('customer_id')
                ->nullable()
                ->constrained('customers', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->char('invoice_number', 13)->unique()->comment('format: INV2608-00001');
            $table->year('transaction_year');
            $table->unsignedTinyInteger('transaction_month');
            $table->date('transaction_date');
            $table->decimal('total_quantity', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);
            $table->enum('payment_method', ['cash', 'debit', 'credit', 'qris'])->default('cash');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('transaction_year');
            $table->index('transaction_month');
            $table->index('transaction_date');
            $table->index(['transaction_year', 'transaction_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
