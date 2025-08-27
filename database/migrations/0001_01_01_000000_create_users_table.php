<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Solo crear la tabla si no existe
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('email')->unique();
                $table->string('password');
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        // Tabla de tokens de restablecimiento de contraseña
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // Tabla de sesiones
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->integer('user_id')->nullable()->index(); // <<--- cambiar a INT
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();

                // Definir la FK manualmente con mismo tipo
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Evitar eliminar la tabla 'users' para no romper relaciones
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
