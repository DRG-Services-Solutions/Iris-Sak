<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('maquila.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"><i class="fas fa-arrow-left text-lg"></i></a>
            <div class="bg-gradient-to-br from-pink-600 to-rose-700 p-3 rounded-lg shadow-lg"><i class="fas fa-history text-white text-xl"></i></div>
            <div><h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Historial de Maquila</h2></div>
        </div>
    </x-slot>
    <div class="py-8"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden"><div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tarima</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ubicación</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Desde</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase"></th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Hacia</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Usuario</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Notas</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-gray-900 dark:text-white">{{ $log->pallet->pallet_code }}</td>
                            <td class="px-4 py-3 text-gray-600 text-xs">{{ $log->pallet->location?->code ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($log->from_station)
                                    @php $fb = match($log->from_station) { 1=>'bg-amber-100 text-amber-700', 2=>'bg-blue-100 text-blue-700', 3=>'bg-purple-100 text-purple-700', default=>'bg-gray-100 text-gray-700' }; @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $fb }}">E{{ $log->from_station }}</span>
                                @else<span class="text-xs text-gray-400">—</span>@endif
                            </td>
                            <td class="px-4 py-3 text-center text-pink-500"><i class="fas fa-arrow-right"></i></td>
                            <td class="px-4 py-3 text-center">
                                @if($log->to_station)
                                    @php $tb = match($log->to_station) { 1=>'bg-amber-100 text-amber-700', 2=>'bg-blue-100 text-blue-700', 3=>'bg-purple-100 text-purple-700', default=>'bg-gray-100 text-gray-700' }; @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $tb }}">E{{ $log->to_station }}</span>
                                @else<span class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700"><i class="fas fa-check mr-0.5"></i> Hecho</span>@endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-xs">{{ $log->changedBy->name }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $log->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-12 text-center text-gray-500"><i class="fas fa-history text-4xl text-gray-300 mb-3 block"></i>Sin registros</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())<div class="px-6 py-4 border-t">{{ $logs->links() }}</div>@endif
        </div>
    </div></div>
</x-app-layout>
