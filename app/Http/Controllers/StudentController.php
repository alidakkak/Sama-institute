<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\DeviceToken;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('registrations')->get();

        $results = $students->map(function ($student) {
            return [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'father_name' => $student->father_name,
                'date_of_birth' => $student->date_of_birth,
                'created_at' => $student->created_at->format('Y-m-d'),
                //                'date_of_registration' => optional($student->registrations->first())->created_at ?
                //                    $student->registrations->first()->created_at->format('Y-m-d') : null,
            ];
        });

        return response()->json($results);
    }

    public function searchStudent(Request $request)
    {
        $search = '%'.$request->input('search').'%';
        $students = Student::where('id', 'LIKE', $search)
            ->orWhere('first_name', 'LIKE', $search)
            ->orWhere('last_name', 'LIKE', $search)
            ->get();

        $results = $students->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->first_name.' '.$student->last_name,
                'father_name' => $student->father_name,
                'image' => url($student->image),
            ];
        });

        return response()->json($results);
    }

    public function store(StoreStudentRequest $request)
    {
        DB::beginTransaction();
        try {
            $password = Str::random(6);
            $student = Student::create(array_merge([
                'password' => Hash::make($password),
                ...$request->except('password'),
            ]));
            DB::commit();

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => new StudentResource($student, $password),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateStudentRequest $request, $studentId)
    {
        DB::beginTransaction();
        try {
            $student = Student::find($studentId);
            if (! $student) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $student->update(array_merge([
                'password' => Hash::make($request->password),
                ...$request->except('password'),
            ]));
            DB::commit();

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => StudentResource::make($student),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($studentId)
    {
        $student = Student::find($studentId);
        if (! $student) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return StudentResource::make($student);
    }

    public function delete($studentId)
    {
        try {
            $student = Student::find($studentId);
            if (! $student) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $student->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => StudentResource::make($student),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    ////// API FOR FLUTTER

    public function getInfoStudent()
    {
        $student = auth('api_student')->user();
        if (! $student) {
            return response()->json(['message' => 'No authenticated student found'], 404);
        }

        return StudentResource::make($student);
    }

    public function getStudentRegistration()
    {
        $student = auth('api_student')->user();
        if (! $student) {
            return response()->json(['message' => 'No authenticated student found'], 404);
        }

        return response()->json(
            $student->registrations->map(function ($registration) {
                return [
                    'semesterID' => $registration->semester->id,
                    'semester' => $registration->semester->name,
                ];
            })
        );
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'password' => 'required|string|min:6',
            //            'device_token' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth('api_student')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $student = auth('api_student')->user();

        DeviceToken::updateOrCreate(
            ['student_id' => $student->id, 'device_token' => $request->device_token],
            ['student_id' => $student->id, 'device_token' => $request->device_token]
        );

        return $this->createNewToken($token);
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api_student')->factory()->getTTL() * 60,
            'user' => auth('api_student')->user(),
        ]);
    }

    public function regeneratePassword($studentID)
    {
        $student = Student::find($studentID);

        if (! $student) {
            return response()->json([
                'message' => 'Student not found',
            ], 404);
        }

        $newPassword = Str::random(6);

        $student->password = bcrypt($newPassword);

        $student->save();

        return response()->json([
            'newPassword' => $newPassword,
            'phone_number' => $student->phone_number,
        ]);
    }
}
