<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNameExpenseRequest;
use App\Http\Requests\UpdateNameExpenseRequest;
use App\Http\Resources\NameExpenseResource;
use App\Models\NameExpense;

class NameExpenseController extends Controller
{
    public function index()
    {
        $nameExpense = NameExpense::all();

        return NameExpenseResource::collection($nameExpense);
    }

    public function store(StoreNameExpenseRequest $request)
    {
        try {
            $nameExpense = NameExpense::create($request->all());

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => NameExpenseResource::make($nameExpense),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateNameExpenseRequest $request, $nameExpenseId)
    {
        try {
            $nameExpense = NameExpense::find($nameExpenseId);
            if (! $nameExpense) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $nameExpense->update($request->all());

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => NameExpenseResource::make($nameExpense),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($nameExpenseId)
    {
        $nameExpense = NameExpense::find($nameExpenseId);
        if (! $nameExpense) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return NameExpenseResource::make($nameExpense);
    }

    public function delete($nameExpenseId)
    {
        try {
            $nameExpense = NameExpense::find($nameExpenseId);
            if (! $nameExpense) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $nameExpense->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => NameExpenseResource::make($nameExpense),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
