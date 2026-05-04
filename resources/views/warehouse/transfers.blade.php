<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('warehouse.locations') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"><i class="fas fa-arrow-left text-lg"></i></a>
            <div class="bg-gradient-to-br from-amber-500 to-amber-700 p-3 rounded-lg shadow-lg"><i class="fas fa-history text-white text-xl"></i></div>
            <div><h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Historial de Transferencias</h2></div>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tarima</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Origen</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase"></th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Destino</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Usuario</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($transfers as $t)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 text-gray-500">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 font-mono font-bold text-gray-900 dark:text-white">{{ $t->pallet->pallet_code }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $t->fromLocation?->code ?? 'Sin asignar' }}</td>
                                    <td class="px-4 py-3 text-center text-amber-500"><i class="fas fa-arrow-right"></i></td>
                                    <td class="px-4 py-3 font-medium text-emerald-600">{{ $t->toLocation->code }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $t->transferredBy->name }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No hay transferencias registradas</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transfers->hasPages())<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">{{ $transfers->links() }}</div>@endif
            </div>
        </div>
    </div>
</x-app-layout>
