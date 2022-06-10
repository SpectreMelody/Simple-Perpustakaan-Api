<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\booksController;
use App\Http\Controllers\userController;
use App\Http\Controllers\authController;
use App\Http\Controllers\servicesController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Book
Route::middleware('auth:sanctum')->get('/indexBook', [booksController::class, 'index']);
Route::middleware('auth:sanctum')->post('/storeBook', [booksController::class, 'store']);
Route::middleware('auth:sanctum')->get('/indexBook/{id}', [booksController::class, 'show']);
Route::middleware('auth:sanctum')->put('/updateBook', [booksController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/destroyBook/{id}', [booksController::class, 'destroy']);

//User
Route::post('/login', [authController::class, 'login']);
Route::post('/register', [userController::class, 'store']);
Route::middleware('auth:sanctum')->post('/logout', [authController::class, 'logout']);

Route::get('/indexPerpustakaan', [userController::class, 'index']);
Route::get('/indexPerpustakaan/{id}', [userController::class, 'show']);
Route::middleware('auth:sanctum')->put('/updatePerpustakaan/{id}', [userController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/destroyPerpustakaan/{id}', [userController::class, 'destroy']);

//Service
Route::middleware('auth:sanctum')->get('/indexPeminjaman', [servicesController::class, 'indexPeminjaman']);
Route::middleware('auth:sanctum')->get('/indexPengembalian', [servicesController::class, ' indexSudahMengembalikan']);
Route::middleware('auth:sanctum')->get('/indexPeminjam/{nama}', [servicesController::class, 'showPeminjamanByNama']);
Route::middleware('auth:sanctum')->get('/indexPengembalian/{nama}', [servicesController::class, 'showPengembalianByNama']);
Route::middleware('auth:sanctum')->any('/servicePinjamBuku', [servicesController::class, 'pinjamBuku']);
// Route::post('/test', [servicesController::class, 'edit']);

