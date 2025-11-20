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
        Schema::table('auditorias', function (Blueprint $table) {
            $table->string('usuario_nombre')->nullable()->after('usuario_id');
            $table->string('expediente')->nullable()->after('usuario_nombre');
            $table->text('detalle')->nullable()->after('accion');
            $table->string('tipo_accion')->nullable()->after('detalle'); // crear, editar, eliminar, subir
            $table->string('modulo')->nullable()->after('tipo_accion'); // expedientes, documentos, cédulas, etc.
            $table->string('ip')->nullable()->after('modulo');
            $table->text('datos_anteriores')->nullable()->after('ip');
            $table->text('datos_nuevos')->nullable()->after('datos_anteriores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditorias', function (Blueprint $table) {
            $table->dropColumn([
                'usuario_nombre',
                'expediente',
                'detalle',
                'tipo_accion',
                'modulo',
                'ip',
                'datos_anteriores',
                'datos_nuevos'
            ]);
        });
    }
};
