<?php

namespace unglue\client\tests\controllers;

use unglue\client\tests\ClientTestCase;
use unglue\client\controllers\CompileController;

class CompileControllerTest extends ClientTestCase
{
    public function testIndexAction()
    {
        $ctrl = new CompileController('compile', $this->app);

        $this->assertSame(0, $ctrl->actionIndex());
    }
}
