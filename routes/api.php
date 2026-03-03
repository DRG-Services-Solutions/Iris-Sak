<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintJobController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

 Route::get('/print-jobs/pending', [PrintJobController::class, 'getPending']);
    Route::post('/print-jobs/{printJob}/complete', [PrintJobController::class, 'markAsComplete']);
    Route::post('/print-jobs/{printJob}/fail', [PrintJobController::class, 'markAsFailed']);
