<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('honorarios_tribunales', function (Blueprint $table) {
            $table->id();
            $table->string('escala');
            $table->decimal('rango_min', 15, 2);
            $table->decimal('rango_max', 15, 2);
            $table->decimal('porcentaje', 8, 2);
            $table->decimal('monto_max', 15, 2)->nullable();
            $table->decimal('monto_base', 15, 2)->nullable();
            $table->string('regla')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('honorarios_tribunales');
    }
};
