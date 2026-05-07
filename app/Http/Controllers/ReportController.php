<?php

namespace App\Http\Controllers;
use App\Models\ActivityLog;
use App\Models\Box;
use Carbon\Carbon;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function storageTimeReport(Request $request)
{
    // 1. Buscamos todas las cajas que han sido despachadas
    // Usamos eager loading para traer el modelo 'user' que hizo el despacho
    $dispatchLogs = ActivityLog::with('user')
        ->where('action', 'BOX_DISPATCHED')
        ->orderBy('created_at', 'desc')
        ->get();

    // 2. Extraemos los IDs de las cajas desde el JSON
    $boxIds = $dispatchLogs->pluck('details.box_id')->filter();

    // 3. Traemos las cajas de la BD con sus relaciones (Contenedor y Artículo)
    // Esto es mucho más rápido que hacer una consulta por cada caja en el foreach
    $boxes = Box::with(['container', 'containerItem'])
        ->whereIn('id', $boxIds)
        ->get()
        ->keyBy('id'); // Indexamos por ID para búsqueda instantánea

    // 4. Armamos la data del reporte
    $reportData = $dispatchLogs->map(function ($log) use ($boxes) {
        $boxId = $log->details['box_id'] ?? null;
        $box = $boxes->get($boxId);

        if (!$box) return null; // Por si la caja fue eliminada físicamente de la BD

        // Tiempos
        $fechaIngreso = $box->closed_at ?? $box->created_at; // Cuando se cerró la tarima/contenedor
        $fechaSalida  = $log->created_at; // Cuando se registró el log BOX_DISPATCHED
        
        // Calculamos los días exactos (puedes usar diffInHours si necesitas más precisión)
        $diasAlmacenado = $fechaIngreso->diffInDays($fechaSalida);

        return (object) [
            'caja_codigo'         => $box->box_code,
            'articulo'            => $box->containerItem->product_description ?? 'N/A',
            'sku'                 => $box->containerItem->barcode ?? 'N/A',
            'contenedor'          => $box->container->container_number ?? 'N/A',
            'fecha_ingreso'       => $fechaIngreso->format('d/m/Y H:i'),
            'fecha_salida'        => $fechaSalida->format('d/m/Y H:i'),
            'dias_almacenamiento' => $diasAlmacenado,
            'despachado_por'      => $log->user->name ?? 'Sistema',
        ];
    })->filter(); // filter() quita los nulos

    return view('reports.storage-time', compact('reportData'));
}
}
