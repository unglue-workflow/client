<?php

namespace unglue\client\tests\tasks;

use unglue\client\tests\ClientTestCase;
use unglue\client\controllers\CompileController;
use unglue\client\tasks\ConfigConnection;
use unglue\client\helpers\FileHelper;
use unglue\client\tasks\JsFileHandler;

class JsFileHandlerTest extends ClientTestCase
{
    public function testEmptyFiles()
    {
        $unglue = $this->createUnglueFile('jsfiles-noefiles.unglue', []);
        $ctrl = new CompileController('js-compile-controller', $this->app);
        $con = new ConfigConnection($unglue['source'], $unglue['folder'], $this->api, $ctrl);
        $js = new JsFileHandler($con);
        $this->assertFalse($js->handleUpload());
    }

    public function testFailingConnection()
    {
        $unglue = $this->createUnglueFile('jsfileserrorapi.unglue', [
            'js' => [
                'js1.js',
            ]
        ], [
            'js1.js' => 'console.log(0)',
        ]);

        $ctrl = new CompileController('js-compile-controller', $this->app);
        $con = new ConfigConnection($unglue['source'], $unglue['folder'], 'localhost/unable/to/resolve', $ctrl);
        $con->test();
        $con->iterate(true);
        $js = new JsFileHandler($con);
        $this->assertFalse($js->handleUpload());
    }

    public function testMultipleJsFileByWildcard()
    {
        $folder = $this->app->basePath . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;

        $unglue = $this->createUnglueFile('jsfileswithwildcard.unglue', [
            'js' => [
                'tests/data/js/*.js',
            ]
        ]);

        $ctrl = new CompileController('js-compile-controller', $this->app);
        $ctrl->verbose = 1;
        $con = new ConfigConnection($unglue['source'], $unglue['folder'], $this->api, $ctrl);
        $con->test();
        $con->iterate(true);
        $js = new JsFileHandler($con);
        $r = $js->handleUpload();


        $result = $unglue['folder'] . $unglue['distName']. '.js';
        $content = file_get_contents($result);
        $this->assertStringContainsString('console.log("b")', $content);
        $this->assertStringContainsString('console.log("a")', $content);
    }
}
