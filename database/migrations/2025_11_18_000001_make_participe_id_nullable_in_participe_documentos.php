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
        Schema::table('participe_documentos', function (Blueprint $table) {
            // Eliminar la restricción de clave foránea
            $table->dropForeign(['participe_id']);
            
            // Hacer la columna nullable
            $table->foreignId('participe_id')->nullable()->change();
            
            // Volver a agregar la restricción de clave foránea con nullable
            $table->foreign('participe_id')
                  ->references('id')
                  ->on('participes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participe_documentos', function (Blueprint $table) {
            // Eliminar la restricción de clave foránea
            $table->dropForeign(['participe_id']);
            
            // Hacer la columna no nullable
            $table->foreignId('participe_id')->nullable(false)->change();
            
            // Volver a agregar la restricción de clave foránea
            $table->foreign('participe_id')
                  ->references('id')
                  ->on('participes')
                  ->onDelete('cascade');
        });
    }
};
