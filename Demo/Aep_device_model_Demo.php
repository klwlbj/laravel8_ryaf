<?php
require_once 'Apis\Aep_device_model.php';

class Aep_device_model_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_device_model::QueryPropertyList("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "10015488");
        echo("result = " . $result. "\n");

        $result = Aep_device_model::QueryServiceList("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "10015488");
        echo("result = " . $result. "\n");


    }
}
