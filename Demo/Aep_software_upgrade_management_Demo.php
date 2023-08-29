<?php
require_once 'Apis\Aep_software_upgrade_management.php';

class Aep_software_upgrade_management_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_software_upgrade_management::OperationalSoftwareUpgradeTask("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_software_upgrade_management::QuerySoftwareUpgradeSubtasks("dFI1lzE0EN2", "xQcjrfNLvQ", "", "10015488", "cd35c680b6d647068861f7fd4e79d3f5");
        echo("result = " . $result. "\n");

        $result = Aep_software_upgrade_management::QuerySoftwareUpgradeTask("dFI1lzE0EN2", "xQcjrfNLvQ", "", "10015488", "cd35c680b6d647068861f7fd4e79d3f5");
        echo("result = " . $result. "\n");

        $result = Aep_software_upgrade_management::CreateSoftwareUpgradeTask("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_software_upgrade_management::ModifySoftwareUpgradeTask("dFI1lzE0EN2", "xQcjrfNLvQ", "", "cd35c680b6d647068861f7fd4e79d3f5", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_software_upgrade_management::ControlSoftwareUpgradeTask("dFI1lzE0EN2", "xQcjrfNLvQ", "", "cd35c680b6d647068861f7fd4e79d3f5", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_software_upgrade_management::DeleteSoftwareUpgradeTask("dFI1lzE0EN2", "xQcjrfNLvQ", "", "10015488", "cd35c680b6d647068861f7fd4e79d3f5");
        echo("result = " . $result. "\n");

        $result = Aep_software_upgrade_management::QuerySoftwareUpradeDeviceList("dFI1lzE0EN2", "xQcjrfNLvQ", "10015488", "", "cd35c680b6d647068861f7fd4e79d3f5");
        echo("result = " . $result. "\n");

        $result = Aep_software_upgrade_management::QuerySoftwareUpgradeDetail("dFI1lzE0EN2", "xQcjrfNLvQ", "", "10015488", "cd35c680b6d647068861f7fd4e79d3f5");
        echo("result = " . $result. "\n");

        $result = Aep_software_upgrade_management::QuerySoftwareUpgradeTaskList("dFI1lzE0EN2", "xQcjrfNLvQ", "10015488", "cd35c680b6d647068861f7fd4e79d3f5");
        echo("result = " . $result. "\n");


    }
}
