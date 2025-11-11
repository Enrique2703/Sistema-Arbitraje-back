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
        Schema::create('cedulas', function (Blueprint $table) {
            $table->id();
            // referencia al documento (participe_documentos)
            $table->foreignId('documentos_id')->nullable()->constrained('participe_documentos')->onDelete('cascade');
            $table->text('comentarios')->nullable();
            // campos útiles adicionales
            $table->string('enviado_a')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cedulas');
    }
};
