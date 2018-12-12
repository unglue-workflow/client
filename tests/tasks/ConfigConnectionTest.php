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
        $distCssMap = $unglue['folder'] . 'mytest.css.map';
        $distJs = $unglue['folder'] . 'mytest.js';
        $distJsMap = $unglue['folder'] . 'mytest.js.map';
$this->assertContains('.class {
  color: red;
}
/*# sourceMappingURL=', file_get_contents($distCss));
        $this->assertContains('mytest.css.map', file_get_contents($distCss));
        $this->assertSame('{"version":3,"sources":["barfoo.scss"],"names":[],"mappings":"AAAA;EACE,WAAW;CACZ","file":"mytest.css","sourcesContent":[".class {\n  color: red;\n}\n\n/*# sourceMappingURL=mytest.css.map */"]}', file_get_contents($distCssMap));
        $this->assertContains('"use strict";

function hello(say) {
  console.log(hello);
}
//# sourceMappingURL=', file_get_contents($distJs));
        $this->assertContains('mytest.js.map', file_get_contents($distJs));
        $this->assertContains('foobar.js"],"names":[],"mappings":";;AAAA,SAAA,KAAA,CAAA,GAAA,EAAA;AAAA,UAAA,GAAA,CAAA,KAAA;AAAA","sourcesContent":["function hello(say) { console.log(hello); }"]}', file_get_contents($distJsMap));
    
        
    }
    
}
