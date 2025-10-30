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
        Schema::create('participe_documento_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participe_documentos_id')
                    ->constrained('participe_documentos')
                    ->onDelete('cascade');
            $table->string('archivo_adjunto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participe_documento_archivos');
    }
};