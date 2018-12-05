<?php

namespace fwcc\client\tests\controllers;

use fwcc\client\tests\ClientTestCase;
use fwcc\client\controllers\CompileController;

class CompileControllerTest extends ClientTestCase
{
    public function testIndexAction()
    {
        $ctrl = new CompileController('compile', $this->app);

        $this->assertSame(0, $ctrl->actionIndex());
    }
}