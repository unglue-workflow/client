<?php

namespace unglue\client\tests\helpers;

use unglue\client\tests\ClientTestCase;
use unglue\client\helpers\FileHelper;


class FileHelperTest extends ClientTestCase
{
    public function testFindUnglueFIles()
    {
        $files = FileHelper::findFilesByExtension($this->app->basePath, 'unglue', ['tests/']);
        $this->assertSame([], $files);
    }
}