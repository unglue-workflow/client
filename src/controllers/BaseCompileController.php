<?php

namespace unglue\client\controllers;

use yii\console\Exception;
use luya\console\Command;
use unglue\client\helpers\FileHelper;
use unglue\client\tasks\ConfigConnection;

/**
 * Base Compile Controller.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class BaseCompileController extends Command
{
    /**
     * @var string The server the client should connect.
     */
    public $server = 'https://v1.api.unglue.io';

    /**
     * {@inheritDoc}
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'server',
        ]);
    }

    private $_folder;

    /**
     * Set current listening folder
     *
     * @param string|null $path A given path to listen oterhwise the current folder where the script is runing.
     */
    public function setFolder($path)
    {
        $this->_folder = $path ?  getcwd() . DIRECTORY_SEPARATOR . $path : getcwd();
    }

    /**
     * Get the current working folder.
     *
     * @return string An absolute path
     */
    public function getFolder()
    {
        return $this->_folder;
    }

    /**
     * Create connection objects where each config file represents a single connection.
     *
     * @return array An array with connection objects.
     */
    public function createConnections()
    {
        $folder = $this->getFolder();

        $unglues = FileHelper::findFiles($folder, 'unglue');

        if (count($unglues) == 0) {
            throw new Exception("Unable to find any .unglue files in '$folder' and subdirectories to start the compile listener.");
        }

        $connections = [];
        foreach ($unglues as $name => $file) {
            $con = new ConfigConnection($name, $folder, rtrim($this->server, '/'));
            if ($con->test()) {
                $con->iterate(true);
                $connections[] = $con;
            } else {
                unset($con);
            }
        }

        if (empty($connections)) {
            throw new Exception("No valid connection detected.");
        }

        return $connections;
    }
}
