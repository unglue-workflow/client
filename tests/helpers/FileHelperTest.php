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

    public function testFindFilesForWildcardPath()
    {
        $this->assertSame(['input.csv'], FileHelper::findFilesForWildcardPath('input.csv'));

        $prefix = $this->app->basePath . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;

        list ($f1, $f2) = FileHelper::findFilesForWildcardPath($prefix . 'js/*.js');

        $this->assertContains('b.js', $f1);
        $this->assertContains('a.js', $f2);

        $f = FileHelper::findFilesForWildcardPath($prefix . 'js/**'); // wil contain lib folder as well.

        $this->assertSame(3, count($f));

    }
}