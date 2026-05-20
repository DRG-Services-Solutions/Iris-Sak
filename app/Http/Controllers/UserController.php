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
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');

        $query = User::with(['roles', 'tenant']);

        if (!$user->hasRole('Super Admin')) {
            $query->where('tenant_id', $user->tenant_id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('users.index', compact('users', 'search'));
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
        $currentUser = auth()->user();

        // Los admins de tenant solo pueden editar usuarios de su propio tenant
        if (!$currentUser->hasRole('Super Admin') && $user->tenant_id !== $currentUser->tenant_id) {
            abort(403, 'No tienes permiso para editar este usuario.');
        }

        if ($currentUser->hasRole('Super Admin')) {
            $tenants = Tenant::where('is_active', true)->get();
            $roles = Role::whereNull('tenant_id')->orWhere('tenant_id', $user->tenant_id)->get();
        } else {
            $tenants = [];
            $roles = Role::where('tenant_id', $currentUser->tenant_id)->get();
        }

        // Obtener el rol actual del usuario
        $currentRole = $user->roles->first()?->name;
        
        return view('users.edit', compact('user', 'tenants', 'roles', 'currentRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $currentUser = auth()->user();

        // Los admins de tenant solo pueden actualizar usuarios de su propio tenant
        if (!$currentUser->hasRole('Super Admin') && $user->tenant_id !== $currentUser->tenant_id) {
            abort(403, 'No tienes permiso para actualizar este usuario.');
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Solo Super Admin puede cambiar el tenant
        if ($currentUser->hasRole('Super Admin') && $request->has('tenant_id')) {
            $data['tenant_id'] = $request->tenant_id;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Sincronizar rol si fue enviado
        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        }

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

    /**
     * Toggle the active/inactive status of a user.
     */
    public function toggleStatus(User $user)
    {
        $currentUser = auth()->user();

        // Prevent self-deactivation
        if ($currentUser->id === $user->id) {
            return redirect()->route('users.index')
                             ->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        // Tenant admins can only toggle users of their own tenant
        if (!$currentUser->hasRole('Super Admin') && $user->tenant_id !== $currentUser->tenant_id) {
            abort(403, 'No tienes permiso para modificar este usuario.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $statusLabel = $user->is_active ? 'activado' : 'desactivado';

        return redirect()->route('users.index')
                         ->with('success', "Usuario {$statusLabel} exitosamente.");
    }
}
