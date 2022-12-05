<?php

use App\Http\Livewire\Home;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('saveDocument',[Home::class,'saveD']);
Route::get('loadDocument',[Home::class,'loadD']);
Route::post('deleteDocument',[Home::class,'eliminarcontenido']);
Route::get('diagramaDatos',[Home::class,'datosD']);
