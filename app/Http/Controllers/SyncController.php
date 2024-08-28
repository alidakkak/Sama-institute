<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncController extends Controller
{
    public function syncChanges(Request $request)
    {
        $table = $request->input('table_name');
        $recordId = $request->input('record_id');
        $changeType = $request->input('change_type');
        $data = $request->input('data');

        if (!in_array($table, ['students', 'semesters', 'subjects', 'registrations', 'classrooms', 'subject_classrooms',
            'student_subjects', 'scholarships', 'device_tokens', 'exams', 'extra_charges', 'import_logs', 'in_out_logs',
            'marks', 'notes', 'student_payments', 'teachers',
        ])) {
            return response()->json(['message' => 'Table not supported'], 400);
        }

        try {
            $this->processChange($table, $recordId, $changeType, $data);
            return response()->json(['message' => 'Sync successful']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to process change', 'error' => $e->getMessage()], 500);
        }
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

 /*   public function test()
    {
        $changes = DB::table('changes')->get();
        $messages = [];

        foreach ($changes as $change) {
            $data = DB::table($change->table_name)->where('id', $change->record_id)->first();

            if ($data) {
                $response = Http::post('https://api.dev2.gomaplus.tech/api/sync', [
                    'table_name' => $change->table_name,
                    'record_id' => $change->record_id,
                    'change_type' => $change->change_type,
                    'data' =>  $data,
                ]);

                if ($response->successful()) {
                    DB::table('changes')->where('id', $change->id)->delete();
                    $messages[] = 'Successfully synced change ID: ' . $change->id;
                } else {
                    $messages[] = 'Failed to sync change ID: ' . $change->id . ' - Status Code: ' . $response->status();
                }
            } else {
                Log::error('Failed to fetch data for change ID: ' . $change->id);
                $messages[] = 'Failed to fetch data for change ID: ' . $change->id;
            }
        }

        return response()->json([
            'message' => 'Sync process completed.',
            'details' => $messages,
        ]);
    }*/
}
