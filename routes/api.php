<?php

use App\Http\Controllers\StudentController;
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
Route::post('/loginStudent', [StudentController::class, 'login']);

Route::group(['middleware' => 'auth:api_student'], function () {
    Route::get('getInfoStudent', [StudentController::class, 'getInfoStudent']);
});
