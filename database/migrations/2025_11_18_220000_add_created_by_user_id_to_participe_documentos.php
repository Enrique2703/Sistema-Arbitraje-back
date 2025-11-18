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
            $table->unsignedBigInteger('created_by_user_id')->nullable()->after('participe_id')->comment('ID de la credencial que creó el documento');
            // No agregamos foreign key para evitar problemas de compatibilidad
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participe_documentos', function (Blueprint $table) {
            $table->dropColumn('created_by_user_id');
        });
    }
};
