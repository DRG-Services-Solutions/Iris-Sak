<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tenant;
use Spatie\Permission\Models\Role;
// use App\Models\Product; // Descomenta esto cuando tengas tu modelo Product

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Variables por defecto
        $totalTenants = 0;
        $totalUsers = 0;
        $totalRoles = 0;
        $totalProducts = 0; // Cambiaremos esto cuando hagamos el CRUD de productos

        // 1. LÓGICA PARA EL SUPER ADMIN
        if ($user->hasRole('Super Admin')) {
            $totalTenants = Tenant::count();
            $totalUsers = User::count(); // Todos los usuarios del sistema
            $totalRoles = Role::count();
        } 
        // 2. LÓGICA PARA EL CLIENTE (EJ: COCA COLA)
        else {
            $tenantId = $user->tenant_id;
            
            $totalUsers = User::where('tenant_id', $tenantId)->count();
            $totalRoles = Role::where('tenant_id', $tenantId)->count();
            
            // Cuando tengas el modelo Product, será algo como:
            // $totalProducts = Product::where('tenant_id', $tenantId)->count();
        }

        // Datos simulados para los gráficos y tablas (los conectaremos a la BD después)
        $entradasMes = 340; 
        $salidasMes = 128;
        $alertasStock = 5;

        return view('dashboard', compact(
            'totalTenants', 
            'totalUsers', 
            'totalRoles', 
            'totalProducts',
            'entradasMes',
            'salidasMes',
            'alertasStock'
        ));
    }
}