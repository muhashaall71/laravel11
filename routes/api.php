<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;


Route::get('/users', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get("/test", function(){
    return response()->json([
        "status" => 'success',
        "message" => "hello world"
    ]);
});


// Route API untuk CRUD Data
Route::get('/dataAll', [PostController::class, 'index']); // GET semua data
Route::get('/data', [PostController::class, 'indexReq']); // GET data by req
Route::get('/data/filter', [PostController::class, 'indexFilter']); //GET by filter
Route::post('/data', [PostController::class, 'store']); // POST data baru
Route::get('/data/{id}', [PostController::class, 'show']); // GET data berdasarkan ID
Route::put('/data/{id}', [PostController::class, 'update']); // UPDATE data
Route::delete('/data/{id}', [PostController::class, 'destroy']); // DELETE data

