<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Auth::routes();
Route::get('/', function () {
    return redirect(route('login'));
});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware(['auth']);
;
Route::get('/diagramador/{id}', [App\Http\Controllers\DocumentController::class, 'diagrama'])->name('diagramador')->middleware(['auth']);
;
Route::get('/exportar', [App\Http\Controllers\DocumentController::class, 'exportarjson'])->name('exportar')->middleware(['auth']);
;
Route::get('/exportarxml', [App\Http\Controllers\DocumentController::class, 'exportarxml'])->name('exportarxml')->middleware(['auth']);
;
