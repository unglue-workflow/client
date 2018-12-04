<?php

namespace fwcc\client;

use luya\console\Command;

class ListenController extends Command
{
    public $extension;

    public $connections = [];

    public function actionIndex($path = null)
    {
        $folder = $path ?  getcwd() . DIRECTORY_SEPARATOR . $path : getcwd();

        $fwccs = FileHelper::findFiles($folder, 'fwcc');

        if (count($fwccs) == 0) {
            return $this->outputError("Unable to find any .fwcc files in '$folder' and subdirectories to start the compile listener.");
        }

        foreach ($fwccs as $name => $file) {
            $con =  new Connection($name, $folder);
            if ($con->test()) {
                $this->connections[] = $con;
            } else {
                unset($con);
            }
        }

        if (empty($this->connections)) {
            return $this->outputError("Not valid connection detected.");
        }

        while (true) {
            foreach ($this->connections as $con) {
                $con->iterate();
            }

            //$this->outputInfo(microtime(true) . " [".memory_get_usage()."] - Stack");
            gc_collect_cycles();
            usleep(300000);
        }
    }
}