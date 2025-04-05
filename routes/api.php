<?php

use App\Http\Controllers\admin\ClinicConfermController;
use App\Http\Controllers\admin\MunicipalityController;
use App\Http\Controllers\Clinic\Auth\Login;
use App\Http\Controllers\Clinic\Auth\Register;
use App\Http\Controllers\showSpecialtyController;
use App\Http\Controllers\admin\SpecialtyController;
use App\Http\Controllers\user_nurmal\ClinicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuth;
use App\Http\Controllers\Clinic\DoctorController;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());

});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('Clinics/logout', [login::class, 'logout']);
});
Route::post('auth/google', [GoogleAuth::class, 'GoogleLogin']);
Route::git('Clinics/nearby', [ClinicController::class, 'nearbyClinics']);
Route::get("Clinics/all", [ClinicController::class, "allClinics"]);
Route::get('/clinics/search', [ClinicController::class, 'searchClinics']);
Route::get('clinics/{id}', [ClinicController::class, 'showClinic']);

Route::get("Clinics/all/conferm", [ClinicConfermController::class, "allClinicsNotConferm"]);
Route::get('/Clinics/search/conferm', [ClinicConfermController::class, 'searchClinicsNotConferm']);
Route::post('/clinics/{id}/approve', [ClinicConfermController::class, 'approveClinic']);


Route::post('Clinics/login', [Login::class, 'login']);
Route::post('Clinics/register', [Register::class, 'register']);
Route::put('Clinics/update/{id}', [register::class, 'update']);
Route::delete('Clinics/delete/{id}', [register::class, 'destroy']);


Route::get('Specialty/show', [showSpecialtyController::class, 'index']);
Route::post('Specialty/add', [SpecialtyController::class, 'store']);
Route::put('Specialty/update/{id}', [SpecialtyController::class, 'update']);
Route::delete('Specialty/delete/{id}', [SpecialtyController::class, 'destroy']);


Route::post('Municipality/add', [MunicipalityController::class, 'store']);
Route::get('Municipality/show', [MunicipalityController::class, 'show']);
Route::put('Municipality/update/{id}', [MunicipalityController::class, 'update']);
Route::delete('Municipality/delete/{id}', [MunicipalityController::class, 'destroy']);



Route::get('show/doctor/{id}', [DoctorController::class, 'index']);
Route::post('add/doctor', [DoctorController::class, 'store']);
Route::put('updata/doctor/{id}', [DoctorController::class, 'update']);
Route::delete('delete/doctor/{id}', [DoctorController::class, 'destroy']);
Route::get('doctor/{id}', [DoctorController::class, 'showdoctor']);

