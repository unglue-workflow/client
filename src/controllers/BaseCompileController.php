<?php

namespace unglue\client\controllers;

use luya\console\Command;
use unglue\client\helpers\FileHelper;
use unglue\client\tasks\ConfigConnection;


class BaseCompileController extends Command
{
    public $connections = [];

    private $_folder;

    public function setFolder($path)
    {
        $this->_folder = $path ?  getcwd() . DIRECTORY_SEPARATOR . $path : getcwd();
    }

    public function getFolder()
    {
        return $this->_folder;
    }

    public function initConfigsAndTest()
    {
        $folder = $this->getFolder();
        $unglues = FileHelper::findFiles($folder, 'unglue');

        if (count($unglues) == 0) {
            return $this->outputError("Unable to find any .unglue files in '$folder' and subdirectories to start the compile listener.");
        }

        foreach ($unglues as $name => $file) {
            $con = new ConfigConnection($name, $folder);
            if ($con->test()) {
                $con->iterate(true);
                $this->connections[] = $con;
            } else {
                unset($con);
            }
        }

        if (empty($this->connections)) {
            return $this->outputError("Not valid connection detected.");
        }
    }
}