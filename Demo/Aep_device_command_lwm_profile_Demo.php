<?php
require_once 'Apis\Aep_device_command_lwm_profile.php';

class Aep_device_command_lwm_profile_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_device_command_lwm_profile::CreateCommandLwm2mProfile("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");


    }
}
