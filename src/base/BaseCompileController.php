<?php

namespace unglue\client\base;

use yii\console\Exception;
use luya\console\Command;
use unglue\client\helpers\FileHelper;
use unglue\client\tasks\ConfigConnection;
use yii\helpers\StringHelper;

/**
 * Base Compile Controller.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
abstract class BaseCompileController extends Command
{
    /**
     * @var string The server the client should connect.
     */
    public $server = 'https://v1.api.unglue.io';

    /**
     * @var string A comma seperated list of paths or files following regex patterns to exclude when searching for unglue files. By default
     * it will exclude unglue files from `vendor/` and `public_html/assets/`.
     * @since 1.1.0
     */
    public $exclude = 'vendor/,public_html/assets/';

    /**
     * @var boolean Whether the system should follow sym links or not. As it may cause Problems for several reasons, this behavior is tunred of by default. The
     * symlinks option is mainly used to search for unglue files and for scss files.
     * @since 1.4.0
     */
    public $symlinks = false;

    /**
     * {@inheritDoc}
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'server', 'exclude', 'symlinks'
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
        $this->_folder = $path ? realpath($path) : getcwd();
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

        $this->verbosePrint("Exclude patterns: " . $this->exclude);

        $unglues = FileHelper::findFilesByExtension($folder, 'unglue', $this->getExcludeList(), [
            FileHelper::OPTION_FOLLOW_SYM_LINKS => $this->symlinks,
        ]);

        if (count($unglues) == 0) {
            throw new Exception("Unable to find any .unglue file in '$folder' and or any subdirectories.");
        }

        $connections = [];
        foreach ($unglues as $name => $file) {
            $con = new ConfigConnection($name, $folder, rtrim($this->server, '/'), $this);
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

    /**
     * Get an array with all folders to exclude
     *
     * @return array An array based from $this->exclude list.
     * @since 1.4.0
     */
    private function getExcludeList()
    {
        return StringHelper::explode($this->exclude, ',', true, true);
    }
}
