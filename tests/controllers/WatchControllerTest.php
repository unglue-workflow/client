<?php

namespace unglue\client\tests\controllers;

use unglue\client\tests\ClientTestCase;
use unglue\client\controllers\WatchController;

class WatchControllerTest extends ClientTestCase
{
    public function testIndexAction()
    {
        $ctrl = new WatchController('watch-controller', $this->app);

        $this->expectException("yii\console\Exception");
        $ctrl->actionIndex('/does/not/exists');
    }
}