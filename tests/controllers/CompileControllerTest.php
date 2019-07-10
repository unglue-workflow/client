<?php

namespace unglue\client\tests\controllers;

use unglue\client\tests\ClientTestCase;
use unglue\client\controllers\CompileController;

class CompileControllerTest extends ClientTestCase
{
    public function testIndexAction()
    {
        $ctrl = new CompileController('compile', $this->app);
        $this->assertSame([
            'verbose', 'interactive', 'server', 'exclude', 'symlinks', 'retry',
        ], $ctrl->options('index'));
        $this->assertSame(0, $ctrl->actionIndex());
    }

    public function testIndexActionWithFolderButEmptyConnection()
    {
        $ctrl = new CompileController('compile', $this->app);
        // reuires an unglue file without css or js defintion
    }
}
