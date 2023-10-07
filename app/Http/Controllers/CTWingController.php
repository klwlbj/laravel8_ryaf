<?php

namespace App\Http\Controllers;

use App\Utils\CTWing;

class CTWingController extends BaseController
{
    public function queryDeviceEventList($productId, $deviceId, $masterKey)
    {
        $client = new CTWing();
        return $client->queryDeviceEventList($productId, $deviceId, $masterKey);
    }

    public function queryDeviceEventTotal($productId, $deviceId, $masterKey)
    {
        $client = new CTWing();
        return $client->queryDeviceEventTotal($productId, $deviceId, $masterKey);
    }

    public function queryCommandList($productId, $deviceId, $masterKey)
    {
        $client = new CTWing();
        return $client->queryCommandList($productId, $deviceId, $masterKey);
    }

    public function queryCommand($productId, $deviceId, $masterKey, $commandId)
    {
        $client = new CTWing();
        return $client->queryCommand($productId, $deviceId, $masterKey, $commandId);
    }

    public function cancelCommand($productId, $deviceId, $masterKey, $commandId)
    {
        $client = new CTWing();
        return $client->cancelCommand($productId, $deviceId, $masterKey, $commandId);
    }

    public function cancelAllCommand($productId, $deviceId, $masterKey)
    {
        $client = new CTWing();
        return $client->cancelAllCommand($productId, $deviceId, $masterKey);
    }

    public function createCommand($productId, $deviceId, $masterKey, $command, $dwPackageNo)
    {
        $client = new CTWing();
        return $client->createCommand($productId, $deviceId, $masterKey, $command, $dwPackageNo);
    }

    public function createCommandLwm2mProfile($productId, $deviceId, $masterKey, $command, $dwPackageNo)
    {
        $client = new CTWing();
        return $client->createCommandLwm2mProfile($productId, $deviceId, $masterKey, $command, $dwPackageNo);
    }

    public function createMicrowaveSettingCommand($productId, $deviceId, $masterKey)
    {
        $client = new CTWing();
        return $client->createMicrowaveSettingCommand($productId, $deviceId, $masterKey);
    }

    public function createGasSettingCommand($productId, $deviceId, $masterKey, $gasAlarmCorrection = 0)
    {
        $client = new CTWing();
        return $client->createGasSettingCommand($productId, $deviceId, $masterKey, $gasAlarmCorrection);
    }

    public function getSubscriptionsList($productId, $masterKey, $pageNow, $pageSize)
    {
        $client = new CTWing();
        return $client->getSubscriptionsList($productId, $masterKey, $pageNow, $pageSize);
    }

    public function getSubscription($productId, $masterKey, $subId)
    {
        $client = new CTWing();
        return $client->getSubscription($productId, $masterKey, $subId);
    }

    public function deleteSubscription($productId, $masterKey, $subId, $subLevel)
    {
        $client = new CTWing();
        return $client->deleteSubscription($productId, $masterKey, $subId, $subLevel);
    }

    public function createSubscription($productId, $deviceId, $masterKey, $subUrl, $subLevel)
    {
        $client = new CTWing();
        return $client->createSubscription($productId, $deviceId, $masterKey, $subUrl, $subLevel);
    }
}
