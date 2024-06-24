<?php

namespace App\Http\Logic;

use Illuminate\Support\Facades\DB;

class AdminLogic extends BaseLogic
{
    public function getAllList($params)
    {
        $query = DB::connection('admin')->table('admin')
            ->where(['admin_enabled' => 1]);

        if(isset($params['keyword']) && $params['keyword']){
            $query->where('admin_name','like','%'.$params['keyword'].'%');
        }

        return $query
            ->get()->toArray();
    }
}
