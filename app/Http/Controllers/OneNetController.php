<?php

namespace App\Http\Controllers;

use App\Utils\OneNet;

class OneNetController extends BaseController
{
    public function echoSign()
    {
        $client = new OneNet();
        echo $client->getSign();
    }

    public function loadResource($imei)
    {
        $client = new OneNet();
        return $client->loadResource($imei);
    }

    public function cacheCommands($imei)
    {
        $client = new OneNet();
        return $client->cacheCommands($imei);
    }

    public function cacheCommand($imei, $uuid)
    {
        $client = new OneNet();
        return $client->cacheCommand($imei, $uuid);
    }

    public function cancelAllCacheCommand($imei)
    {
        $client = new OneNet();
        return $client->cancelAllCacheCommand($imei);
    }

    public function issueCacheCommand($imei, $args, $dwPackageNo)
    {
        $client = new OneNet();
        return $client->issueCacheCommand($imei, $args, $dwPackageNo);
    }

    public function createGasSettingCommand($imei, $gasAlarmCorrection)
    {
        $client = new OneNet();
        return $client->createGasSettingCommand($imei, $gasAlarmCorrection);
    }

    public function customWriteResource($imei, $command, $dwPackageNo)
    {
        $client = new OneNet();
        return $client->customWriteResource($imei, $command, $dwPackageNo);
    }

    public function writeResource($imei, $cmd)
    {
        $client = new OneNet();
        return $client->writeResource($imei,$cmd);
    }

    public function execute($imei, $command, $dwPackageNo)
    {
        $client = new OneNet();
        return $client->execute($imei, $command, $dwPackageNo);
    }
    public function realTimewriteResource($imei, $command, $dwPackageNo)
    {
        $client = new OneNet();
        return $client->realTimewriteResource($imei, $command, $dwPackageNo);

    }

    public function cancelCacheCommand($imei, $uuid)
    {
        $client = new OneNet();
        return $client->cancelCacheCommand($imei, $uuid);
    }

    public function logQuery($imei, $uuid)
    {
        $client = new OneNet();
        return $client->logQuery($imei, $uuid);
    }

    public function deviceInfo($projectId,$imei)
    {
        $client = new OneNet();
        return $client->deviceInfo($projectId,$imei);
    }
}
