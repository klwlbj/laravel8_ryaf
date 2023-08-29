<?php

namespace App\Http\Controllers;

use App\Utils\CTWing;

class CTWingController extends BaseController
{
    public function queryDeviceEventList($productId, $deviceId)
    {
        $client = new CTWing();
        return $client->queryDeviceEventList($productId, $deviceId);
    }

    public function queryDeviceEventTotal($productId, $deviceId)
    {
        $client = new CTWing();
        return $client->queryDeviceEventTotal($productId, $deviceId);
    }

    public function queryCommandList($productId, $deviceId)
    {
        $client = new CTWing();
        return $client->queryCommandList($productId, $deviceId);
    }

    public function queryCommand($productId, $deviceId, $commandId)
    {
        $client = new CTWing();
        return $client->queryCommand($productId, $deviceId, $commandId);
    }

    public function cancelCommand($productId, $deviceId, $commandId)
    {
        $client = new CTWing();
        return $client->cancelCommand($productId, $deviceId, $commandId);
    }

    public function cancelAllCommand($productId, $deviceId)
    {
        $client = new CTWing();
        return $client->cancelAllCommand($productId, $deviceId);
    }

/*    public function createCommand($productId, $deviceId, $args)
    {
        $client = new CTWing();
        return $client->createCommand($productId, $deviceId, $args);
    }*/

    public function createCommandLwm2mProfile($productId, $deviceId, $command, $dwPackageNo)
    {
        $client = new CTWing();
        return $client->createCommandLwm2mProfile($productId, $deviceId, $command, $dwPackageNo);
    }

    public function getSubscriptionsList($productId, $pageNow, $pageSize)
    {
        $client = new CTWing();
        return $client->getSubscriptionsList($productId, $pageNow, $pageSize);
    }

    public function getSubscription($productId, $subId)
    {
        $client = new CTWing();
        return $client->getSubscription($productId, $subId);
    }

    public function deleteSubscription($productId, $subId, $subLevel)
    {
        $client = new CTWing();
        return $client->deleteSubscription($productId, $subId, $subLevel);
    }

    public function createSubscription($productId, $deviceId, $subUrl, $subLevel)
    {
        $client = new CTWing();
        return $client->createSubscription($productId, $deviceId, $subUrl, $subLevel);
    }
}
