<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncChangesToServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-changes-to-server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync local database changes to the server when internet is available';

    /**
     * Execute the console command.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $changes = DB::table('changes')->get();
        $successMessages = [];
        $errorMessages = [];

        foreach ($changes as $change) {
            $data = DB::table($change->table_name)->where('id', $change->record_id)->first();

            if ($data) {
                $postData = $data->toArray();

                if ($change->table_name == 'students' && isset($postData['image'])) {
                    $photoPath = storage_path('app/public/students_image/' . $postData['image']);

                    if (file_exists($photoPath)) {
                        $response = Http::attach(
                            'photo', file_get_contents($photoPath), basename($photoPath)
                        )->post('https://api.dev2.gomaplus.tech/api/sync', [
                            'table_name' => $change->table_name,
                            'record_id' => $change->record_id,
                            'change_type' => $change->change_type,
                            'data' => $postData,
                        ]);
                    } else {
                        $errorMessages[] = 'Photo file not found for change ID: ' . $change->id;
                        continue;
                    }
                } else {
                    $response = Http::post('https://api.dev2.gomaplus.tech/api/sync', [
                        'table_name' => $change->table_name,
                        'record_id' => $change->record_id,
                        'change_type' => $change->change_type,
                        'data' => $postData,
                    ]);
                }

                if ($response->successful()) {
                    DB::table('changes')->where('id', $change->id)->delete();
                    $successMessages[] = 'Successfully synced change ID: ' . $change->id;
                } else {
                    $errorMessages[] = 'Failed to sync change ID: ' . $change->id . ' - Status Code: ' . $response->status();
                }
            } else {
                $errorMessages[] = 'Failed to fetch data for change ID: ' . $change->id;
            }
        }
        if (!empty($successMessages)) {
            foreach ($successMessages as $message) {
                $this->info($message);
            }
        }

        if (!empty($errorMessages)) {
            foreach ($errorMessages as $message) {
                $this->error($message);
            }
        }

        $this->info('Sync process completed.');
    }

}
