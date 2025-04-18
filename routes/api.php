<?php

use App\Http\Controllers\admin\Auth\LoginController;
use App\Http\Controllers\admin\Auth\LogoutController;
use App\Http\Controllers\admin\Auth\RegisterController;
use App\Http\Controllers\admin\ClinicConfermController;
use App\Http\Controllers\admin\MunicipalityController;
use App\Http\Controllers\Clinic\Auth\Login;
use App\Http\Controllers\Clinic\Auth\Register;
use App\Http\Controllers\Clinic\SchedulesController;
use App\Http\Controllers\Doctor\Auth\LogouteController;
use App\Http\Controllers\showSpecialtyController;
use App\Http\Controllers\admin\SpecialtyController;
use App\Http\Controllers\user_nurmal\ClinicController;
use App\Http\Middleware\BearerTokenMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuth;
use App\Http\Controllers\Clinic\DoctorController;



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return response()->json($request->user());
// });


// Route::middleware('auth:sanctum')->group(function () {
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [LogoutController::class, 'logout']);
    Route::post('/doctor/logout', [LogouteController::class, 'logout']);
    Route::post('Clinics/logout', [Login::class, 'logout']);
    Route::post('User/logout', [GoogleAuth::class, 'logout']);

    // clinic
    Route::put('Clinics/update/{id}', [register::class, 'update']);
    Route::delete('Clinics/delete/{id}', [register::class, 'destroy']);


    Route::get('show/doctor/{id}', [DoctorController::class, 'index']);
    Route::post('add/doctor', [DoctorController::class, 'store']);
    Route::put('updata/doctor/{id}', [DoctorController::class, 'update']);
    Route::delete('delete/doctor/{id}', [DoctorController::class, 'destroy']);
    Route::get('doctor/{id}', [DoctorController::class, 'showdoctor']);


    // admin
    Route::post('/clinics/{id}/approve', [ClinicConfermController::class, 'approveClinic']);


    Route::post('Specialty/add', [SpecialtyController::class, 'store']);
    Route::put('Specialty/update/{id}', [SpecialtyController::class, 'update']);
    Route::delete('Specialty/delete/{id}', [SpecialtyController::class, 'destroy']);

    Route::post('Schedules/add', [SchedulesController::class, 'addSchedules']);
    Route::post('Schedules/update/{id}', [SchedulesController::class, 'UpdataSchedules']);
    Route::post('Schedules/delete/{id}', [SchedulesController::class, 'delete']);
    Route::get('Schedules/show/{clinic_id}', [SchedulesController::class, 'getSchedulesByClinicId']);


    Route::put('Municipality/update/{id}', [MunicipalityController::class, 'update']);
    Route::delete('Municipality/delete/{id}', [MunicipalityController::class, 'destroy']);
    Route::post('Municipality/add', [MunicipalityController::class, 'store']);


    // user_normal
    Route::get('/clinics/by-specialty/{id}', [ClinicController::class, 'getBySpecialty']);
    Route::put('User/update', [GoogleAuth::class, 'update']);


    Route::post('/update_user/google', [GoogleAuth::class, 'Update']);
    Route::post('/user/logout', [GoogleAuth::class, 'logout']);
    
    // doctor
    Route::post('Schedules_doctor/add', [\App\Http\Controllers\Doctor\SchedulesController::class, 'addSchedules']);
    Route::post('Schedules_doctor/update/{id}', [\App\Http\Controllers\Doctor\SchedulesController::class, 'UpdataSchedules']);
    Route::post('Schedules_doctor/delete/{id}', [\App\Http\Controllers\Doctor\SchedulesController::class, 'delete']);
    Route::get('Schedules_doctor/show/{clinic_id}', [\App\Http\Controllers\Doctor\SchedulesController::class, 'getSchedulesByClinicId']);

});

Route::post('/admin/login', [LoginController::class, 'login']);
Route::post('/admin/create', [RegisterController::class, 'Registeradmin']);


Route::post('auth/google', [GoogleAuth::class, 'GoogleLogin']);
Route::get('Clinics/nearby', [ClinicController::class, 'nearbyClinics']);
Route::get("Clinics/all", [ClinicController::class, "allClinics"]);
Route::get('/clinics/search', [ClinicController::class, 'searchClinics']);
Route::get('clinics/{id}', [ClinicController::class, 'showClinic']);

Route::get("Clinics/all/conferm", [ClinicConfermController::class, "allClinicsNotConferm"]);
Route::get('/Clinics/search/conferm', [ClinicConfermController::class, 'searchClinicsNotConferm']);


Route::post('Clinics/login', [Login::class, 'login']);
Route::post('Clinics/register', [Register::class, 'register']);


Route::get('Specialty/show', [showSpecialtyController::class, 'index']);



Route::get('Municipality/show', [MunicipalityController::class, 'show']);



Route::post('/doctor/login', [\App\Http\Controllers\Doctor\Auth\LoginController::class, 'login']);




