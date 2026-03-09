<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->hasRole('Super Admin')) {
            $roles = Role::with('permissions')->paginate(10);
        }
        else {
            $roles = Role::where('tenant_id', $user->tenant_id)
                         ->with('permissions')
                         ->paginate(10);
        }
        
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('module');
        
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->name,
        ]);

        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')
                         ->with('success', '¡Rol creado exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $this->authorizeTenant($role);
        $permissions = Permission::all()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $this->authorizeTenant($role); 

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')
                         ->with('success', '¡Rol actualizado exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $this->authorizeTenant($role); // Candado de seguridad

        $role->delete();

        return redirect()->route('roles.index')
                         ->with('success', '¡Rol eliminado exitosamente!');
    }

    private function authorizeTenant(Role $role)
    {
        $user = auth()->user();
        
        // Si no es Super Admin Y el rol no pertenece a su empresa, ¡bloquear!
        if (!$user->hasRole('Super Admin') && $role->tenant_id !== $user->tenant_id) {
            abort(403, 'Acceso denegado: No tienes permiso para gestionar este rol.');
        }
    }
}
