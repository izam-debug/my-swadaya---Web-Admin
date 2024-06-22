<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TagihanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/api/user', [AuthController::class, 'apiLogin']);
Route::post('/api/get-tagihan', [TagihanController::class, 'apiGet']);
Route::post('/api/update-tagihan', [TagihanController::class, 'updateTagihan']);
