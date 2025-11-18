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
        Schema::table('participe_documento_archivos', function (Blueprint $table) {
            $table->bigInteger('tamano')->nullable()->after('archivo_adjunto')->comment('Tamaño del archivo en bytes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participe_documento_archivos', function (Blueprint $table) {
            $table->dropColumn('tamano');
        });
    }
};
