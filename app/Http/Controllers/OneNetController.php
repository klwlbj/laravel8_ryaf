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

    public function loadResource()
    {
        $client = new OneNet();
        return $client->loadResource();
    }

    public function cacheCommands()
    {
        $client = new OneNet();
        return $client->cacheCommands();
    }

    public function cacheCommand($uuid)
    {
        $client = new OneNet();
        return $client->cacheCommand($uuid);
    }

    public function cancelAllCacheCommand()
    {
        $client = new OneNet();
        return $client->cancelAllCacheCommand();
    }

    public function issueCacheCommand($args)
    {
        $client = new OneNet();
        return $client->issueCacheCommand($args);
    }

    public function writeResource($command, $dwPackageNo)
    {
        $client = new OneNet();
        return $client->writeResource($command, $dwPackageNo);
    }

    public function cancelCacheCommand($uuid)
    {
        $client = new OneNet();
        return $client->cancelCacheCommand($uuid);
    }

    public function logQuery($uuid)
    {
        $client = new OneNet();
        return $client->logQuery($uuid);
    }
}
