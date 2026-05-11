<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Box;
use App\Models\Container;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Reporte de trazabilidad completa: vida de cada caja desde recepción del contenedor hasta embarque.
     *
     * Hitos del timeline por caja:
     * ┌─────────────────────────────────────────────────────────────────────────────────┐
     * │ Contenedor     Caja         Caja en      Tarima     Tarima en    Maquila       │
     * │ Recibido  →  Creada   →   Tarima    →  Cerrada  →   Rack    →  (si aplica) → Embarcada │
     * └─────────────────────────────────────────────────────────────────────────────────┘
     */
    public function traceabilityReport(Request $request)
    {
        // ──────────────────────────────────────
        // 1. FILTROS
        // ──────────────────────────────────────
        $filters = $request->validate([
            'container_id'  => 'nullable|exists:containers,id',
            'status'        => 'nullable|string|in:en_almacen,embarcado,todos',
            'date_from'     => 'nullable|date',
            'date_to'       => 'nullable|date|after_or_equal:date_from',
            'search'        => 'nullable|string|max:100',
        ]);

        // ──────────────────────────────────────
        // 2. QUERY PRINCIPAL DE CAJAS
        // ──────────────────────────────────────
        $query = Box::with([
            'container',
            'containerItem',
            'pallet.location',
            'creator',
        ]);

        // Filtro por contenedor
        if (!empty($filters['container_id'])) {
            $query->where('container_id', $filters['container_id']);
        }

        // Filtro por status
        if (!empty($filters['status']) && $filters['status'] !== 'todos') {
            if ($filters['status'] === 'embarcado') {
                $query->where('status', 'embarcado');
            } else {
                // "en_almacen" = todo lo que NO está embarcado
                $query->where('status', '!=', 'embarcado');
            }
        }

        // Filtro por rango de fechas (basado en creación de la caja)
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Búsqueda por texto (código de caja, código de tarima, barcode del artículo)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('box_code', 'like', "%{$search}%")
                  ->orWhereHas('containerItem', fn($qi) => $qi->where('barcode', 'like', "%{$search}%")
                        ->orWhere('product_description', 'like', "%{$search}%"))
                  ->orWhereHas('pallet', fn($qp) => $qp->where('pallet_code', 'like', "%{$search}%"));
            });
        }

        $boxes = $query->orderBy('created_at', 'desc')->get();

        // ──────────────────────────────────────
        // 3. OBTENER LOGS DE DESPACHO EN BATCH
        //    (evita N+1: una sola query para todos los logs)
        // ──────────────────────────────────────
        $boxIds = $boxes->pluck('id')->toArray();

        $dispatchLogs = ActivityLog::where('action', 'BOX_DISPATCHED')
            ->get()
            ->filter(function ($log) use ($boxIds) {
                return in_array($log->details['box_id'] ?? null, $boxIds);
            })
            ->keyBy(fn($log) => $log->details['box_id']);

        // ──────────────────────────────────────
        // 4. ARMAR TIMELINE POR CAJA
        // ──────────────────────────────────────
        $reportData = $boxes->map(function (Box $box) use ($dispatchLogs) {
            $container = $box->container;
            $pallet    = $box->pallet;

            // --- Hitos con timestamps ---
            $hitos = [];

            // Hito 1: Recepción del contenedor
            $containerReceivedAt = $container?->received_at;
            $hitos['contenedor_recibido'] = $containerReceivedAt;

            // Hito 2: Caja creada (inspección / reempaque)
            $boxCreatedAt = $box->created_at;
            $hitos['caja_creada'] = $boxCreatedAt;

            // Hito 3: Caja asignada a tarima
            // Si tiene pallet_id, la caja fue asignada. Usamos assigned_to_pallet_at si existe,
            // si no, estimamos con el created_at de la caja (ya que la asignación es inmediata en flujo normal)
            $assignedToPalletAt = $box->assigned_to_pallet_at ?? ($box->pallet_id ? $box->created_at : null);
            $hitos['asignada_a_tarima'] = $assignedToPalletAt;

            // Hito 4: Tarima cerrada
            $palletClosedAt = $pallet?->closed_at;
            $hitos['tarima_cerrada'] = $palletClosedAt;

            // Hito 5: Tarima ubicada en rack
            // Usamos located_at si existe en el modelo, si no, estimamos con closed_at
            $palletLocatedAt = $pallet?->located_at ?? ($pallet?->location_id ? $pallet?->closed_at : null);
            $hitos['tarima_ubicada'] = $palletLocatedAt;

            // Hito 6: Maquila (si aplica)
            $maquilaStartedAt   = $pallet?->maquila_started_at;
            $maquilaCompletedAt = $pallet?->maquila_completed_at;
            $hitos['maquila_inicio']   = $maquilaStartedAt;
            $hitos['maquila_completa'] = $maquilaCompletedAt;

            // Hito 7: Despacho / Embarque
            $dispatchLog   = $dispatchLogs->get($box->id);
            $dispatchedAt  = $dispatchLog?->created_at;
            $dispatchedBy  = $dispatchLog?->user?->name ?? null;
            $hitos['embarcada'] = $dispatchedAt;

            // --- Cálculos de duración ---
            $duraciones = [];

            // Tiempo total en almacén (desde recepción de contenedor hasta embarque o hasta hoy)
            $referenceEnd = $dispatchedAt ?? now();
            $referenceStart = $containerReceivedAt ?? $boxCreatedAt;

            $duraciones['total_almacen'] = $referenceStart
                ? $this->formatDuration($referenceStart, $referenceEnd)
                : null;

            $duraciones['total_almacen_dias'] = $referenceStart
                ? $referenceStart->diffInDays($referenceEnd)
                : null;

            // Tiempo en recepción (contenedor recibido → caja creada)
            $duraciones['en_recepcion'] = ($containerReceivedAt && $boxCreatedAt)
                ? $this->formatDuration($containerReceivedAt, $boxCreatedAt)
                : null;

            // Tiempo sin tarima (caja creada → asignada a tarima)
            $duraciones['sin_tarima'] = ($boxCreatedAt && $assignedToPalletAt)
                ? $this->formatDuration($boxCreatedAt, $assignedToPalletAt)
                : null;

            // Tiempo en armado (asignada a tarima → tarima cerrada)
            $duraciones['en_armado'] = ($assignedToPalletAt && $palletClosedAt)
                ? $this->formatDuration($assignedToPalletAt, $palletClosedAt)
                : null;

            // Tiempo en almacenaje (tarima ubicada → embarque)
            $duraciones['en_rack'] = ($palletLocatedAt && $dispatchedAt)
                ? $this->formatDuration($palletLocatedAt, $dispatchedAt)
                : ($palletLocatedAt ? $this->formatDuration($palletLocatedAt, now()) . ' (en curso)' : null);

            // Tiempo en maquila
            $duraciones['en_maquila'] = ($maquilaStartedAt && $maquilaCompletedAt)
                ? $this->formatDuration($maquilaStartedAt, $maquilaCompletedAt)
                : ($maquilaStartedAt ? $this->formatDuration($maquilaStartedAt, now()) . ' (en curso)' : null);

            // --- Determinar el paso actual ---
            $pasoActual = $this->resolveCurrentStep($box, $pallet, $dispatchedAt);

            return (object) [
                // Identificación
                'box_id'              => $box->id,
                'caja_codigo'         => $box->box_code,
                'source'              => $box->source,
                'articulo'            => $box->containerItem->product_description ?? 'N/A',
                'sku'                 => $box->containerItem->barcode ?? 'N/A',
                'cantidad'            => $box->quantity,
                'contenedor_numero'   => $container->container_number ?? 'N/A',
                'contenedor_sello'    => $container->container_seal_number ?? 'N/A',
                'tarima_codigo'       => $pallet?->pallet_code ?? '—',
                'localidad'           => $pallet?->location?->code ?? '—',
                'status'              => $box->status,

                // Timeline
                'hitos'               => $hitos,
                'duraciones'          => $duraciones,

                // Estado actual
                'paso_actual'         => $pasoActual,
                'despachado_por'      => $dispatchedBy,
            ];
        });

        // ──────────────────────────────────────
        // 5. ESTADÍSTICAS RESUMEN
        // ──────────────────────────────────────
        $stats = [
            'total_cajas'          => $reportData->count(),
            'embarcadas'           => $reportData->where('status', 'embarcado')->count(),
            'en_almacen'           => $reportData->where('status', '!=', 'embarcado')->count(),
            'promedio_dias'        => round($reportData->avg('duraciones.total_almacen_dias') ?? 0, 1),
            'max_dias'             => $reportData->max('duraciones.total_almacen_dias') ?? 0,
            'sin_tarima'           => $reportData->where('tarima_codigo', '—')->count(),
        ];

        // Lista de contenedores para el dropdown del filtro
        $containers = Container::select('id', 'container_number', 'container_seal_number')
            ->orderBy('received_at', 'desc')
            ->get();

        return view('reports.traceability', compact('reportData', 'stats', 'containers', 'filters'));
    }

    /**
     * Detalle de trazabilidad de una caja individual (para modal o vista detalle).
     */
    public function boxTimeline(Box $box)
    {
        $box->load(['container', 'containerItem', 'pallet.location', 'creator']);

        // Todos los logs de esta caja
        $logs = ActivityLog::where(function ($q) use ($box) {
            $q->where('box_id', $box->id)
              ->orWhere('details->box_id', $box->id);
        })
        ->with('user')
        ->orderBy('created_at', 'asc')
        ->get();

        return view('reports.box-timeline', compact('box', 'logs'));
    }

    // ──────────────────────────────────────
    // HELPERS PRIVADOS
    // ──────────────────────────────────────

    /**
     * Formatea la diferencia entre dos fechas de forma legible.
     * Ejemplo: "3d 4h 25m" o "2h 15m" o "45m"
     */
    private function formatDuration(Carbon $start, Carbon $end): string
    {
        $diff = $start->diff($end);

        $parts = [];

        if ($diff->days > 0) {
            $parts[] = $diff->days . 'd';
        }
        if ($diff->h > 0) {
            $parts[] = $diff->h . 'h';
        }
        if ($diff->i > 0 || empty($parts)) {
            $parts[] = $diff->i . 'm';
        }

        return implode(' ', $parts);
    }

    /**
     * Determina en qué paso del proceso se encuentra actualmente una caja.
     */
    private function resolveCurrentStep(Box $box, ?object $pallet, ?Carbon $dispatchedAt): string
    {
        if ($dispatchedAt || $box->status === 'embarcado') {
            return 'embarcada';
        }

        if ($pallet?->maquila_started_at && !$pallet?->maquila_completed_at) {
            return 'en_maquila';
        }

        if ($pallet?->location_id) {
            return 'en_rack';
        }

        if ($pallet?->status === 'cerrada') {
            return 'tarima_cerrada_sin_ubicar';
        }

        if ($box->pallet_id) {
            return 'en_tarima_abierta';
        }

        if ($box->status === 'cerrada') {
            return 'disponible_sin_tarima';
        }

        return 'en_recepcion';
    }
}