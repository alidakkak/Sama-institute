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

        try {
            foreach ($changes as $change) {
                $data = DB::table($change->table_name)->where('id', $change->record_id)->first();

                if ($data) {
                    $response = Http::post('https://api.dev2.gomaplus.tech/api/sync', [
                        'table_name' => $change->table_name,
                        'record_id' => $change->record_id,
                        'change_type' => $change->change_type,
                        'data' => $data,
                    ]);

                    if ($response->successful()) {
                        DB::table('changes')->where('id', $change->id)->delete();
                        $this->info('Successfully synced change ID: '.$change->id);
                    } else {
                        $this->error('Failed to sync change ID: '.$change->id.' - Status Code: '.$response->status());
                    }
                } else {
                    $this->error('Failed to fetch data for change ID: '.$change->id);
                }
            }
        } catch (\Exception $e) {
            $this->error('No Internet connection available or other error occurred');
        }

        $this->info('Sync process completed.');
    }
}
