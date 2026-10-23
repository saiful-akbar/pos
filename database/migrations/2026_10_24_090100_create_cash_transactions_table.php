<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        // Transaksi kas masuk dan kas keluar (satu kas toko).
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')
                ->constrained('users', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->char('transaction_number', 13)->unique()->comment('format: CSH2610-00001');
            $table->date('transaction_date');
            $table->enum('type', ['INCOMING', 'OUTGOING']);
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('transaction_date');
            $table->index(['type', 'transaction_date']);
        });

        // Nominal harus lebih besar dari nol.
        DB::connection($this->connection)->statement(
            'ALTER TABLE cash_transactions ADD CONSTRAINT cash_transactions_amount_check CHECK (amount > 0)',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
