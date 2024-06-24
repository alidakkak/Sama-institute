<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\GeneralExpenseController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\NameExpenseController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

//Route::group(['middleware' => 'check_user:1'], function () {

//// Semester
Route::get('semesters', [SemesterController::class, 'index']);
Route::post('semesters', [SemesterController::class, 'store']);
Route::patch('semesters/{semesterId}', [SemesterController::class, 'update']);
Route::get('semesters/{semesterId}', [SemesterController::class, 'show']);
Route::delete('semesters/{semesterId}', [SemesterController::class, 'delete']);

//// Classroom
Route::get('classrooms', [ClassroomController::class, 'index']);
Route::post('classrooms', [ClassroomController::class, 'store']);
Route::patch('classrooms/{classroomId}', [ClassroomController::class, 'update']);
Route::get('classrooms/{classroomId}', [ClassroomController::class, 'show']);
Route::delete('classrooms/{classroomId}', [ClassroomController::class, 'delete']);
Route::post('addTeacher', [ClassroomController::class, 'addTeacher']);

//// Student
Route::get('students', [StudentController::class, 'index']);
Route::get('showDetails/{studentID}', [StudentController::class, 'showDetails']);
Route::post('students', [StudentController::class, 'store']);
Route::patch('students/{studentId}', [StudentController::class, 'update']);
Route::get('students/{studentId}', [StudentController::class, 'show']);
Route::delete('students/{studentId}', [StudentController::class, 'delete']);

//// Note
Route::get('notes', [NoteController::class, 'index']);
Route::post('notes', [NoteController::class, 'store']);
Route::patch('notes/{noteId}', [NoteController::class, 'update']);
Route::get('notes/{noteId}', [NoteController::class, 'show']);
Route::delete('notes/{noteId}', [NoteController::class, 'delete']);

//// Subject
Route::get('subjects', [SubjectController::class, 'index']);
Route::post('subjects', [SubjectController::class, 'store']);
Route::patch('subjects/{subjectId}', [SubjectController::class, 'update']);
Route::get('subjects/{subjectId}', [SubjectController::class, 'show']);
Route::delete('subjects/{subjectId}', [SubjectController::class, 'delete']);

//// Exam
Route::get('exams', [ExamController::class, 'index']);
Route::post('exams', [ExamController::class, 'store']);
Route::patch('exams/{examId}', [ExamController::class, 'update']);
Route::get('exams/{examId}', [ExamController::class, 'show']);
Route::delete('exams/{examId}', [ExamController::class, 'delete']);

//// Mark
Route::get('marks', [MarkController::class, 'index']);
Route::post('marks', [MarkController::class, 'store']);
Route::patch('marks/{markId}', [MarkController::class, 'update']);
Route::get('marks/{markId}', [MarkController::class, 'show']);
Route::delete('marks/{markId}', [MarkController::class, 'delete']);

//// Scholarship
Route::get('scholarships', [ScholarshipController::class, 'index']);
Route::post('scholarships', [ScholarshipController::class, 'store']);
Route::patch('scholarships/{scholarshipId}', [ScholarshipController::class, 'update']);
Route::get('scholarships/{scholarshipId}', [ScholarshipController::class, 'show']);
Route::delete('scholarships/{scholarshipId}', [ScholarshipController::class, 'delete']);

//// Teacher
Route::get('teachers', [TeacherController::class, 'index']);
Route::post('teachers', [TeacherController::class, 'store']);
Route::patch('teachers/{teacherId}', [TeacherController::class, 'update']);
Route::get('teachers/{teacherId}', [TeacherController::class, 'show']);
Route::delete('teachers/{teacherId}', [TeacherController::class, 'delete']);

//// Name Expenses
Route::get('nameExpenses', [NameExpenseController::class, 'index']);
Route::post('nameExpenses', [NameExpenseController::class, 'store']);
Route::patch('nameExpenses/{nameExpenseId}', [NameExpenseController::class, 'update']);
Route::get('nameExpenses/{nameExpenseId}', [NameExpenseController::class, 'show']);
Route::delete('nameExpenses/{nameExpenseId}', [NameExpenseController::class, 'delete']);

//// General Expense
Route::get('generalExpenses', [GeneralExpenseController::class, 'index']);
Route::post('generalExpenses', [GeneralExpenseController::class, 'store']);
Route::patch('generalExpenses/{generalExpenseId}', [GeneralExpenseController::class, 'update']);
Route::get('generalExpenses/{generalExpenseId}', [GeneralExpenseController::class, 'show']);
Route::delete('generalExpenses/{generalExpenseId}', [GeneralExpenseController::class, 'delete']);

//});
