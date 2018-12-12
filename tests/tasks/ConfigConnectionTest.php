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

        $connection = new ConfigConnection($unglue['source'], $unglue['folder'], 'https://v1.api.unglue.io', $ctrl);
        $connection->test();
        $connection->iterate(true);

        $distCss = $unglue['folder'] . 'mytest.css';
        $distJs = $unglue['folder'] . 'mytest.js';
$this->assertSame('.class {
  color: red;
}
/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIi9Vc2Vycy9iYXNpbC93ZWJzaXRlcy9jbGllbnQvYmFyZm9vLnNjc3MiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7RUFBUyxXQUFTO0NBQUkiLCJmaWxlIjoidG8uY3NzIiwic291cmNlc0NvbnRlbnQiOlsiLmNsYXNzIHsgY29sb3I6cmVkOyB9Il19 */', file_get_contents($distCss));

$this->assertSame('"use strict";

function hello(say) {
  console.log(hello);
}//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIi9Vc2Vycy9iYXNpbC93ZWJzaXRlcy9jbGllbnQvZm9vYmFyLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7O0FBQUEsU0FBQSxLQUFBLENBQUEsR0FBQSxFQUFBO0FBQUEsVUFBQSxHQUFBLENBQUEsS0FBQTtBQUFBIiwic291cmNlc0NvbnRlbnQiOlsiZnVuY3Rpb24gaGVsbG8oc2F5KSB7wqBjb25zb2xlLmxvZyhoZWxsbyk7IH0iXX0=', file_get_contents($distJs));
        
    }
    
}
