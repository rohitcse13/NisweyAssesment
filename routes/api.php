<?php

use App\Http\Controllers\ContactController;
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

Route::get('/view-contacts', [ContactController::class, 'index']);
Route::post('/save-contacts', [ContactController::class, 'store']);
Route::get('/view-contact/{id}', [ContactController::class, 'show']);
Route::put('/update-contacts/{id}', [ContactController::class, 'update']);
Route::delete('/delete-contacts/{id}', [ContactController::class, 'destroy']);
Route::post('/upload-contacts', [ContactController::class, 'importXML']);
