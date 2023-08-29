<?php
require_once 'Apis\Tenant_device_statistics.php';

class Tenant_device_statistics_Demo
{
    public static function Demo(){
        $result=null;

        $result = Tenant_device_statistics::QueryTenantDeviceCount("dFI1lzE0EN2", "xQcjrfNLvQ");
        echo("result = " . $result. "\n");

        $result = Tenant_device_statistics::QueryTenantDeviceTrend("dFI1lzE0EN2", "xQcjrfNLvQ", "", "");
        echo("result = " . $result. "\n");

        $result = Tenant_device_statistics::QueryTenantDeviceActiveCount("dFI1lzE0EN2", "xQcjrfNLvQ");
        echo("result = " . $result. "\n");


    }
}
