<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Crea los roles globales del sistema (tenant_id = null).
     * 
     * Roles creados:
     *  - Super Admin  → Acceso total a la plataforma (gestión de clientes/tenants)
     *  - Director     → Visibilidad total del tenant; aprueba procesos clave
     *  - Gerente      → Administración operativa; gestión de usuarios del tenant
     *  - Supervisor   → Supervisión de operaciones de almacén
     *  - Calidad      → Acceso a inspección, etiquetado y reportes de calidad
     */
    public function run(): void
    {
        // Limpiar caché de permisos antes de ejecutar
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'Super Admin',
            'Director',
            'Gerente',
            'Supervisor',
            'Calidad',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name'      => $roleName,
                'guard_name' => 'web',
                'tenant_id' => null,   // Roles globales (sin tenant específico)
            ]);

            $this->command->info("✔  Rol '{$roleName}' creado / verificado.");
        }

        // -- Permisos especiales que el Super Admin siempre debe tener --
        // (Los permisos de módulos se gestionan desde PermissionSeeder / RoleController)
        $superAdmin = Role::where('name', 'Super Admin')->where('tenant_id', null)->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
            $this->command->info("✔  Super Admin sincronizado con todos los permisos.");
        }
    }
}
