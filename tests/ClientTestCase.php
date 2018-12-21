<?php

namespace unglue\client\tests;

use luya\testsuite\cases\ConsoleApplicationTestCase;
use unglue\client\helpers\FileHelper;

abstract class ClientTestCase extends ConsoleApplicationTestCase
{
    public $unglueFiles = [];

    public $api = 'https://v1.api.unglue.io';

    public function getConfigArray()
    {
        return [
            'id' => 'unglueclient',
            'basePath' => dirname(__DIR__),
        ];
    }

    public function createUnglueFile($name, array $content, array $files = [])
    {
        $source = getcwd() . DIRECTORY_SEPARATOR . $name;


        $folder = FileHelper::createDirectory(dirname($source));
        $r = FileHelper::writeFile($source, json_encode($content));

        $map = [];
        foreach ($files as $file => $content) {
            $nf = getcwd() . DIRECTORY_SEPARATOR . $file;
            $g = FileHelper::writeFile($nf, $content);
            $map[]  = [
                'name' => $name,
                'source' => $nf,
                'content' => $content,
            ];
        }

        $unglue = [
            'name' => $name,
            'source' => $source,
            'content' => $content,
            'folder' => dirname($source) . DIRECTORY_SEPARATOR,
            'files' => $map,
            'distName' => basename($name, '.unglue'),
        ];

        $this->unglueFiles[] = $unglue;

        return $unglue;
    }

    public function beforeTearDown()
    {
        parent::beforeTearDown();

        foreach ($this->unglueFiles as $file) {
            $r = FileHelper::unlink($file['source']);
            FileHelper::unlink($file['distName'].'.css');
            FileHelper::unlink($file['distName'].'.css.map');
            FileHelper::unlink($file['distName'].'.js');
            FileHelper::unlink($file['distName'].'.js.map');
            foreach ($file['files'] as $a) {
                FileHelper::unlink($a['source']);
            }
        }
    }
}
