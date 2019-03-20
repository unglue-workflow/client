<?php

namespace unglue\client\tests\tasks;

use unglue\client\tests\ClientTestCase;
use unglue\client\controllers\CompileController;
use unglue\client\tasks\ConfigConnection;
use unglue\client\tasks\CssFileHandler;
use luya\helpers\StringHelper;

class CssFileHandlerTest extends ClientTestCase
{
    public function testEmptyFiles()
    {
        $unglue = $this->createUnglueFile('cssfiles-noefiles.unglue', []);
        $ctrl = new CompileController('css-compile-controller', $this->app);
        $con = new ConfigConnection($unglue['source'], $unglue['folder'], $this->api, $ctrl);
        $css = new CssFileHandler($con);
        $this->assertFalse($css->handleUpload());
    }

    public function testFailingConnection()
    {
        $unglue = $this->createUnglueFile('cssfileserrorapi.unglue', [
            'css' => [
                'css.css',
            ]
        ], [
            'css.css' => '.red { color:red; }',
        ]);

        $ctrl = new CompileController('css-compile-controller', $this->app);
        $con = new ConfigConnection($unglue['source'], $unglue['folder'], 'localhost/unable/to/resolve', $ctrl);
        $con->test();
        $con->iterate(true);
        $css = new CssFileHandler($con);
        $this->assertFalse($css->handleUpload());
    }

    public function testCssMapInsteadOfScss()
    {
        $unglue = $this->createUnglueFile('cssfileserrorapi.unglue', [
            'css' => [
                'css.css',
            ]
        ], [
            'css.css' => '.red { color:red; }',
        ]);

        $ctrl = new CompileController('css-compile-controller', $this->app);
        $con = new ConfigConnection($unglue['source'], $unglue['folder'], 'localhost/unable/to/resolve', $ctrl);
        $con->test();
        $con->iterate(true);
        $css = new CssFileHandler($con);
        $css->init();

        $found = false;
        foreach ($css->getMap() as $key => $content) {
            if (StringHelper::contains('/css.css', $key)) {
                $found = true;
            }
        }

        $this->assertTrue($found);
    }
}
