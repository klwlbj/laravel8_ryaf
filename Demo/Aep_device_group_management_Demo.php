<?php
require_once 'Apis\Aep_device_group_management.php';

class Aep_device_group_management_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_device_group_management::CreateDeviceGroup("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_device_group_management::UpdateDeviceGroup("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_device_group_management::DeleteDeviceGroup("dFI1lzE0EN2", "xQcjrfNLvQ", "");
        echo("result = " . $result. "\n");

        $result = Aep_device_group_management::QueryDeviceGroupList("dFI1lzE0EN2", "xQcjrfNLvQ", "", "");
        echo("result = " . $result. "\n");

        $result = Aep_device_group_management::QueryGroupDeviceList("dFI1lzE0EN2", "xQcjrfNLvQ", "", "");
        echo("result = " . $result. "\n");

        $result = Aep_device_group_management::UpdateDeviceGroupRelation("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_device_group_management::getGroupDetailByDeviceId("dFI1lzE0EN2", "xQcjrfNLvQ", "10015488", "10015488test");
        echo("result = " . $result. "\n");


    }
}
