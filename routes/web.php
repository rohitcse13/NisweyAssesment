<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('contact-form');
});

Route::get('/view-contacts', [ContactController::class, 'getAllContacts'])->name('contacts.view');
Route::post('/submit-form', [ContactController::class, 'store'])->name('submit.form');
Route::put('/update-contact/{id}', [ContactController::class, 'update'])->name('update.contact');
Route::post('/upload-xml', [ContactController::class, 'importXML'])->name('upload.xml');
Route::delete('/delete-contact/{id}', [ContactController::class, 'destroy'])->name('delete.contact');
