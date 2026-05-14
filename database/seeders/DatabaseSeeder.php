<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Location;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(FloorLocationSeeder::class);

        // 1. Limpiar la caché de Spatie Permissions (Súper importante cuando hacemos seeds)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- FASE 1: EL SUPER ADMIN (Global) ---
        
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'administrador@drg.mx',
            'password' => bcrypt('admin123!'), 
            'tenant_id' => null,
        ]);

        $roleSuperAdmin = Role::create([
            'name' => 'Super Admin', 
            'tenant_id' => null
        ]);
        
        $superAdmin->assignRole($roleSuperAdmin);

        // --- FASE 1: EL SUPER ADMIN (Global) ---


        $supervisor = User::factory()->create([
            'name' => 'Supervisor',
            'email' => 'supervisionalmacen1@saklogistiks.com',
            'password' => bcrypt('Sak#2026!'), 
            'tenant_id' => null,
        ]);

        $roleSupervisor = Role::create([
            'name' => 'Supervisor', 
            'tenant_id' => null
        ]);
        
        $supervisor->assignRole($roleSupervisor);


        

    }  
}