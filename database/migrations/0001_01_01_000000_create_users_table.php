<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ================================
        // Tabla roles
        // ================================
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->unsignedBigInteger('id', true); // AUTO_INCREMENT BIGINT
                $table->string('nombre', 50);
            });
        }

        // ================================
        // Tabla users
        // ================================
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Solo agregar remember_token si no existe
                if (!Schema::hasColumn('users', 'remember_token')) {
                    $table->rememberToken()->after('activo');
                }
            });
        } 

        // ================================
        // Tabla password_reset_tokens
        // ================================
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // ================================
        // Tabla sessions
        // ================================
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->integer('user_id')->nullable(); // Cambiado a integer para que coincida con users.id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('sessions')) {
            Schema::dropIfExists('sessions');
        }

        if (Schema::hasTable('password_reset_tokens')) {
            Schema::dropIfExists('password_reset_tokens');
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'rol_id')) {
                    $table->dropForeign(['rol_id']);
                    $table->dropColumn('rol_id');
                }
            });
            // No eliminamos la tabla para conservar datos
        }

        if (Schema::hasTable('roles')) {
            Schema::dropIfExists('roles');
        }
    }
};

