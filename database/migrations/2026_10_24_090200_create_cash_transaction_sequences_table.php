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
        // Penghitung nomor urut transaksi kas per bulan (yymm). Baris ini dikunci selama transaksi database
        // berlangsung sehingga dua request bersamaan tidak pernah mendapat nomor yang sama.
        Schema::create('cash_transaction_sequences', function (Blueprint $table) {
            $table->char('period', 4)->primary()->comment('format: yymm');
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transaction_sequences');
    }
};
