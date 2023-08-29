<?php
require_once 'Apis\Usr.php';

class Usr_Demo
{
    public static function Demo(){
        $result=null;

        $result = Usr::SdkDownload("dFI1lzE0EN2", "xQcjrfNLvQ", "", "");
        echo("result = " . $result. "\n");


    }
}
