<?php

namespace unglue\client\tests;

use luya\testsuite\cases\ConsoleApplicationTestCase;

abstract class ClientTestCase extends ConsoleApplicationTestCase
{
    public function getConfigArray()
    {
        return [
            'id' => 'unglueclient',
            'basePath' => dirname(__DIR__),
        ];
    }
}
