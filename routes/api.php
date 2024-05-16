<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\WorkerController;

// Clock-in worker
Route::post('/worker/clock-in', [WorkerController::class, 'clockIn']);

// Get worker's clock-ins
Route::get('/worker/clock-ins', [WorkerController::class, 'getClockIns']);
