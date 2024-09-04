<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExtraChargeController;
use App\Http\Controllers\MarkController;
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
Route::post('logout', [AuthController::class, 'logout']);

Route::group(['middleware' => 'auth:api_student'], function () {
    /// Get Information For Student
    Route::get('getInfoStudent', [StudentController::class, 'getInfoStudent']);

    /// Note
    Route::get('getNotes/{semesterID}', [NoteController::class, 'getNote']);

    /// Subject
    Route::get('getSubjects/{semesterID}', [SubjectController::class, 'getSubject']);
    Route::get('GPASubject/{subjectID}', [SubjectController::class, 'GPASubject']);
    Route::get('OverallGPA/{semesterID}', [SubjectController::class, 'OverallGPA']);

    /// Student Payment
    Route::get('getStudentPayment/{semesterID}', [StudentPaymentController::class, 'getStudentPayment']);

    /// Marks
    Route::get('getMarks/{semesterID}', [MarkController::class, 'getMarks']);

    /// ExtraCharge
    Route::get('getExtraCharges/{semesterID}', [ExtraChargeController::class, 'getExtraCharge']);

    /// Get Student Registration
    Route::get('getStudentRegistration', [StudentController::class, 'getStudentRegistration']);
});
