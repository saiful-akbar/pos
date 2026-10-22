<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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
        DB::statement('ALTER TABLE sale_return_details MODIFY grand_total DECIMAL(15, 2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE sale_return_cart_details MODIFY grand_total DECIMAL(15, 2) NOT NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE sale_return_details MODIFY grand_total DECIMAL(5, 2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE sale_return_cart_details MODIFY grand_total DECIMAL(5, 2) NOT NULL DEFAULT 0');
    }
};
