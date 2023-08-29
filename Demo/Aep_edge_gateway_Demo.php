<?php
require_once 'Apis\Aep_edge_gateway.php';

class Aep_edge_gateway_Demo
{
    public static function Demo(){
        $result=null;

        $result = Aep_edge_gateway::DeleteEdgeInstanceDevice("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_edge_gateway::QueryEdgeInstanceDevice("dFI1lzE0EN2", "xQcjrfNLvQ", "");
        echo("result = " . $result. "\n");

        $result = Aep_edge_gateway::CreateEdgeInstance("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_edge_gateway::EdgeInstanceDeploy("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_edge_gateway::DeleteEdgeInstance("dFI1lzE0EN2", "xQcjrfNLvQ", "");
        echo("result = " . $result. "\n");

        $result = Aep_edge_gateway::AddEdgeInstanceDevice("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");

        $result = Aep_edge_gateway::AddEdgeInstanceDrive("dFI1lzE0EN2", "xQcjrfNLvQ", "{}");
        echo("result = " . $result. "\n");


    }
}
