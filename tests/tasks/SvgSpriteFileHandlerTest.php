<?php

namespace unglue\client\tests\tasks;

use unglue\client\tests\ClientTestCase;
use unglue\client\controllers\CompileController;
use unglue\client\tasks\ConfigConnection;
use unglue\client\helpers\FileHelper;
use unglue\client\tasks\SvgSpriteFileHandler;

class SvgSpriteFileHandlerTest extends ClientTestCase
{
    public function testSpriteResponse()
    {
        $unglue = $this->createUnglueFile('svgsprite.unglue', [
            'svg' => [
                'svg1.svg',
                'svg2.svg',
            ]
        ], [
            'svg1.svg' => '<svg aria-hidden="true" data-prefix="fas" data-icon="angle-down" class="svg-inline--fa fa-angle-down fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z"></path></svg>',
            'svg2.svg' => '<svg aria-hidden="true" data-prefix="fas" data-icon="angle-down" class="svg-inline--fa fa-angle-down fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z"></path></svg>',
        ]);

        $ctrl = new CompileController('svg-compile-controller', $this->app);
        $connection = new ConfigConnection($unglue['source'], $unglue['folder'], $this->api, $ctrl);
        $connection->test();
        $connection->iterate(true);

        $sprite = $unglue['folder'] . 'svgsprite.svg';

        $this->assertSame('<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs/><symbol id="svg1" viewBox="0 0 320 512" role="img"><path fill="currentColor" d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z"/></symbol><symbol id="svg2" viewBox="0 0 320 512" role="img"><path fill="currentColor" d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z"/></symbol></svg>', file_get_contents($sprite));
    
        FileHelper::unlink($sprite);
    }

    public function testEmptyFiles()
    {
        $unglue = $this->createUnglueFile('svgsprite-noefiles.unglue', []);
        $ctrl = new CompileController('svg-compile-controller', $this->app);
        $con = new ConfigConnection($unglue['source'], $unglue['folder'], $this->api, $ctrl);
        $svg = new SvgSpriteFileHandler($con);
        $this->assertFalse($svg->handleUpload());
    }

    public function testFailingConnection()
    {
        $unglue = $this->createUnglueFile('svgsprite-noefiles.unglue', [
            'svg' => [
                'svg1.svg',
            ]
        ], [
            'svg1.svg' => '<svg></scv>'
        ]);
        $ctrl = new CompileController('svg-compile-controller', $this->app);
        $con = new ConfigConnection($unglue['source'], $unglue['folder'], 'localhost/not/found', $ctrl);
        $con->test();
        $con->iterate(true);
        $svg = new SvgSpriteFileHandler($con);
        $this->assertFalse($svg->handleUpload());
    }
}
