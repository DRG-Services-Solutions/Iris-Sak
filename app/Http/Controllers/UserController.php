<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;    
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('Super Admin')) {
            // ¡Asegúrate de tener ->with('roles') aquí!
            $users = User::with('roles')->paginate(10); 
        } else {
            // ¡Y aquí también!
            $users = User::where('tenant_id', $user->tenant_id)
                         ->with('roles')
                         ->paginate(10);
        }

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if ($user->hasRole('Super Admin')) {
            $tenants = Tenant::where('is_active', true)
                        ->doesntHave('users') 
                        ->get();
            
            $roles = []; 
        } 
        else {
            $tenants = [];
            $roles = Role::where('tenant_id', $user->tenant_id)->get();
        }
        
        return view('users.create', compact('tenants', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $currentUser = auth()->user();

        if ($currentUser->hasRole('Super Admin')) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tenant_id' => $request->tenant_id,
            ]);

            setPermissionsTeamId($request->tenant_id);

            $adminRole = Role::firstOrCreate([
                'name' => 'Administrador',
                'tenant_id' => $request->tenant_id
            ]);

            $adminRole->syncPermissions(Permission::all());

            $user->assignRole($adminRole);
        } 
        else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tenant_id' => $currentUser->tenant_id,
            ]);

            // Le asigna el rol que el cliente eligió en la vista
            if ($request->filled('role')) {
                $user->assignRole($request->role);
            }
        }

        return redirect()->route('users.index')->with('success', '¡Usuario creado exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $tenants = Tenant::where('is_active', true)->get();
        
        return view('users.edit', compact('user', 'tenants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'tenant_id' => $request->tenant_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
                         ->with('success', '¡Usuario actualizado exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')
                             ->with('error', 'Por seguridad, no puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->route('users.index')
                         ->with('success', '¡Usuario eliminado exitosamente!');
    }
}
