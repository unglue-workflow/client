<?php

namespace unglue\client\tests\base;

use unglue\client\base\BaseFileHandler;
use unglue\client\tests\ClientTestCase;
use unglue\client\tasks\ConfigConnection;
use unglue\client\controllers\WatchController;
use unglue\client\helpers\FileHelper;

class BaseFileHandlerTest extends ClientTestCase
{
    /**
     * @covers \unglue\client\interfaces\FileHandlerInterface
     */
    public function testHasFileInMapChanged()
    {
        $ctrl = new WatchController('watch', $this->app);
        $ctrl->verbose = 1;
        $con = new ConfigConnection('barfoo', 'barfoo', 'barfoo', $ctrl);
        $f = new TestFileHandler($con);

        $temp = tempnam(sys_get_temp_dir(), 'barfoo');

        $f->addToMap($temp);
        $this->assertFalse($f->hasFileInMapChanged());
        sleep(1);
        $this->assertTrue(touch($temp));
        $this->assertTrue($f->hasFileInMapChanged());

        FileHelper::unlink($temp);
    }

    /**
     * Test index delete
     *
     * @see https://github.com/unglue-workflow/client/issues/9
     */
    public function testDeletedFileFromIndex()
    {
        $ctrl = new WatchController('watch', $this->app);
        $ctrl->verbose = 1;
        $con = new ConfigConnection('barfoo', 'barfoo', 'barfoo', $ctrl);
        $f = new TestFileHandler($con);

        $temp = tempnam(sys_get_temp_dir(), 'barfoodeleted');

        $f->addToMap($temp);
        $this->assertFalse($f->hasFileInMapChanged());
        sleep(1);
        FileHelper::unlink($temp);
        $this->assertTrue($f->hasFileInMapChanged());

        $this->assertTrue(empty($f->getMap()));
    }

    public function testFailingRequest()
    {
        $ctrl = new WatchController('watch', $this->app);
        $testUnglueRequst = $this->createUnglueFile('failingrequest.unglue', ['js' => ['none.js']]);
        $con = new ConfigConnection($testUnglueRequst['source'], 'barfoo', 'barfoo', $ctrl);
        $f = new TestFileHandler($con);
        $this->assertFalse($f->generateRequest('endpoint', ['pay' => 'load']));
    }
}


class TestFileHandler extends BaseFileHandler
{
    public function name()
    {
        return 'test';
    }

    public function handleUpload()
    {
    }

    public function init()
    {
    }
}
