<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
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
        $student = Student::all();

        return StudentResource::collection($student);
    }

    public function store(StoreStudentRequest $request)
    {
        DB::beginTransaction();
        try {
            $image = $request->hasFile('image') ? $request->file('image') : '/students_image/female.jpg';
            $password = Str::random(10);
            $student = Student::create(array_merge([
                'password' => Hash::make($password),
                'image' => $image,
                ...$request->except('password', 'image'),
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

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth('api_student')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

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
}
