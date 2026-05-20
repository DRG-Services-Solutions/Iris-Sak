{{-- resources/views/users/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-br from-violet-500 to-fuchsia-600 p-3 rounded-lg shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    Editar Usuario
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Actualiza la información y accesos de esta cuenta</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 flex items-center p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                    <svg class="w-5 h-5 text-emerald-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 flex items-center p-4 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                    <svg class="w-5 h-5 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Tarjeta de info del usuario --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-violet-600 to-fuchsia-700 px-6 py-5">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 h-14 w-14 bg-white/15 rounded-full flex items-center justify-center ring-2 ring-white/30">
                            <span class="text-white font-bold text-xl">{{ substr($user->name, 0, 2) }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ $user->name }}</h3>
                            <p class="text-sm text-violet-200">{{ $user->email }}</p>
                        </div>
                        <div class="ml-auto hidden sm:flex items-center space-x-3">
                            @if($currentRole ?? false)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white border border-white/30">
                                    {{ $currentRole }}
                                </span>
                            @endif
                            {{-- Status badge --}}
                            @if($user->is_active)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-400/30 text-white border border-emerald-300/40">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-500/30 text-white border border-red-300/40">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-300"></span>
                                    Inactivo
                                </span>
                            @endif
                            <span class="text-xs text-violet-200">Creado: {{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formulario de edición --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-xl border border-gray-200 dark:border-gray-700">
                
                <div class="h-2 bg-gradient-to-r from-violet-500 via-fuchsia-500 to-violet-700"></div>

                <div class="p-8">
                    <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nombre Completo --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nombre Completo</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition duration-200">
                                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            {{-- Correo Electrónico --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correo Electrónico</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition duration-200">
                                @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            {{-- Selección de Empresa (Solo Super Admin) --}}
                            @role('Super Admin')
                                <div class="md:col-span-2">
                                    <label for="tenant_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Asignar a Empresa / Cliente</label>
                                    <select name="tenant_id" id="tenant_id"
                                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition duration-200">
                                        <option value="">Acceso Global (Super Admin)</option>
                                        @foreach($tenants as $tenant)
                                            <option value="{{ $tenant->id }}" {{ old('tenant_id', $user->tenant_id) == $tenant->id ? 'selected' : '' }}>
                                                {{ $tenant->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tenant_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                            @endrole

                            {{-- Selector de Rol --}}
                            @if(count($roles ?? []) > 0)
                                <div class="md:col-span-2">
                                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rol de Acceso</label>
                                    <select name="role" id="role"
                                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition duration-200">
                                        <option value="" disabled>Selecciona un rol...</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ old('role', $currentRole) == $role->name ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            {{-- Contraseña (Opcional) --}}
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nueva Contraseña</label>
                                <input type="password" name="password" id="password"
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition duration-200"
                                       placeholder="Déjalo en blanco para no cambiarla">
                                @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            {{-- Confirmar Contraseña --}}
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition duration-200">
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end space-x-4">
                            <a href="{{ route('users.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-violet-600 to-fuchsia-600 border border-transparent rounded-lg font-bold text-sm text-white shadow-lg hover:from-violet-700 hover:to-fuchsia-700 transform hover:-translate-y-0.5 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Actualizar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Zona de peligro: Eliminar / Desactivar usuario --}}
            @if(auth()->id() !== $user->id)
                <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-xl border border-red-200 dark:border-red-800/50">
                    <div class="p-6 space-y-4">

                        {{-- Toggle Activo/Inactivo --}}
                        <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <h3 class="text-sm font-bold {{ $user->is_active ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    {{ $user->is_active ? 'Desactivar Usuario' : 'Activar Usuario' }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $user->is_active
                                        ? 'El usuario no podrá iniciar sesión mientras esté inactivo.'
                                        : 'Permitirá que el usuario vuelva a iniciar sesión en el sistema.' }}
                                </p>
                            </div>
                            <form action="{{ route('users.toggle-status', $user) }}" method="POST"
                                  onsubmit="return confirm('¿{{ $user->is_active ? 'Desactivar' : 'Activar' }} a {{ $user->name }}?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 text-white text-xs font-bold rounded-lg shadow-sm transition-all duration-200 transform hover:-translate-y-0.5
                                               {{ $user->is_active
                                                    ? 'bg-amber-500 hover:bg-amber-600'
                                                    : 'bg-emerald-600 hover:bg-emerald-700' }}">
                                    @if($user->is_active)
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                        Desactivar
                                    @else
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>
                                        Activar
                                    @endif
                                </button>
                            </form>
                        </div>

                        {{-- Eliminar usuario --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-red-600 dark:text-red-400">Zona de Peligro</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Eliminar esta cuenta de forma permanente. Esta acción no se puede deshacer.</p>
                            </div>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all duration-200 transform hover:-translate-y-0.5">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Eliminar Usuario
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>