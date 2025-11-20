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
            $table->boolean('revisado')->default(false)->after('habilitado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participe_documentos', function (Blueprint $table) {
            $table->dropColumn('revisado');
        });
    }
};
