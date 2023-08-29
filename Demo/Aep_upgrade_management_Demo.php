<?php
require_once 'Apis\Aep_upgrade_management.php';

class Aep_upgrade_management_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_upgrade_management::QueryRemoteUpgradeDetail("dFI1lzE0EN2", "xQcjrfNLvQ", "", "10015488");
        echo("result = " . $result. "\n");

        $result = Aep_upgrade_management::QueryRemoteUpgradeTask("dFI1lzE0EN2", "xQcjrfNLvQ", "", "10015488");
        echo("result = " . $result. "\n");

        $result = Aep_upgrade_management::ControlRemoteUpgradeTask("dFI1lzE0EN2", "xQcjrfNLvQ", "", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_upgrade_management::QueryRemoteUpradeDeviceList("dFI1lzE0EN2", "xQcjrfNLvQ", "10015488", "");
        echo("result = " . $result. "\n");

        $result = Aep_upgrade_management::DeleteRemoteUpgradeTask("dFI1lzE0EN2", "xQcjrfNLvQ", "", "10015488");
        echo("result = " . $result. "\n");

        $result = Aep_upgrade_management::QueryRemoteUpgradeTaskList("dFI1lzE0EN2", "xQcjrfNLvQ", "10015488");
        echo("result = " . $result. "\n");

        $result = Aep_upgrade_management::ModifyRemoteUpgradeTask("dFI1lzE0EN2", "xQcjrfNLvQ", "", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_upgrade_management::CreateRemoteUpgradeTask("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_upgrade_management::OperationalRemoteUpgradeTask("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_upgrade_management::QueryRemoteUpgradeSubtasks("dFI1lzE0EN2", "xQcjrfNLvQ", "", "10015488");
        echo("result = " . $result. "\n");


    }
}
