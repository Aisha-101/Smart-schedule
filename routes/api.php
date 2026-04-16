<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SpecialistAvailabilityController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\SpecialistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Public routes
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::get('/services',[ServiceController::class,'index']);

//Protected routes
Route::middleware('auth:api')->group(function(){
    
    
    Route::post('/specialists', [SpecialistController::class, 'store']);
    

    Route::post('/services',[ServiceController::class,'store']);
    Route::put('/services/{id}',[ServiceController::class,'update']);

    Route::get('/appoinphp artisantments',[AppointmentController::class,'index']);
    Route::post('/appointments',[AppointmentController::class,'store']);
    Route::put('/appointments/{id}',[AppointmentController::class,'update']);
    Route::delete('/appointments/{id}',[AppointmentController::class,'destroy']);

    Route::get('/specialists/{id}/schedule',[SpecialistAvailabilityController::class,'index']);
    Route::post('/specialists/{id}/schedule',[SpecialistAvailabilityController::class,'store']);
});
