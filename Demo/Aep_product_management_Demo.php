<?php
require_once 'Apis\Aep_product_management.php';

class Aep_product_management_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_product_management::QueryProduct("dFI1lzE0EN2", "xQcjrfNLvQ", "10015488");
        echo("result = " . $result. "\n");

        $result = Aep_product_management::QueryProductList("dFI1lzE0EN2", "xQcjrfNLvQ");
        echo("result = " . $result. "\n");

        $result = Aep_product_management::DeleteProduct("dFI1lzE0EN2", "xQcjrfNLvQ", "cd35c680b6d647068861f7fd4e79d3f5", "10015488");
        echo("result = " . $result. "\n");

        $result = Aep_product_management::CreateProduct("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_product_management::UpdateProduct("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");


    }
}
