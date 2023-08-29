<?php
require_once 'Apis\Aep_mq_sub.php';

class Aep_mq_sub_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_mq_sub::QueryServiceState("dFI1lzE0EN2", "xQcjrfNLvQ");
        echo("result = " . $result. "\n");

        $result = Aep_mq_sub::OpenMqService("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_mq_sub::QueryTopicInfo("dFI1lzE0EN2", "xQcjrfNLvQ", "");
        echo("result = " . $result. "\n");

        $result = Aep_mq_sub::QueryTopicCacheInfo("dFI1lzE0EN2", "xQcjrfNLvQ", "");
        echo("result = " . $result. "\n");

        $result = Aep_mq_sub::QueryTopics("dFI1lzE0EN2", "xQcjrfNLvQ");
        echo("result = " . $result. "\n");

        $result = Aep_mq_sub::QuerySubRules("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_mq_sub::ClosePushService("dFI1lzE0EN2", "xQcjrfNLvQ");
        echo("result = " . $result. "\n");


    }
}
