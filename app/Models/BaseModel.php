<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public static function printSql($query)
    {
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        return (vsprintf(str_replace('?', '%s', $sql), $bindings));
    }
}
