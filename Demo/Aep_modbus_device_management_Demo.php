<?php
require_once 'Apis\Aep_modbus_device_management.php';

class Aep_modbus_device_management_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_modbus_device_management::UpdateDevice("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "10015488test", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_modbus_device_management::CreateDevice("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_modbus_device_management::QueryDevice("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "10015488test", "10015488");
        echo("result = " . $result. "\n");

        $result = Aep_modbus_device_management::QueryDeviceList("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "10015488");
        echo("result = " . $result. "\n");

        $result = Aep_modbus_device_management::DeleteDevice("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "10015488", "");
        echo("result = " . $result. "\n");

        $result = Aep_modbus_device_management::ListDeviceInfo("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "{}");
        echo("result = " . $result. "\n");


    }
}
