<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cuantia_indeterminada', function (Blueprint $table) {
            $table->id();
            $table->decimal('porcentaje_arbitro', 8, 2);
            $table->decimal('porcentaje_secretario', 8, 2);
            $table->decimal('porcentaje_nulidad', 8, 2);
            $table->decimal('porcentaje_resolucion', 8, 2);
            $table->decimal('porcentaje_tarifa', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuantia_indeterminada');
    }
};
