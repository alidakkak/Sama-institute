<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClassroomRequest;
use App\Http\Requests\UpdateClassroomRequest;
use App\Http\Resources\ClassroomResource;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        $classroom = Classroom::all();

        return ClassroomResource::collection($classroom);
    }

    public function store(StoreClassroomRequest $request)
    {
        try {
            $classroom = Classroom::create($request->all());

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => ClassroomResource::make($classroom),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateClassroomRequest $request, $classroomId)
    {
        try {
            $classroom = Classroom::find($classroomId);
            if (! $classroom) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $classroom->update($request->all());

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => ClassroomResource::make($classroom),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($classroomId)
    {
        $classroom = Classroom::find($classroomId);
        if (! $classroom) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return ClassroomResource::make($classroom);
    }

    public function delete($classroomId)
    {
        try {
            $classroom = Classroom::find($classroomId);
            if (! $classroom) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $classroom->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => ClassroomResource::make($classroom),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
