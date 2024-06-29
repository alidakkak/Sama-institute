<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentPaymentController;
use App\Http\Controllers\SubjectController;
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
    /// Get Information For Student
    Route::get('getInfoStudent', [StudentController::class, 'getInfoStudent']);

    /// Note
    Route::get('getNotes', [NoteController::class, 'getNote']);

    /// Subject
    Route::get('getSubjects', [SubjectController::class, 'getSubject']);

    /// Student Payment
    Route::get('getStudentPayment', [StudentPaymentController::class, 'getStudentPayment']);
});
