<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSemesterRequest;
use App\Http\Requests\UpdateSemesterRequest;
use App\Http\Resources\SemesterResource;
use App\Http\Resources\StudentSubjectResource;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class SemesterController extends Controller
{
    /// Get Student And Subject By Semester ID
    public function getStudentAndSubjectBySemesterID($semesterId)
    {
        $semester = Semester::find($semesterId);
        if (! $semester) {
            return response()->json(['message' => 'Semester not found'], 404);
        }

        return StudentSubjectResource::make($semester);
    }

    public function index()
    {
        $semester = Semester::all();

        return SemesterResource::collection($semester);
    }

    public function store(StoreSemesterRequest $request)
    {
        try {
            DB::beginTransaction();
            $semester = Semester::create($request->all());

            if ($request->has('subjects')) {
                $semester->subject()->createMany($request->input('subjects'));
            }
            DB::commit();

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => SemesterResource::make($semester),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateSemesterRequest $request, $semesterId)
    {
        try {
            DB::beginTransaction();
            $semester = Semester::find($semesterId);
            if (! $semester) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $semester->update($request->all());

            if ($request->has('subjects')) {
                $subjectsData = $request->input('subjects');
                foreach ($subjectsData as $subjectData) {
                    $subject = Subject::find($subjectData['subject_id']);
                    if ($subject && $subject->semester_id == $semester->id) {
                        $subject->update($subjectData);
                    }
                }
            }
            DB::commit();

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => SemesterResource::make($semester),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($semesterId)
    {
        $semester = Semester::find($semesterId);
        if (! $semester) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return SemesterResource::make($semester);
    }

    public function delete($semesterId)
    {
        try {
            $semester = Semester::find($semesterId);
            if (! $semester) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $semester->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => SemesterResource::make($semester),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
