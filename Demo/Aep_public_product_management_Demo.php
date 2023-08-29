<?php
require_once 'Apis\Aep_public_product_management.php';

class Aep_public_product_management_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_public_product_management::QueryPublicByPublicProductId("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_public_product_management::QueryPublicByProductId("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_public_product_management::InstantiateProduct("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_public_product_management::QueryAllPublicProductList("dFI1lzE0EN2", "xQcjrfNLvQ");
        echo("result = " . $result. "\n");

        $result = Aep_public_product_management::QueryMyPublicProductList("dFI1lzE0EN2", "xQcjrfNLvQ");
        echo("result = " . $result. "\n");


    }
}
