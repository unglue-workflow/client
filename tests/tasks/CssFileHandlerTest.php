<?php

namespace unglue\client\tests\tasks;

use unglue\client\tests\ClientTestCase;
use unglue\client\controllers\CompileController;
use unglue\client\tasks\ConfigConnection;
use unglue\client\helpers\FileHelper;
use unglue\client\tasks\CssFileHandler;

class CssFileHandlerTest extends ClientTestCase
{
    public function testEmptyFiles()
    {
        $unglue = $this->createUnglueFile('cssfiles-noefiles.unglue', []);
        $ctrl = new CompileController('css-compile-controller', $this->app);
        $con = new ConfigConnection($unglue['source'], $unglue['folder'], $this->api, $ctrl);
        $js = new CssFileHandler($con);
        $this->assertFalse($js->handleUpload());
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
        $js = new CssFileHandler($con);
        $this->assertFalse($js->handleUpload());
    }
}
