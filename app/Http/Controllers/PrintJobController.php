<?php

namespace App\Http\Controllers;

use App\Models\PrintJob;
use App\Http\Requests\StorePrintJobRequest;
use App\Http\Requests\UpdatePrintJobRequest;

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
}
