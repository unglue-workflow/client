<?php

namespace unglue\client\tests\tasks;

use unglue\client\tests\ClientTestCase;
use unglue\client\tasks\ConfigConnection;

class ConfigConnectionTest extends ClientTestCase
{
    public function testCompiler()
    {
        $connection = new ConfigConnection(__DIR__.'/../data/output.unglue', __DIR__, 'localhost');
        $this->assertContains('../data', $connection->getunglueDir());
        $this->assertSame('output', $connection->getunglueFile());
        $this->assertContains('../data/output.js', $connection->createunglueFile('js'));

        $this->assertNotFalse($connection->getHasCssConfig());
        $this->assertNotFalse($connection->getHasJsConfig());
        $this->assertSame([], $connection->getConfigOptions());
    }
}
