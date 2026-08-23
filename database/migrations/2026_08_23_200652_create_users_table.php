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
        Schema::create('users', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('role_id')
                ->constrained('roles', 'id', 'users_role_id_foreign')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('username');
            $table->string('password');
            $table->string('full_name', 100);
            $table->string('photo', 100)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('username', 'users_username_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
