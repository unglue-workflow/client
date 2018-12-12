<?php

namespace unglue\client\tests\tasks;

use unglue\client\tests\ClientTestCase;
use unglue\client\tasks\ConfigConnection;
use unglue\client\controllers\CompileController;
use unglue\client\helpers\FileHelper;

class ConfigConnectionTest extends ClientTestCase
{
    public function testConnectionObjects()
    {
        $ctrl = new CompileController('compile-controller', $this->app);

        $connection = new ConfigConnection(__DIR__.'/../data/output.unglue', __DIR__ .'/../', 'https://v1.api.unglue.io', $ctrl);
        $this->assertTrue($connection->test());
        $this->assertSame(2, $connection->jsHandler->count());
        $this->assertSame(1, $connection->cssHandler->count());

        $this->assertTrue($connection->iterate(true));
    }

    public function testCreateUnglueFileWithoutOptions()
    {
        $unglue = $this->createUnglueFile('mytest.unglue', [
            'js' => ['foobar.js'],
            'css' => ['barfoo.scss']
        ], [
            'foobar.js' => 'function hello(say) { console.log(hello); }',
            'barfoo.scss' => '.class { color:red; }',
        ]);

        $ctrl = new CompileController('compile-controller', $this->app);

        $connection = new ConfigConnection($unglue['source'], $unglue['folder'], 'https://v1.api.unglue.io', $ctrl);
        $connection->test();
        $connection->iterate(true);

        $distCss = $unglue['folder'] . 'mytest.css';
        $distJs = $unglue['folder'] . 'mytest.js';

        $this->assertSameNoSpace('.class{color:red}', file_get_contents($distCss));
        $this->assertSame('"use strict";function hello(l){console.log(hello)}', file_get_contents($distJs));
    }
}
