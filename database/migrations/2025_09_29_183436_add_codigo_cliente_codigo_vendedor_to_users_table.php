<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
        $table->string('codigo_cliente')->nullable()->after('rol_id');
        $table->string('codigo_vendedor')->nullable()->after('codigo_cliente');
        $table->unsignedInteger('max_sessions')->default(1)->after('codigo_vendedor');
        });
    }


    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['codigo_cliente', 'codigo_vendedor', 'max_sessions']);
        });
    }
};
