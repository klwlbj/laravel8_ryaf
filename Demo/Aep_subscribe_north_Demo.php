<?php
require_once 'Apis\Aep_subscribe_north.php';

class Aep_subscribe_north_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_subscribe_north::GetSubscription("dFI1lzE0EN2", "xQcjrfNLvQ", "", "10015488", "cd35c680b6d647068861f7fd4e79d3f5");
        echo("result = " . $result. "\n");

        $result = Aep_subscribe_north::GetSubscriptionsList("dFI1lzE0EN2", "xQcjrfNLvQ", "10015488", "", "", "cd35c680b6d647068861f7fd4e79d3f5");
        echo("result = " . $result. "\n");

        $result = Aep_subscribe_north::DeleteSubscription("dFI1lzE0EN2", "xQcjrfNLvQ", "", "10015488", "", "cd35c680b6d647068861f7fd4e79d3f5");
        echo("result = " . $result. "\n");

        $result = Aep_subscribe_north::CreateSubscription("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "{}");
        echo("result = " . $result. "\n");


    }
}
