<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TagihanController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('layout.login');
})->middleware('guest')->name('login');
Route::get('/dashboard', function () {
    return view('layout.dashboard');
})->middleware('auth');
Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/master/data-pelanggan', [ClientController::class, 'index'])->name('master-client')->middleware('auth');
Route::get('/master/data-petugas', [AuthController::class, 'list_petugas'])->name('master-petugas')->middleware('auth');
Route::post('/add/client', [ClientController::class, 'add_client']);
Route::post('/add/petugas', [AuthController::class, 'add_petugas']);
Route::post('/update/client', [ClientController::class, 'update_client']);
Route::post('/update/petugas', [AuthController::class, 'update_petugas']);
Route::post('/update-harga-air', [TagihanController::class, 'update_harga']);
Route::get('/clients/{id}/edit', [ClientController::class, 'edit_client']);
Route::get('/petugas/{id}/edit', [AuthController::class, 'edit_petugas']);
Route::get('/clients/{id}/delete', [ClientController::class, 'delete_client']);
Route::get('/petugas/{id}/delete', [AuthController::class, 'delete_petugas']);
Route::post('/client/login', [ClientController::class, 'authenticate']);
Route::get('/master/harga-air', [TagihanController::class, 'harga_air'])->name('master-air')->middleware('auth');
Route::get('/client-dashboard', [ClientController::class, 'dashboard'])->name('client-dashboard');
Route::get('/siarkan', [TagihanController::class, 'siarkan_tagihan']);
Route::get('/tagihan', [TagihanController::class, 'index_tagihan']);
Route::get('/tagihan/{id}/bayar', [TagihanController::class, 'bayar_tagihan']);
Route::get('/kwitansi/{id}', [TagihanController::class, 'kwitansi']);
Route::get('/dash/admin/chart-pemakaian', [TagihanController::class, 'chart_admin_pemakaian_year'])->name('pemakaian.chart');
Route::get('/dash/admin/chart-tagihan', [TagihanController::class, 'chart_admin_tagihan_year'])->name('tagihan.chart');
Route::post('/broadcast/{id}', [TagihanController::class, 'sendWhatsAppMessages']);
Route::post('/siarkan', [TagihanController::class, 'siarkan']);
