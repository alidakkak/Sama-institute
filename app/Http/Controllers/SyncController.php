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

        if (! in_array($table, ['students', 'semesters', 'subjects', 'registrations', 'classrooms', 'subject_classrooms',
            'student_subjects', 'scholarships', 'device_tokens', 'exams', 'extra_charges', 'import_logs', 'in_out_logs',
            'marks', 'notes', 'student_payments', 'teachers',
        ])) {
            return response()->json(['message' => 'Table not supported'], 400);
        }

        $this->processChange($table, $recordId, $changeType, $data);

        return response()->json(['message' => 'Sync successful']);
    }

    protected function processChange($table, $recordId, $changeType, $data)
    {
        switch ($changeType) {
            case 'create':
                DB::table($table)->insert($data);
                break;
            case 'update':
                DB::table($table)->where('id', $recordId)->update($data);
                break;
            case 'delete':
                DB::table($table)->where('id', $recordId)->delete();
                break;
            default:
                return response()->json(['message' => 'Change type not supported'], 400);
        }
    }
}
