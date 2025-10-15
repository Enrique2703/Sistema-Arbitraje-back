<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameContraseñaInCredenciales extends Migration
{
    public function up(): void
    {
        Schema::table('credenciales', function (Blueprint $table) {
            if (Schema::hasColumn('credenciales', 'contraseña') && !Schema::hasColumn('credenciales', 'password')) {
                $table->renameColumn('contraseña', 'password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('credenciales', function (Blueprint $table) {
            if (Schema::hasColumn('credenciales', 'password') && !Schema::hasColumn('credenciales', 'contraseña')) {
                $table->renameColumn('password', 'contraseña');
            }
        });
    }
}
