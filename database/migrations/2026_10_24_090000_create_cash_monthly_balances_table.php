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
        // Saldo awal aktual kas setiap bulan (hasil penghitungan kas fisik).
        Schema::create('cash_monthly_balances', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')
                ->constrained('users', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->year('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Satu periode hanya boleh memiliki satu saldo awal (termasuk baris yang sudah di-soft delete).
            $table->unique(['year', 'month'], 'cash_monthly_balances_year_month_unique');
        });

        // Bulan harus 1-12 dan saldo awal tidak boleh negatif.
        DB::connection($this->connection)->statement(
            'ALTER TABLE cash_monthly_balances ADD CONSTRAINT cash_monthly_balances_month_check CHECK (month BETWEEN 1 AND 12)',
        );
        DB::connection($this->connection)->statement(
            'ALTER TABLE cash_monthly_balances ADD CONSTRAINT cash_monthly_balances_opening_check CHECK (opening_balance >= 0)',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_monthly_balances');
    }
};
