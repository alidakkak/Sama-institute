<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $exam = Exam::all();

        return ExamResource::collection($exam);
    }

    public function store(StoreExamRequest $request)
    {
        try {
            $exam = Exam::create($request->all());

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => ExamResource::make($exam),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateExamRequest $request, $examId)
    {
        try {
            $exam = Exam::find($examId);
            if (! $exam) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $exam->update($request->all());

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => ExamResource::make($exam),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($examId)
    {
        $exam = Exam::find($examId);
        if (! $exam) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return ExamResource::make($exam);
    }

    public function delete($examId)
    {
        try {
            $exam = Exam::find($examId);
            if (! $exam) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $exam->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => ExamResource::make($exam),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
