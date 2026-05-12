<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Box;
use App\Models\Container;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Vista Web del Reporte
     */
    public function traceabilityReport(Request $request)
    {
        $data = $this->prepareTraceabilityReportData($request);
        
        return view('reports.traceability', [
            'reportData' => $data['reportData'],
            'stats'      => $data['stats'],
            'containers' => $data['containers'],
            'filters'    => $data['filters']
        ]);
    }

    /**
     * Generación del PDF
     */
    public function exportStorageTimePdf(Request $request)
    {
        $data = $this->prepareTraceabilityReportData($request);

        // Generar el PDF usando la vista especial para impresión
        $pdf = Pdf::loadView('reports.pdf.storage-time', [
            'reportData' => $data['reportData'],
            'stats'      => $data['stats'],
            'filters'    => $data['filters']
        ]);

        // Configuración de página
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Reporte_Trazabilidad_' . now()->format('Y-m-d_H-i') . '.pdf');
    }

    /**
     * Lógica centralizada para obtener los datos (DRY - Don't Repeat Yourself)
     */
    private function prepareTraceabilityReportData(Request $request)
    {
        // 1. FILTROS
        $filters = $request->validate([
            'container_id'  => 'nullable|exists:containers,id',
            'status'        => 'nullable|string|in:en_almacen,embarcado,todos',
            'date_from'     => 'nullable|date',
            'date_to'       => 'nullable|date|after_or_equal:date_from',
            'search'        => 'nullable|string|max:100',
        ]);

        // 2. QUERY PRINCIPAL
        $query = Box::with([
            'container',
            'containerItem',
            'pallet.location',
            'creator',
        ]);

        if (!empty($filters['container_id'])) {
            $query->where('container_id', $filters['container_id']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'todos') {
            if ($filters['status'] === 'embarcado') {
                $query->where('status', 'embarcado');
            } else {
                $query->where('status', '!=', 'embarcado');
            }
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

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

        // 3. LOGS DE DESPACHO
        $boxIds = $boxes->pluck('id')->toArray();
        $dispatchLogs = ActivityLog::where('action', 'BOX_DISPATCHED')
            ->get()
            ->filter(function ($log) use ($boxIds) {
                return in_array($log->details['box_id'] ?? null, $boxIds);
            })
            ->keyBy(fn($log) => $log->details['box_id']);

        // 4. ARMAR DATA
        $reportData = $boxes->map(function (Box $box) use ($dispatchLogs) {
            $container = $box->container;
            $pallet    = $box->pallet;
            $hitos     = [];

            // Tiempos clave
            $containerReceivedAt = $container?->received_at;
            $boxCreatedAt        = $box->created_at;
            $assignedToPalletAt  = $box->assigned_to_pallet_at ?? ($box->pallet_id ? $box->created_at : null);
            $palletClosedAt      = $pallet?->closed_at;
            $palletLocatedAt     = $pallet?->located_at ?? ($pallet?->location_id ? $pallet?->closed_at : null);
            $maquilaStartedAt    = $pallet?->maquila_started_at;
            $maquilaCompletedAt  = $pallet?->maquila_completed_at;

            $dispatchLog         = $dispatchLogs->get($box->id);
            $dispatchedAt        = $dispatchLog?->created_at;
            $dispatchedBy        = $dispatchLog?->user?->name ?? null;

            // Hitos para timeline
            $hitos = [
                'contenedor_recibido' => $containerReceivedAt,
                'caja_creada'         => $boxCreatedAt,
                'asignada_a_tarima'   => $assignedToPalletAt,
                'tarima_cerrada'      => $palletClosedAt,
                'tarima_ubicada'      => $palletLocatedAt,
                'maquila_inicio'      => $maquilaStartedAt,
                'maquila_completa'    => $maquilaCompletedAt,
                'embarcada'           => $dispatchedAt,
            ];

            // Cálculos de duración
            $referenceEnd   = $dispatchedAt ?? now();
            $referenceStart = $containerReceivedAt ?? $boxCreatedAt;

            $duraciones = [
                'total_almacen'      => $referenceStart ? $this->formatDuration($referenceStart, $referenceEnd) : null,
                'total_almacen_dias' => $referenceStart ? round($referenceStart->diffInMinutes($referenceEnd) / 1440, 2) : null,
                'en_recepcion'       => ($containerReceivedAt && $boxCreatedAt) ? $this->formatDuration($containerReceivedAt, $boxCreatedAt) : null,
                'sin_tarima'         => ($boxCreatedAt && $assignedToPalletAt) ? $this->formatDuration($boxCreatedAt, $assignedToPalletAt) : null,
                'en_armado'          => ($assignedToPalletAt && $palletClosedAt) ? $this->formatDuration($assignedToPalletAt, $palletClosedAt) : null,
                'en_rack'            => ($palletLocatedAt) ? $this->formatDuration($palletLocatedAt, $dispatchedAt ?? now()) : null,
                'en_maquila'         => ($maquilaStartedAt) ? $this->formatDuration($maquilaStartedAt, $maquilaCompletedAt ?? now()) : null,
            ];

            return (object) [
                'box_id'            => $box->id,
                'caja_codigo'       => $box->box_code,
                'source'            => $box->source,
                'articulo'          => $box->containerItem->product_description ?? 'N/A',
                'sku'               => $box->containerItem->barcode ?? 'N/A',
                'cantidad'          => $box->quantity,
                'contenedor_sello'  => $container->container_seal_number ?? 'N/A',
                'tarima_codigo'     => $pallet?->pallet_code ?? '—',
                'localidad'         => $pallet?->location?->code ?? '—',
                'status'            => $box->status,
                'hitos'             => $hitos,
                'duraciones'        => $duraciones,
                'paso_actual'       => $this->resolveCurrentStep($box, $pallet, $dispatchedAt),
                'despachado_por'    => $dispatchedBy,
            ];
        });

        // 5. STATS Y LISTAS
        $stats = [
            'total_cajas'   => $reportData->count(),
            'embarcadas'    => $reportData->where('status', 'embarcado')->count(),
            'en_almacen'    => $reportData->where('status', '!=', 'embarcado')->count(),
            'promedio_dias' => round($reportData->avg('duraciones.total_almacen_dias') ?? 0, 2),
            'max_dias'      => round($reportData->max('duraciones.total_almacen_dias') ?? 0, 2),
            'sin_tarima'    => $reportData->where('tarima_codigo', '—')->count(),
        ];

        $containers = Container::select('id', 'container_number', 'container_seal_number')
            ->orderBy('received_at', 'desc')
            ->get();

        return compact('reportData', 'stats', 'containers', 'filters');
    }

    private function formatDuration(Carbon $start, Carbon $end): string
    {
        $diff = $start->diff($end);
        $parts = [];
        if ($diff->days > 0) $parts[] = $diff->days . 'd';
        if ($diff->h > 0)    $parts[] = $diff->h . 'h';
        if ($diff->i > 0 || empty($parts)) $parts[] = $diff->i . 'm';

        return implode(' ', $parts);
    }

    private function resolveCurrentStep(Box $box, ?object $pallet, ?Carbon $dispatchedAt): string
    {
        if ($dispatchedAt || $box->status === 'embarcado') return 'embarcada';
        if ($pallet?->maquila_started_at && !$pallet?->maquila_completed_at) return 'en_maquila';
        if ($pallet?->location_id) return 'en_rack';
        if ($pallet?->status === 'cerrada') return 'tarima_cerrada_sin_ubicar';
        if ($box->pallet_id) return 'en_tarima_abierta';
        if ($box->status === 'cerrada') return 'disponible_sin_tarima';
        return 'en_recepcion';
    }

    public function currentInventory(Request $request)
    {
        $boxes = Box::with(['container', 'pallet.location', 'containerItem'])
                    ->where('status', '!=', 'embarcado')
                    ->orderBy('created_at', 'desc')
                    ->get();

        $stats = [
            'total_cajas'        => $boxes->count(),
            'total_piezas'       => $boxes->sum('quantity'),
            'total_tarimas'      => $boxes->pluck('pallet_id')->filter()->unique()->count(),
            'total_contenedores' => $boxes->pluck('container_id')->filter()->unique()->count(),
        ];

        return view('inventory.index', compact('boxes', 'stats'));
    }



    
}