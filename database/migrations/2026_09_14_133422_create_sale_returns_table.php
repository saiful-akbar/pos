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
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')
                ->constrained('users', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignUlid('sale_id')
                ->constrained('sales', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->char('return_number', 13)->unique()->comment('format: RTN2608-00001');
            $table->date('transaction_date');
            $table->decimal('total_quantity', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
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
        Schema::dropIfExists('sale_returns');
    }
};
