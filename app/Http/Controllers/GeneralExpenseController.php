<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGeneralExpenseRequest;
use App\Http\Requests\UpdateGeneralExpenseRequest;
use App\Http\Resources\GeneralExpenseResource;
use App\Models\GeneralExpense;
use Illuminate\Http\Request;

class GeneralExpenseController extends Controller
{
    public function index()
    {
        $general = GeneralExpense::all();

        return GeneralExpenseResource::collection($general);
    }

    public function store(StoreGeneralExpenseRequest $request)
    {
        try {
            $general = GeneralExpense::create($request->all());

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => GeneralExpenseResource::make($general),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateGeneralExpenseRequest $request, $generalExpenseId)
    {
        try {
            $general = GeneralExpense::find($generalExpenseId);
            if (! $general) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $general->update($request->all());

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => GeneralExpenseResource::make($general),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($generalExpenseId)
    {
        $general = GeneralExpense::find($generalExpenseId);
        if (! $general) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return GeneralExpenseResource::make($general);
    }

    public function delete($generalExpenseId)
    {
        try {
            $general = GeneralExpense::find($generalExpenseId);
            if (! $general) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $general->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => GeneralExpenseResource::make($general),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
