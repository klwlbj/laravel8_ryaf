<?php
require_once 'Apis\Aep_standard_management.php';

class Aep_standard_management_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_standard_management::QueryStandardModel("dFI1lzE0EN2", "xQcjrfNLvQ", "");
        echo("result = " . $result. "\n");


    }
}
