<?php

namespace unglue\client\tests\tasks;

use unglue\client\tests\ClientTestCase;
use unglue\client\tasks\ConfigConnection;

class ConfigConnectionTest extends ClientTestCase
{
    public function testConnectionObjects()
    {
        $connection = new ConfigConnection(__DIR__.'/../data/output.unglue', __DIR__ .'/../', 'https://v1.api.unglue.io');
        $this->assertTrue($connection->test());
        $this->assertSame(2, $connection->jsConnection->count());
        $this->assertSame(1, $connection->cssConnection->count());

        $this->assertTrue($connection->iterate(true));
    }
    /*
    public function testCompiler()
    {
        $connection = new ConfigConnection(__DIR__.'/../data/output.unglue', __DIR__, 'localhost');
        $this->assertContains('../data', $connection->getunglueDir());
        $this->assertSame('output', $connection->getunglueFile());
        $this->assertContains('../data/output.js', $connection->createunglueFile('js'));

        $this->assertNotFalse($connection->getHasCssConfig());
        $this->assertNotFalse($connection->getHasJsConfig());
        $this->assertSame([
            "maps" => true
        ], $connection->getConfigOptions());
    }

    public function testIgnoreOfDistFiles()
    {
        $connection = new ConfigConnection(__DIR__.'/../data/output.unglue', __DIR__, 'localhost');

        $distUnglueFile = $connection->createunglueFile('js');
        $connection->test();

        0 => Array &1 (
        'file' => '/Users/basil/websites/client/tests/tasks/../data/input.js'
        'filemtime' => 1544288795
    )
    1 => Array &2 (
        'file' => '/Users/basil/websites/client/tests/tasks/../data/input2.js'
        'filemtime' => 1544288795
    )
        $this->assertSame(2, count($connection->jsMap));
        $this->assertFalse($connection->findMapChange($connection->jsMap));
    }
    */
}
