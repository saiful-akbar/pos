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
        Schema::create('role_modules', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('role_id')
                ->constrained('roles', 'id', 'role_id_foreign')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignUlid('module_id')
                ->constrained('modules', 'id', 'module_id_foreign')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('create');
            $table->boolean('read');
            $table->boolean('update');
            $table->boolean('delete');
            $table->timestamps();
            $table->softDeletes();

            $table->index('create', 'create_index');
            $table->index('read', 'read_index');
            $table->index('update', 'update_index');
            $table->index('delete', 'delete_index');
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
