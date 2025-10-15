<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoUsuarioToCredenciales extends Migration
{
    public function up(): void
    {
        Schema::table('credenciales', function (Blueprint $table) {
            if (!Schema::hasColumn('credenciales', 'tipo_usuario')) {
                $table->string('tipo_usuario', 50)->nullable()->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('credenciales', function (Blueprint $table) {
            if (Schema::hasColumn('credenciales', 'tipo_usuario')) {
                $table->dropColumn('tipo_usuario');
            }
        });
    }
}
