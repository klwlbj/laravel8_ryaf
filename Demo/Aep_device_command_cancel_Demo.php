<?php
require_once 'Apis\Aep_device_command_cancel.php';

class Aep_device_command_cancel_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_device_command_cancel::CancelAllCommand("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "{}");
        echo("result = " . $result. "\n");


    }
}
