<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGradeRequest;
use App\Http\Requests\UpdateGradeRequest;
use App\Http\Resources\GradeResource;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function index()
    {
        $grade = Grade::all();

        return GradeResource::collection($grade);
    }

    public function store(StoreGradeRequest $request)
    {
        DB::beginTransaction();
        try {
            $grade = Grade::create($request->all());

            DB::commit();
            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => GradeResource::make($grade),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateGradeRequest $request, $gradeId)
    {
        try {
            $grade = Grade::find($gradeId);
            if (! $grade) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $grade->update($request->all());

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => GradeResource::make($grade),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($gradeId)
    {
        $grade = Grade::find($gradeId);
        if (! $grade) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return GradeResource::make($grade);
    }

    public function delete($Id)
    {
        try {
            $grade = Grade::find($Id);
            if (! $grade) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $grade->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => GradeResource::make($grade),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
