<?php
require_once 'Apis\Aep_device_status.php';

class Aep_device_status_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_device_status::QueryDeviceStatus("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_device_status::QueryDeviceStatusList("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_device_status::getDeviceStatusHisInTotal("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_device_status::getDeviceStatusHisInPage("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");


    }
}
