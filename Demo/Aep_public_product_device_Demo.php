<?php
require_once 'Apis\Aep_public_product_device.php';

class Aep_public_product_device_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_public_product_device::QueryDeviceToken("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "{}");
        echo("result = " . $result. "\n");


    }
}
