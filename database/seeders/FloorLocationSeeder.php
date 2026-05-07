<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class FloorLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usamos firstOrCreate para evitar duplicados si el seeder se ejecuta múltiples veces
        Location::firstOrCreate(
            ['code' => 'PISO'], // Condición de búsqueda (si ya existe el código PISO, no hace nada)
            [
                'name'      => 'Almacenamiento en Piso (Bulk)',
                'zone'      => 'GENERAL', // Ajusta esto si tienes zonas específicas como 'A', 'B', etc.
                // 'is_active' => true,   // Descomenta si tu tabla tiene este campo
                // 'max_capacity' => 9999 // Descomenta si tu tabla requiere un número máximo por defecto
            ]
        );

        $this->command->info('Ubicación PISO creada exitosamente.');
    }
}