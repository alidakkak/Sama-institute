<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExtraChargeController;
use App\Http\Controllers\GeneralExpenseController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentPaymentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherSalaryController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

//Route::group(['middleware' => 'check_user:1'], function () {

//// Semester
Route::get('semesters', [SemesterController::class, 'index']);
Route::get('getStudentAndSubjectBySemesterID/{semesterId}', [SemesterController::class, 'getStudentAndSubjectBySemesterID']);
Route::post('semesters', [SemesterController::class, 'store']);
Route::patch('semesters/{semesterId}', [SemesterController::class, 'update']);
Route::get('semesters/{semesterId}', [SemesterController::class, 'show']);
Route::delete('semesters/{semesterId}', [SemesterController::class, 'delete']);

//// Classroom
Route::get('classrooms/{classroomId}', [ClassroomController::class, 'show']);
Route::post('classrooms', [ClassroomController::class, 'store']);
Route::patch('classrooms/{classroomId}', [ClassroomController::class, 'update']);
Route::delete('classrooms/{classroomId}', [ClassroomController::class, 'delete']);
Route::post('addTeacher', [ClassroomController::class, 'addTeacher']);
Route::patch('updateTeacher', [ClassroomController::class, 'updateTeacher']);

//// Student
Route::get('students', [StudentController::class, 'index']);
Route::get('searchStudents', [StudentController::class, 'searchStudent']);
Route::post('students', [StudentController::class, 'store']);
Route::post('students/{studentId}', [StudentController::class, 'update']);
Route::get('students/{studentId}', [StudentController::class, 'show']);
Route::delete('students/{studentId}', [StudentController::class, 'delete']);
Route::patch('regeneratePassword/{studentId}', [StudentController::class, 'regeneratePassword']);

//// Registration
Route::post('registrations', [RegistrationController::class, 'store']);
Route::patch('registrations/{id}', [RegistrationController::class, 'update']);
Route::post('calculateCoursePrice', [RegistrationController::class, 'calculateCoursePrice']);
Route::post('withdrawalFromTheCourse', [RegistrationController::class, 'withdrawalFromTheCourse']);

//// Student Payment
Route::get('studentPayment', [StudentPaymentController::class, 'index']);
Route::post('studentPayment', [StudentPaymentController::class, 'store']);
Route::patch('studentPayment/{Id}', [StudentPaymentController::class, 'update']);

//// Note
Route::get('notes', [NoteController::class, 'index']);
Route::post('notes', [NoteController::class, 'store']);
Route::patch('notes/{noteId}', [NoteController::class, 'update']);
Route::get('notes/{noteId}', [NoteController::class, 'show']);
Route::delete('notes/{noteId}', [NoteController::class, 'delete']);

//// ExtraCharge
Route::get('extraCharges', [ExtraChargeController::class, 'index']);
Route::post('extraCharges', [ExtraChargeController::class, 'store']);
Route::patch('extraCharges/{extraChargeId}', [ExtraChargeController::class, 'update']);
Route::get('extraCharges/{extraChargeId}', [ExtraChargeController::class, 'show']);
Route::delete('extraCharges/{extraChargeId}', [ExtraChargeController::class, 'delete']);

//// Subject
Route::get('subjects', [SubjectController::class, 'index']);
Route::delete('subjects/{subjectId}', [SubjectController::class, 'delete']);

//// Exam
Route::get('exams', [ExamController::class, 'index']);
Route::post('exams', [ExamController::class, 'store']);
Route::patch('exams/{examId}', [ExamController::class, 'update']);
Route::get('exams/{examId}', [ExamController::class, 'show']);
Route::delete('exams/{examId}', [ExamController::class, 'delete']);

//// Mark
Route::post('marks', [MarkController::class, 'store']);
Route::patch('marks/{markId}', [MarkController::class, 'update']);
Route::get('showStudent', [MarkController::class, 'showStudent']);
Route::delete('marks/{markId}', [MarkController::class, 'delete']);

//// Scholarship
Route::get('scholarships', [ScholarshipController::class, 'index']);
Route::post('scholarships', [ScholarshipController::class, 'store']);
Route::patch('scholarships/{scholarshipId}', [ScholarshipController::class, 'update']);
Route::get('scholarships/{scholarshipId}', [ScholarshipController::class, 'show']);
Route::delete('scholarships/{scholarshipId}', [ScholarshipController::class, 'delete']);
Route::post('specialDiscount', [ScholarshipController::class, 'specialDiscount']);

//// Teacher
Route::get('teachers', [TeacherController::class, 'index']);
Route::get('teacherActive', [TeacherController::class, 'teacherActive']);
Route::post('teachers', [TeacherController::class, 'store']);
Route::patch('switchStatus/{id}', [TeacherController::class, 'switchStatus']);
Route::patch('teachers/{teacherId}', [TeacherController::class, 'update']);
Route::get('teachers/{teacherId}', [TeacherController::class, 'show']);
Route::delete('teachers/{teacherId}', [TeacherController::class, 'delete']);

//// Teacher Salary
Route::get('teacherSalary/{teacherID}', [TeacherSalaryController::class, 'index']);
Route::post('teacherSalary', [TeacherSalaryController::class, 'store']);
Route::patch('teacherSalary/{Id}', [TeacherSalaryController::class, 'update']);

//// General Expense
Route::get('generalExpenses', [GeneralExpenseController::class, 'index']);
Route::post('generalExpenses', [GeneralExpenseController::class, 'store']);
Route::patch('generalExpenses/{generalExpenseId}', [GeneralExpenseController::class, 'update']);
Route::get('generalExpenses/{generalExpenseId}', [GeneralExpenseController::class, 'show']);
Route::delete('generalExpenses/{generalExpenseId}', [GeneralExpenseController::class, 'delete']);

/// Statistic
Route::get('getStatisticGeneral', [StatisticController::class, 'getStatisticGeneral']);

/// Attendance
Route::post('attendances', [AttendanceController::class, 'fetchAttendance']);
Route::get('attendances', [AttendanceController::class, 'test']);

//});

//Route::get('attendances', [NoteController::class, 'get']);
