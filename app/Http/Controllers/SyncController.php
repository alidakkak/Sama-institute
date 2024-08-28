<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    public function syncChanges(Request $request)
    {
        $table = $request->input('table_name');
        $recordId = $request->input('record_id');
        $changeType = $request->input('change_type');
        $data = $request->input('data');

        switch ($table) {
            case 'students':
                $this->processStudentChange($recordId, $changeType, $data);
                break;
            case 'semesters':
                $this->processSemesterChange($recordId, $changeType, $data);
                break;
            case 'subjects':
                $this->processSubjectChange($recordId, $changeType, $data);
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

    protected function processSemesterChange($recordId, $changeType, $data)
    {
        switch ($changeType) {
            case 'create':
                DB::table('semesters')->insert($data);
                break;
            case 'update':
                DB::table('semesters')->where('id', $recordId)->update($data);
                break;
            case 'delete':
                DB::table('semesters')->where('id', $recordId)->delete();
                break;
        }
    }

    protected function processSubjectChange($recordId, $changeType, $data)
    {
        switch ($changeType) {
            case 'create':
                DB::table('subjects')->insert($data);
                break;
            case 'update':
                DB::table('subjects')->where('id', $recordId)->update($data);
                break;
            case 'delete':
                DB::table('subjects')->where('id', $recordId)->delete();
                break;
        }
    }
}
