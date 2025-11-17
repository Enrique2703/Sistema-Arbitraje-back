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
        Schema::create('participe_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participe_id')->constrained('participes')->onDelete('cascade');
            $table->foreignId('expediente_id')->constrained('expedientes')->onDelete('cascade');
            $table->string('parte')->nullable();
            $table->text('sumilla')->nullable();
            $table->string('enlace_descarga');
            $table->boolean('habilitado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participe_documentos');
    }
};