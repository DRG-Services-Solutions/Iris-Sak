<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('picking.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"><i class="fas fa-arrow-left text-lg"></i></a>
            <div class="bg-gradient-to-br from-orange-500 to-orange-700 p-3 rounded-lg shadow-lg"><i class="fas fa-plus text-white text-xl"></i></div>
            <div><h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Nueva Orden de Surtido</h2></div>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>@endif

            <form method="POST" action="{{ route('picking.store') }}" class="space-y-6">
                @csrf
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white"><i class="fas fa-info-circle text-orange-500 mr-2"></i>Datos de la Orden</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Cliente *</label><input type="text" name="client_name" required value="{{ old('client_name') }}" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100" placeholder="Nombre del cliente"></div>
                        <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Destino *</label><input type="text" name="destination" required value="{{ old('destination') }}" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100" placeholder="Ciudad o dirección"></div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Prioridad</label>
                            <select name="priority" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100">
                                <option value="normal">Normal</option><option value="urgente">Urgente</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Asignar a</label>
                            <select name="assigned_to" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100">
                                <option value="">Sin asignar</option>
                                @foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Notas</label><textarea name="notes" rows="2" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100" placeholder="Observaciones...">{{ old('notes') }}</textarea></div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4"><i class="fas fa-pallet text-orange-500 mr-2"></i>Seleccionar Tarimas a Surtir</h3>
                    @if($availablePallets->count())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-96 overflow-y-auto">
                            @foreach($availablePallets as $p)
                                <label class="flex items-start p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:border-orange-400 transition">
                                    <input type="checkbox" name="pallet_ids[]" value="{{ $p->id }}" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 mt-1 mr-3">
                                    <div class="text-sm">
                                        <p class="font-mono font-bold text-gray-900 dark:text-white">{{ $p->pallet_code }}</p>
                                        <p class="text-xs text-gray-500">{{ $p->container->container_number }} · {{ $p->location?->code ?? 'Sin loc.' }} · {{ $p->boxes->count() }} cajas · {{ $p->boxes->sum('quantity') }} pzas</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 text-center py-6">No hay tarimas disponibles para surtir. Deben estar cerradas y con localidad asignada.</p>
                    @endif
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('picking.index') }}" class="px-5 py-2.5 text-gray-600 dark:text-gray-300 text-sm">Cancelar</a>
                    <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-500 text-sm font-medium shadow-lg"><i class="fas fa-save mr-1"></i> Crear Orden</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
