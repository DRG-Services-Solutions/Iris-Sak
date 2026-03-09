<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar la caché de Spatie antes de empezar (Buena práctica)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Definir nuestra estructura de permisos (Módulo => Permisos)
        $modulos = [
            'Gestión de Empleados' => [
                ['name' => 'view-users', 'display_name' => 'Ver Usuarios'],
                ['name' => 'create-users', 'display_name' => 'Dar de alta Usuarios'],
                ['name' => 'edit-users', 'display_name' => 'Editar información Usuarios'],
                ['name' => 'delete-users', 'display_name' => 'Dar de baja Usuarios'],
            ],
            
            'Catálogo e Inventario' => [
                ['name' => 'view-products', 'display_name' => 'Ver catálogo de Productos'],
                ['name' => 'create-products', 'display_name' => 'Agregar nuevos Productos'],
                ['name' => 'edit-products', 'display_name' => 'Modificar Productos'],
                ['name' => 'delete-products', 'display_name' => 'Eliminar Productos'],
            ],

            'Ventas y Reportes' => [
                ['name' => 'create-sales', 'display_name' => 'Registrar Nueva Venta'],
                ['name' => 'view-reports', 'display_name' => 'Ver Reportes'],
                ['name' => 'export-data', 'display_name' => 'Exportar datos a Excel/PDF'],
            ],
        ];

        foreach ($modulos as $nombreModulo => $permisos) {
            foreach ($permisos as $permiso) {
                Permission::firstOrCreate(
                    ['name' => $permiso['name']], 
                    [
                        'display_name' => $permiso['display_name'], 
                        'module' => $nombreModulo 
                    ]
                );
            }
        }
    }
}