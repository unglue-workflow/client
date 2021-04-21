<?php

namespace unglue\client\tests\base;

use unglue\client\tests\ClientTestCase;
use unglue\client\base\BaseCompileController;

class BaseCompileControllerTest extends ClientTestCase
{
    public function testEmptyConnectionsExceptions()
    {
        $unglue = $this->createUnglueFile('testdata/emptyconnections.unglue', ['bar' => 'foo']);

        $c = new TestBaseCompileController('base-test', $this->app);
        $c->setFolder($unglue['folder']);
        $this->assertStringContainsString('client/testdata', $c->getFolder());

        $this->expectException("yii\console\Exception");
        $c->createConnections();
    }
}

class TestBaseCompileController extends BaseCompileController
{
}
