<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;

class TeacherController extends Controller
{
    /// Get All Teachers
    public function index()
    {
        $teacher = Teacher::all();

        return TeacherResource::collection($teacher);
    }

    //// Get Teachers Active
    public function teacherActive()
    {
        $teacher = Teacher::all()->where('status', 1);

        return TeacherResource::collection($teacher);
    }

    /// Make Teacher Inactive
    public function switchStatus($id)
    {
        $teacher = Teacher::find($id);

        if (! $teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        $teacher->update(['status' => ! boolval($teacher->status)]);

        return response()->json(['message' => 'Updated SuccessFully'], 200);
    }

    public function store(StoreTeacherRequest $request)
    {
        try {
            $teacher = Teacher::create($request->all());
            //            $teacher->subjects()->attach($request->subject_ids);

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => TeacherResource::make($teacher),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateTeacherRequest $request, $teacherId)
    {
        try {
            $teacher = Teacher::find($teacherId);
            if (! $teacher) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $teacher->update($request->all());
            //            $teacher->subjects()->sync($request->subject_ids);

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => TeacherResource::make($teacher),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($teacherId)
    {
        $teacher = Teacher::find($teacherId);
        if (! $teacher) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return TeacherResource::make($teacher);
    }

    public function delete($teacherId)
    {
        try {
            $teacher = Teacher::find($teacherId);
            if (! $teacher) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $teacher->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => TeacherResource::make($teacher),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
