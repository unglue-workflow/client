<?php

namespace unglue\client\tests\tasks;

use unglue\client\tests\ClientTestCase;
use unglue\client\tasks\ConfigConnection;
use unglue\client\controllers\CompileController;
use unglue\client\helpers\FileHelper;

class ConfigConnectionTest extends ClientTestCase
{
    /**
     * @covers \unglue\client\interfaces\ConnectionInterface
     */
    public function testConnectionObjects()
    {
        $ctrl = new CompileController('compile-controller', $this->app);

        $connection = new ConfigConnection(__DIR__.'/../data/output.unglue', __DIR__ .'/../', $this->api, $ctrl);

        $this->assertFalse($connection->writeUnglueConfigFolderDistFile(false, 'null'));

        $this->assertTrue($connection->test());

        $this->assertSame(2, $connection->handlers['unglue\client\tasks\JsFileHandler']->count());
        $this->assertSame(1, $connection->handlers['unglue\client\tasks\CssFileHandler']->count());

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

        $connection = new ConfigConnection($unglue['source'], $unglue['folder'], $this->api, $ctrl);
        $connection->test();
        $connection->iterate(true);

        $distCss = $unglue['folder'] . 'mytest.css';
        $distJs = $unglue['folder'] . 'mytest.js';

        $this->assertSame('.class{color:red}
', file_get_contents($distCss));
        $this->assertSame('"use strict";function hello(l){console.log(hello)}', file_get_contents($distJs));
    }


    public function testCreateUnglueFileWithOptions()
    {
        $unglue = $this->createUnglueFile('mytest.unglue', [
            'js' => ['foobar.js'],
            'css' => ['barfoo.scss'],
            'options' => [
                'compress' => false,
                'maps' => true,
            ]
        ], [
            'foobar.js' => 'function hello(say) { console.log(hello); }',
            'barfoo.scss' => '.class { color:red; }',
        ]);

        $ctrl = new CompileController('compile-controller', $this->app);

        $connection = new ConfigConnection($unglue['source'], $unglue['folder'], $this->api, $ctrl);
        $connection->test();
        $connection->iterate(true);

        $distCss = $unglue['folder'] . 'mytest.css';
        $distJs = $unglue['folder'] . 'mytest.js';
        $this->assertSame('.class {
  color: red;
}
/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImJhcmZvby5zY3NzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0VBQVMsV0FBUztDQUFJIiwiZmlsZSI6Im15dGVzdC5jc3MiLCJzb3VyY2VzQ29udGVudCI6WyIuY2xhc3MgeyBjb2xvcjpyZWQ7IH0iXX0= */', file_get_contents($distCss));

        $this->assertSame('"use strict";

function hello(say) {
  console.log(hello);
}//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImZvb2Jhci5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiOztBQUFBLFNBQUEsS0FBQSxDQUFBLEdBQUEsRUFBQTtBQUFBLFVBQUEsR0FBQSxDQUFBLEtBQUE7QUFBQSIsInNvdXJjZXNDb250ZW50IjpbImZ1bmN0aW9uIGhlbGxvKHNheSkge8KgY29uc29sZS5sb2coaGVsbG8pOyB9Il19', file_get_contents($distJs));
    }
}
