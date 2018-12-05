<?php

namespace fwcc\client;

use luya\console\Command;


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
        $fwccs = FileHelper::findFiles($folder, 'fwcc');

        if (count($fwccs) == 0) {
            return $this->outputError("Unable to find any .fwcc files in '$folder' and subdirectories to start the compile listener.");
        }

        foreach ($fwccs as $name => $file) {
            $con =  new Connection($name, $folder);
            if ($con->test()) {
                $con->triggerBuild();
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