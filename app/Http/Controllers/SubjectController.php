<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subject = Subject::all();

        return SubjectResource::collection($subject);
    }

    public function store(StoreSubjectRequest $request)
    {
        try {
            $subject = Subject::create($request->all());

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => SubjectResource::make($subject),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateSubjectRequest $request, $subjectId)
    {
        try {
            $subject = Subject::find($subjectId);
            if (! $subject) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $subject->update($request->all());

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => SubjectResource::make($subject),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($subjectId)
    {
        $subject = Subject::find($subjectId);
        if (! $subject) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return SubjectResource::make($subject);
    }

    public function delete($subjectId)
    {
        try {
            $subject = Subject::find($subjectId);
            if (! $subject) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $subject->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => SubjectResource::make($subject),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
