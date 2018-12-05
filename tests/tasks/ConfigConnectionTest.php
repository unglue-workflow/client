<?php

namespace fwcc\client\tests\tasks;

use fwcc\client\tests\ClientTestCase;
use fwcc\client\tasks\ConfigConnection;


class ConfigConnectionTest extends ClientTestCase
{
    public function testCompiler()
    {
        $connection = new ConfigConnection(__DIR__.'/../data/output.fwcc', __DIR__);
        $this->assertContains('../data', $connection->getFwccDir());
        $this->assertSame('output', $connection->getFwccFile());
        $this->assertContains('../data/output.js', $connection->createFwccFile('js'));

        $this->assertNotFalse($connection->getHasCssConfig());
        $this->assertNotFalse($connection->getHasJsConfig());
        $this->assertSame([], $connection->getConfigOptions());
    }
}