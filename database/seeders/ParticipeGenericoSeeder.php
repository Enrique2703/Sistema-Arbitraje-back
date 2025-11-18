<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ParticipeGenericoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existe el partícipe genérico
        $participeExiste = DB::table('participes')->where('nombres', 'Árbitro Sistema')->first();
        
        if (!$participeExiste) {
            // Crear una credencial genérica para el partícipe
            $credencialId = DB::table('credenciales')->insertGetId([
                'email' => 'arbitro.sistema@sistema.local',
                'password' => Hash::make('sistema123'), // Contraseña por defecto
                'rol' => 'participe',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Crear el partícipe genérico
            DB::table('participes')->insert([
                'id' => 1,
                'credencial_id' => $credencialId,
                'nombres' => 'Árbitro Sistema',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('Partícipe genérico "Árbitro Sistema" creado exitosamente.');
        } else {
            $this->command->info('El partícipe genérico ya existe.');
        }
    }
}
