<?php

use App\Http\Controllers\user_nurmal\ClinicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuth;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

Route::post('auth/google', [GoogleAuth::class, 'GoogleLogin']);
Route::get('Clinics/nearby', [ClinicController::class, 'nearbyClinics']);
Route::get("Clinics/all",[ClinicController::class, "allClinics"]);
