<?php
require_once 'Apis\Aep_nb_device_management.php';

class Aep_nb_device_management_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_nb_device_management::BatchCreateNBDevice("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_nb_device_management::BatchCancelDevices("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_nb_device_management::DeleteDeviceByImei("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_nb_device_management::QueryDeviceByImei("dFI1lzE0EN2", "xQcjrfNLvQ", "10015488", "", "cd35c680b6d647068861f7fd4e79d3f5");
        echo("result = " . $result. "\n");


    }
}
