<?php

namespace fwcc\client\tests;

use luya\testsuite\cases\ConsoleApplicationTestCase;

class ClientTestCase extends ConsoleApplicationTestCase
{
    public function getConfigArray()
    {
        return [
            'id' => 'fwccclient',
            'basePath' => dirname(__DIR__),
        ];
    }
}