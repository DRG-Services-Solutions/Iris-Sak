<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar la caché de Spatie Permissions (Súper importante cuando hacemos seeds)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- FASE 1: EL SUPER ADMIN (Global) ---
        
        // Creamos al Super Admin que no pertenece a ningún tenant
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'administrador@drg.mx',
            'password' => bcrypt('admin123!'), // La contraseña será 'password'
            'tenant_id' => null,
        ]);

        // Creamos el rol global 'Super Admin'. 
        // Al forzar tenant_id a null, le decimos a Spatie que este rol aplica a nivel general.
        $roleSuperAdmin = Role::create([
            'name' => 'Super Admin', 
            'tenant_id' => null
        ]);
        
        $superAdmin->assignRole($roleSuperAdmin);


        // --- FASE 2: CREAR LOS INQUILINOS (Tenants) ---
        
        $tenantA = Tenant::create(['name' => 'Coca Cola', 'is_active' => true]);
        $tenantB = Tenant::create(['name' => 'Pepsi', 'is_active' => true]);


        // --- FASE 3: USUARIOS Y ROLES POR INQUILINO ---

        // ----- INQUILINO A (Coca Cola) -----
        // Le decimos a Spatie: "Todo lo que haga de roles ahora, es para el Tenant A"
        setPermissionsTeamId($tenantA->id); 
        
        // Creamos un rol "Gerente" exclusivo para Coca Cola
        $roleGerenteA = Role::create(['name' => 'Gerente', 'tenant_id' => $tenantA->id]);
        
        $userA = User::factory()->create([
            'name' => 'Juan de Coca Cola',
            'email' => 'juan@cocacola.com',
            'password' => bcrypt('password'),
            'tenant_id' => $tenantA->id,
        ]);
        $userA->assignRole($roleGerenteA);


        // ----- INQUILINO B (Pepsi) -----
        // Cambiamos el contexto de Spatie al Tenant B
        setPermissionsTeamId($tenantB->id); 
        
        // Creamos un rol "Gerente" exclusivo para Pepsi (Nota: el nombre puede repetirse porque es otro tenant)
        $roleGerenteB = Role::create(['name' => 'Gerente', 'tenant_id' => $tenantB->id]);
        
        $userB = User::factory()->create([
            'name' => 'Maria de Pepsi',
            'email' => 'maria@pepsi.com',
            'password' => bcrypt('password'),
            'tenant_id' => $tenantB->id,
        ]);
        $userB->assignRole($roleGerenteB);
    }

    
}