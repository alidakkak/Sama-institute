<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarkRequest;
use App\Http\Requests\UpdateMarkRequest;
use App\Http\Resources\MarkResource;
use App\Models\Mark;

class MarkController extends Controller
{
    public function index()
    {
        $mark = Mark::all();

        return MarkResource::collection($mark);
    }

    public function store(StoreMarkRequest $request)
    {
        try {
            $data = $request->validated();
            $marks = [];
            foreach ($data['student_id'] as $index => $student_id) {
                $mark = Mark::create([
                    'student_id' => $student_id,
                    'subject_id' => $data['subject_id'],
                    'exam_id' => $data['exam_id'],
                    'result' => $data['result'][$index],
                ]);
                $marks[] = $mark;
            }

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => MarkResource::collection(collect($marks)),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateMarkRequest $request, $markId)
    {
        try {
            $mark = Mark::find($markId);
            if (! $mark) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $mark->update($request->all());

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => MarkResource::make($mark),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($markId)
    {
        $mark = Mark::find($markId);
        if (! $mark) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return MarkResource::make($mark);
    }

    public function delete($markId)
    {
        try {
            $mark = Mark::find($markId);
            if (! $mark) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $mark->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => MarkResource::make($mark),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
