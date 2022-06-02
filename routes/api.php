<?php

use App\Http\Controllers\BitcoinController;
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
Route::get(
    '/getBitcoinInfo',
    [BitcoinController::class, 'getBitcoinInfo']
)->name('getBitcoinInfo');

Route::fallback(function(){
    return response()->json(['result'=> false,'message' => 'Url not found'], 404);
});
