<?php

use App\Http\Controllers\ChargeController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\UseController;
use App\Http\Controllers\GetUsageLogsController;
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

Route::get("balance", [BalanceController::class, "getBalance"]);
Route::post("use", [UseController::class, "use"]);
Route::post("charge", [ChargeController::class, "charge"]);
Route::get("usage_logs", [GetUsageLogsController::class, "getUsageLogs"]);
