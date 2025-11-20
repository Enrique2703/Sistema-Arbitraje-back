<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminar la clave foránea primero
        DB::statement('ALTER TABLE auditorias DROP FOREIGN KEY auditorias_usuario_id_foreign');
        
        // Hacer el campo nullable
        DB::statement('ALTER TABLE auditorias MODIFY usuario_id BIGINT UNSIGNED NULL');
        
        // Volver a crear la clave foránea con ON DELETE SET NULL
        DB::statement('ALTER TABLE auditorias ADD CONSTRAINT auditorias_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar la clave foránea
        DB::statement('ALTER TABLE auditorias DROP FOREIGN KEY auditorias_usuario_id_foreign');
        
        // Hacer el campo NOT NULL
        DB::statement('ALTER TABLE auditorias MODIFY usuario_id BIGINT UNSIGNED NOT NULL');
        
        // Volver a crear la clave foránea con ON DELETE CASCADE
        DB::statement('ALTER TABLE auditorias ADD CONSTRAINT auditorias_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE');
    }
};
