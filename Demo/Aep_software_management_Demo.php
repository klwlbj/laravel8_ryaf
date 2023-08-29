<?php
require_once 'Apis\Aep_software_management.php';

class Aep_software_management_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_software_management::UpdateSoftware("dFI1lzE0EN2", "xQcjrfNLvQ", "", "cd35c680b6d647068861f7fd4e79d3f5", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_software_management::DeleteSoftware("dFI1lzE0EN2", "xQcjrfNLvQ", "", "10015488", "cd35c680b6d647068861f7fd4e79d3f5");
        echo("result = " . $result. "\n");

        $result = Aep_software_management::QuerySoftware("dFI1lzE0EN2", "xQcjrfNLvQ", "", "10015488", "cd35c680b6d647068861f7fd4e79d3f5");
        echo("result = " . $result. "\n");

        $result = Aep_software_management::QuerySoftwareList("dFI1lzE0EN2", "xQcjrfNLvQ", "10015488", "cd35c680b6d647068861f7fd4e79d3f5");
        echo("result = " . $result. "\n");


    }
}
