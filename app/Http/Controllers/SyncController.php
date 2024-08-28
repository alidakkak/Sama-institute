<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    public function syncChanges(Request $request)
    {
        $table = $request->input('table_name');
        $recordId = $request->input('record_id');
        $changeType = $request->input('change_type');

        switch ($table) {
            case 'students':
                $data = Student::where('id', $recordId)->firstOrFail()->toArray();
                $this->processStudentChange($recordId, $changeType, $data);
                break;
            default:
                return response()->json(['message' => 'Table not supported'], 400);
        }

        return response()->json(['message' => 'Sync successful']);
    }

    protected function processStudentChange($recordId, $changeType, $data)
    {
        switch ($changeType) {
            case 'create':
                DB::table('students')->insert($data);
                break;
            case 'update':
                DB::table('students')->where('id', $recordId)->update($data);
                break;
            case 'delete':
                DB::table('students')->where('id', $recordId)->delete();
                break;
        }
    }
}
