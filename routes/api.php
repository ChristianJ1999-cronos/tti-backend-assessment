<?php

use App\Http\Controllers\InstrumentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;

Route::post('/patients', [PatientController::class, 'store']);
Route::post('/instruments', [InstrumentController::class, 'store']);
Route::post('/patients/{patientId}/submissions', [PatientController::class, 'storeSubmission']);
Route::get('/patients/{patientId}/submissions', [PatientController::class, 'index']);
Route::get('/patients/{patientId}/submissions/{submissionId}', [PatientController::class, 'show']);
Route::get('/patients/{patientId}/summary', [PatientController::class, 'summary']);