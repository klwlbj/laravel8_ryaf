<?php
require_once 'Apis\Aep_firmware_management.php';

class Aep_firmware_management_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_firmware_management::UpdateFirmware("dFI1lzE0EN2", "xQcjrfNLvQ", "", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_firmware_management::QueryFirmwareList("dFI1lzE0EN2", "xQcjrfNLvQ", "10015488");
        echo("result = " . $result. "\n");

        $result = Aep_firmware_management::QueryFirmware("dFI1lzE0EN2", "xQcjrfNLvQ", "", "10015488");
        echo("result = " . $result. "\n");

        $result = Aep_firmware_management::DeleteFirmware("dFI1lzE0EN2", "xQcjrfNLvQ", "", "10015488");
        echo("result = " . $result. "\n");


    }
}
