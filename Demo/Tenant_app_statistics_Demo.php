<?php
require_once 'Apis\Tenant_app_statistics.php';

class Tenant_app_statistics_Demo
{
    public static function Demo(){
        $result=null;

        $result = Tenant_app_statistics::QueryTenantApiMonthlyCount("dFI1lzE0EN2", "xQcjrfNLvQ");
        echo("result = " . $result. "\n");

        $result = Tenant_app_statistics::QueryTenantAppCount("dFI1lzE0EN2", "xQcjrfNLvQ");
        echo("result = " . $result. "\n");

        $result = Tenant_app_statistics::QueryTenantApiTrend("dFI1lzE0EN2", "xQcjrfNLvQ", "", "");
        echo("result = " . $result. "\n");


    }
}
