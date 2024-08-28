<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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

//    public function test()
//    {
//        $messages = [];
//
//        $changes = DB::table('changes')->get();
//
//        foreach ($changes as $change) {
//            try {
//                $response = Http::post('https://api.dev2.gomaplus.tech/api/sync', [
//                    'table_name' => $change->table_name,
//                    'record_id' => $change->record_id,
//                    'change_type' => $change->change_type,
//                ]);
//
//                if ($response->successful()) {
//                    DB::table('changes')->where('id', $change->id)->delete();
//                    $messages[] = 'Successfully synced change ID: ' . $change->id;
//                } else {
//                    $messages[] = 'Failedd to sync change ID: ' . $change->id . ' - ' . $response->body();
//                }
//            } catch (\Exception $e) {
//                $messages[] = 'Failed to sync change ID: ' . $change->id . ' - Exception: ' . $e->getMessage();
//            }
//        }
//
//        return response()->json([
//            'message' => 'Sync process completed.',
//            'details' => $messages,
//        ]);
//    }
}
