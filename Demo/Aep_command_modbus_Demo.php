<?php
require_once 'Apis\Aep_command_modbus.php';

class Aep_command_modbus_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_command_modbus::QueryCommandList("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "10015488", "10015488test");
        echo("result = " . $result. "\n");

        $result = Aep_command_modbus::QueryCommand("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "10015488", "10015488test", "");
        echo("result = " . $result. "\n");

        $result = Aep_command_modbus::CancelCommand("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_command_modbus::CreateCommand("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "{}");
        echo("result = " . $result. "\n");


    }
}
