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
        $operationsRole =Role::firstOrCreate(['name' => 'lider_etiquetado']);
        $operationsRole2 = Role::firstOrCreate(['name' => 'especialista_etiquetado']);

        $permissions = [
            'view-dashboard',
            'manage-users',
            'manage-products',
            'create-orders',
            'view-orders',
            'manage-wharehouse',
        ];

        foreach ($permissions as $perm)
        {
            Permission::firstOrCreate(['name' => $perm]);
        }

        //Asignamos todos los permisos al administrador y demas permisos a los demas roles
        $adminRole->syncPermissions($permissions);
        $wharehouseAdminRole->syncPermissions($permissions);
        $operationsRole->syncPermissions($permissions);
        $operationsRole2->syncPermissions($permissions);

        

        $admin = User::create([
            'name' => 'Administrador DRG',
            'email' => 'admin@drg.mx',
            'password' => 'admin123', 
        ]);

       

        $admin->assignRole($adminRole);
      
    }
}