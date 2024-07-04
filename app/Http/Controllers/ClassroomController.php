<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClassroomRequest;
use App\Http\Requests\UpdateClassroomRequest;
use App\Http\Resources\ClassroomResource;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\SubjectClassroom;
use Illuminate\Support\Facades\DB;

class ClassroomController extends Controller
{
    //// Get Student And Subject By Classroom ID
    public function show($classroomId)
    {
        $classroom = Classroom::with(['subjects.teachers'])->find($classroomId);
        if (! $classroom) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return ClassroomResource::make($classroom);
    }

    ////  Add Teacher To Subject And Classroom
    public function addTeacher(StoreClassroomRequest $request)
    {
        $validated = $request->validated();
        $classroom = Classroom::findOrFail($validated['classroom_id']);

        $existingPivot = $classroom->subjects()->where('subject_id', $validated['subject_id'])->first();

        if ($existingPivot) {
            $classroom->subjects()->updateExistingPivot($validated['subject_id'], ['teacher_id' => $validated['teacher_id']]);
        } else {
            $classroom->subjects()->attach($validated['subject_id'], ['teacher_id' => $validated['teacher_id']]);
        }

        return response()->json(['message' => 'Success'], 200);
    }

    public function store(StoreClassroomRequest $request)
    {
        try {
            DB::beginTransaction();
            $classroom = Classroom::create($request->all());

            $semesterId = $request->input('semester_id');
            $subjects = Subject::where('semester_id', $semesterId)->select('id')->get();

            foreach ($subjects as $subject) {
                SubjectClassroom::create([
                    'subject_id' => $subject->id,
                    'classroom_id' => $classroom->id,
                ]);
            }
            DB::commit();

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => ClassroomResource::make($classroom),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

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
