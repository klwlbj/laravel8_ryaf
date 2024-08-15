<?php

namespace App\Http\Logic;

use Illuminate\Support\Facades\DB;

class WarehouseLogic extends BaseLogic
{
    public function getAllList($params)
    {
        $query = DB::connection('admin')->table('warehouse');


        return $query
            ->orderBy('waho_id','asc')
            ->get()->toArray();
    }
}
