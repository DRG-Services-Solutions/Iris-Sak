<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('dispatch.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"><i class="fas fa-arrow-left text-lg"></i></a>
            <div class="bg-gradient-to-br from-green-600 to-green-800 p-3 rounded-lg shadow-lg"><i class="fas fa-truck text-white text-xl"></i></div>
            <div><h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Nuevo Despacho</h2></div>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))<div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">{{ session('error') }}</div>@endif
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <form method="POST" action="{{ route('dispatch.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Orden de Surtido *</label>
                        <select name="picking_order_id" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100">
                            <option value="">Seleccionar...</option>
                            @foreach($completedOrders as $o)
                                <option value="{{ $o->id }}" {{ ($selectedOrder?->id ?? old('picking_order_id')) == $o->id ? 'selected' : '' }}>{{ $o->order_number }} — {{ $o->client_name }} ({{ $o->items->count() }} tarimas)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Tipo de transporte *</label>
                            <select name="transport_type" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100">
                                <option value="5ta_rueda">5ta Rueda</option><option value="torton">Tortón</option><option value="camioneta">Camioneta</option><option value="otro">Otro</option>
                            </select>
                        </div>
                        <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Destino *</label><input type="text" name="destination" required value="{{ $selectedOrder?->destination ?? old('destination') }}" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100" placeholder="Ciudad/dirección"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Operador</label><input type="text" name="driver_name" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100" placeholder="Nombre del operador"></div>
                        <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Placas</label><input type="text" name="plates" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100" placeholder="ABC-123"></div>
                    </div>
                    <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Notas</label><textarea name="notes" rows="2" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100"></textarea></div>
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('dispatch.index') }}" class="px-5 py-2.5 text-gray-600 text-sm">Cancelar</a>
                        <button type="submit" class="px-6 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium shadow-lg"><i class="fas fa-save mr-1"></i> Crear Despacho</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
