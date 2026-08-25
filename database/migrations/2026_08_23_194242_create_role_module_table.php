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
        Schema::create('role_module', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('role_id')
                ->constrained('roles', 'id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignUlid('module_id')
                ->constrained('modules', 'id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('read');
            $table->boolean('create');
            $table->boolean('update');
            $table->boolean('remove');
            $table->boolean('delete');
            $table->timestamps();
            $table->softDeletes();

            $table->index('create');
            $table->index('read');
            $table->index('update');
            $table->index('delete');

            $table->unique(['role_id', 'module_id'], 'role_module_pivot_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_modules');
    }
};
