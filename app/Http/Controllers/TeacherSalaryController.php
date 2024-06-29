<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherSalaryRequest;
use App\Http\Requests\UpdateTeacherSalaryRequest;
use App\Http\Resources\TeacherSalaryResource;
use App\Models\TeacherSalary;

class TeacherSalaryController extends Controller
{
    public function index()
    {
        $teacherSalary = TeacherSalary::all();

        return TeacherSalaryResource::collection($teacherSalary);
    }

    public function store(StoreTeacherSalaryRequest $request)
    {
        try {
            $teacherSalary = TeacherSalary::create($request->all());

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => TeacherSalaryResource::make($teacherSalary),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateTeacherSalaryRequest $request, $Id)
    {
        try {
            $teacherSalary = TeacherSalary::find($Id);
            if (! $teacherSalary) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $teacherSalary->update($request->all());

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => TeacherSalaryResource::make($teacherSalary),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
