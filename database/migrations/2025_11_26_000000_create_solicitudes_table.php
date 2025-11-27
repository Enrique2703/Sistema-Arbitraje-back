<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('participe_id');
            $table->string('estado');
            $table->string('demandante');
            $table->string('demandado');
            $table->timestamps();

            $table->foreign('participe_id')->references('id')->on('participes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
