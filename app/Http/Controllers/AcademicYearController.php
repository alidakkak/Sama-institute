<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use App\Http\Resources\AcademicYearResource;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYear = AcademicYear::all();

        return AcademicYearResource::collection($academicYear);
    }

    public function store(StoreAcademicYearRequest $request)
    {
        try {
            $academicYear = AcademicYear::create($request->all());

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => AcademicYearResource::make($academicYear),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateAcademicYearRequest $request, $academicYearId)
    {
        try {
            $academicYear = AcademicYear::find($academicYearId);
            if (! $academicYear) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $academicYear->update($request->all());

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => AcademicYearResource::make($academicYear),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($academicYearId)
    {
        $academicYear = AcademicYear::find($academicYearId);
        if (! $academicYear) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return AcademicYearResource::make($academicYear);
    }

    public function delete($academicYearId)
    {
        try {
            $academicYear = AcademicYear::find($academicYearId);
            if (! $academicYear) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $academicYear->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => AcademicYearResource::make($academicYear),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
