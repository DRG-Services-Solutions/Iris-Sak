<?php

namespace App\Http\Controllers;

use App\Models\PrintJob;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StorePrintJobRequest;
use App\Http\Requests\UpdatePrintJobRequest;
use Illuminate\Http\Request;

class PrintJobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePrintJobRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PrintJob $printJob)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PrintJob $printJob)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePrintJobRequest $request, PrintJob $printJob)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrintJob $printJob)
    {
        //
    }

    public function getPending(): JsonResponse
    {
        $pendingJobs = PrintJob::where('status', 'pending')
                                ->orderBy('created_at', 'asc')
                                ->get(['id', 'zpl_data', 'printer_ip']);

        return response()->json([
            'success' => true,
            'data' => $pendingJobs
        ]);
    }

    public function markAsComplete(Request $request, PrintJob $printJob): JsonResponse
    {
        // Si tienes la Policy configurada, puedes autorizar aquí:
        // $this->authorize('update', $printJob);

        $printJob->update([
            'status' => 'printed',
            'printed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "PrintJob {$printJob->id} marcado como completado."
        ]);
    }

    public function markAsFailed(Request $request, PrintJob $printJob): JsonResponse
    {
        $printJob->update([
            'status' => 'failed',
        ]);

        return response()->json([
            'success' => true,
            'message' => "PrintJob {$printJob->id} reportó un error."
        ]);
    }
}
