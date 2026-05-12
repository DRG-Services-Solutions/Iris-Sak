<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Trazabilidad</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #b91c1c; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f3f4f6; color: #374151; text-align: left; padding: 8px; border-bottom: 2px solid #d1d5db; }
        td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .group-header { background-color: #fdf2f8; font-weight: bold; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Trazabilidad y Lead Time</h1>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @php
        $groupedByContainer = $reportData->groupBy('contenedor_sello');
    @endphp

    <table>
        <thead>
            <tr>
                <th>Caja</th>
                <th>SKU</th>
                <th>Descripción</th>
                <th>Proceso Actual</th>
                <th class="text-right">Días en Almacén</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedByContainer as $contenedorSello => $boxesInContainer)
                {{-- Fila del Contenedor --}}
                <tr class="group-header">
                    <td colspan="4">Contenedor: {{ $contenedorSello ?: 'Sin Contenedor' }} ({{ $boxesInContainer->count() }} cajas)</td>
                    <td class="text-right">Max: {{ round($boxesInContainer->max(fn($b) => $b->duraciones['total_almacen_dias'] ?? 0), 2) }} días</td>
                </tr>

                {{-- Cajas del contenedor --}}
                @foreach($boxesInContainer->sortByDesc('duraciones.total_almacen_dias') as $row)
                    <tr>
                        <td>{{ $row->caja_codigo }}</td>
                        <td>{{ $row->sku }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($row->articulo, 40) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $row->paso_actual)) }}</td>
                        <td class="text-right">{{ round($row->duraciones['total_almacen_dias'] ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>