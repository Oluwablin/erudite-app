<?php

use App\Http\Controllers\v1\API\EventController;
use App\Http\Controllers\v1\API\ParticipantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'fetchEvents']);
        Route::post('/', [EventController::class, 'createEvent']);
        Route::post('/{event}/register', [ParticipantController::class, 'register']);
        Route::post('/{event}/bulk-register', [ParticipantController::class, 'bulkRegister']);
    });
});
