<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Http\Resources\RegistrationResource;
use App\Models\Registration;
use App\Models\StudentSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function store(StoreRegistrationRequest $request)
    {

        try {
            DB::beginTransaction();
            $registration = Registration::create($request->all());
            foreach ($request->subjects as $subject) {
                StudentSubject::create([
                    'subject_id' => $subject->id,
                    'student_id' => $request->student_id,
                ]);
            }
            DB::commit();

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => RegistrationResource::make($classroom),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
