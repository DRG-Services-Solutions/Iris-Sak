<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Creacion de roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'almacenista']);
        $wharehouseAdminRole = Role::firstOrCreate(['name' => 'warehouse_manager']);

        $permissions = [
            'view-dashboard',
            'manage-users',
            'manage-products',
            'create-orders',
            'view-orders',
            'manage-wharehouse',
        ];

        //Creamos todos los permisos con un ciclo foreach
        foreach ($permissions as $perm)
        {
            Permission::firstOrCreate(['name' => $perm]);
        }

        //Asignamos todos los permisos al administrador
        $adminRole->syncPermissions($permissions);
        $wharehouseAdminRole->syncPermissions($permissions);

        $admin = User::create([
            'name' => 'Administrador DRG',
            'email' => 'admin@drg.mx',
            'password' => 'admin123', 
        ]);

        $wharehouseAdmin = User::create([
            'name' => 'Almacenista DRG',
            'email' => 'warehouse@drg.mx',
            'password' => 'warehouse123', 
        ]);

        $admin->assignRole($adminRole);
        $wharehouseAdmin->assignRole($wharehouseAdminRole);
    }
}