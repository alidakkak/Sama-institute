<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GeneralObserver
{
    public function created(Model $model)
    {
        $this->logChange($model, 'create');
    }

    public function updated(Model $model)
    {
        $this->logChange($model, 'update');
    }

    public function deleted(Model $model)
    {
        $this->logChange($model, 'delete');
    }

    protected function logChange(Model $model, $changeType)
    {
        DB::table('changes')->insert([
            'table_name' => $model->getTable(),
            'record_id' => $model->getKey(),
            'change_type' => $changeType,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
