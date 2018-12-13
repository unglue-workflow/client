<?php

namespace unglue\client\tasks;

use Curl\Curl;
use yii\helpers\Console;
use yii\helpers\Json;
use unglue\client\helpers\FileHelper;
use unglue\client\helpers\ConsoleHelper;
use luya\console\Command;
use unglue\client\interfaces\ConnectionInterface;

/**
 * Represents a connection for an Unglue config file.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class ConfigConnection implements ConnectionInterface
{
    /**
     * @var string The path to the config file assigned while creating the connection object.
     */
    private $_configFile;
    
    /**
     * @var string The path to the watch folder assigned while creating the connection object.
     */
    private $_folder;

    /**
     * @var string The server to connection assigned while creating the connection object.
     */
    private $_server;

    /**
     * @var Command The command controller object.
     */
    private $_command;

    /**
     * @var JsFileHandler The javascript files handler object.
     */
    public $jsHandler;

    /**
     * @var CssFileHandler The css files handler object.
     */
    public $cssHandler;

    /**
     * Config Connection Constructuro
     *
     * @param string $configFile
     * @param string $folder
     * @param string $server
     * @param Command $command
     */
    public function __construct($configFile, $folder, $server, Command $command)
    {
        $this->_configFile = $configFile;
        $this->_folder = $folder;
        $this->_server = $server;
        $this->_command = $command;
    }

    /**
     * Get the context console command object
     *
     * @return Command
     */
    public function getCommand()
    {
        return $this->_command;
    }

    /**
     * Get the server url for the api.
     *
     * @return string
     */
    public function getServer()
    {
        return $this->_server;
    }

    /**
     * Get the aboslute path to the Unglue config ile.
     *
     * @return string
     */
    public function getConfigFile()
    {
        return $this->_configFile;
    }

    private $_config;

    /**
     * The unglue config file as array.
     *
     * @return array An array with key value pairing where keys can be:
     * - css
     * - js
     * - options
     */
    public function getUnglueConfig()
    {
        if ($this->_config === null) {
            $this->_config = Json::decode(file_get_contents($this->_configFile));
        }

        return $this->_config;
    }

    /**
     * The folder where all files are watched including subdirectories of the given folder
     *
     * @return string
     */
    public function getWatchFolder()
    {
        return $this->_folder;
    }

    /**
     * Get the section from an unglue config file
     *
     * @param string $section The section name like `css`, `js` or `options`.
     * @param mixed $defaultValue A value which is returned if the key is NOT found.
     * @return array
     */
    public function getHasUnglueConfigSection($section, $defaultValue = false)
    {
        $config = $this->getUnglueConfig();

        return isset($config[$section]) ? $config[$section] : $defaultValue;
    }

    /**
     * Get an absolute path for a file along to the unglue config file.
     *
     * This is mainly used to generate the dist files which are stored in the same place.
     *
     * @param string $name The name of the file to append to the unglue base folder path.
     * @return string
     */
    public function getUnglueConfigFolderPath($name)
    {
        return $this->getUnglueConfigFolder() . DIRECTORY_SEPARATOR . ltrim($name, DIRECTORY_SEPARATOR);
    }

    /**
     * Generate absolute path for a file which is the same as unglue config except of another extension name.
     *
     * @param string $extension The extension which should be used to generate the file.
     * @return string
     */
    public function getUnglueConfigFolderDistFilePath($extension)
    {
        return $this->getUnglueConfigFolder() . DIRECTORY_SEPARATOR . $this->getUnglueConfigFileBaseName() . '.'. $extension;
    }

    /**
     * Write a file next to the unglue config file (dist) with content and a given extension.
     *
     * @param string $content The content to write.
     * @param string $extension The extension
     * @return boolean
     */
    public function writeUnglueConfigFolderDistFile($content, $extension)
    {
        $file = $this->getUnglueConfigFolderDistFilePath($extension);
        $write = @file_put_contents($file, $content);
        if (!$write) {
            ConsoleHelper::errorMessage("Unable to write file '{$file}'.");
            return false;
        }

        return true;
    }

    /**
     * Get the base name of the unglue config file like `main` or `foobar` if the unglue config is `main.unglue`.
     *
     * @return string
     */
    public function getUnglueConfigFileBaseName()
    {
        return basename($this->_configFile, '.unglue');
    }

    /**
     * Get only the name of the unglue config file without folder/path.
     *
     * @return string
     */
    public function getUnglueConfigName()
    {
        return pathinfo($this->_configFile, PATHINFO_BASENAME);
    }

    /**
     * Get the folder where the unglue file is located without trailing slash.
     *
     * @return string
     */
    public function getUnglueConfigFolder()
    {
        return rtrim(dirname($this->_configFile), DIRECTORY_SEPARATOR);
    }

    /**
     * {@inheritDoc}
     */
    public function test()
    {
        $success = false;
        ConsoleHelper::infoMessage($this->getUnglueConfigName() . ': load and test (' . $this->getConfigFile().')');

        // test js connection
        $this->jsHandler = new JsFileHandler($this);
        $this->jsHandler->init();
        if ($this->jsHandler->count() > 0) {
            $success = true;
        }

        // test valid css connection
        $this->cssHandler = new CssFileHandler($this);
        $this->cssHandler->init();
        if ($this->cssHandler->count() > 0) {
            $success = true;
        }

        return $success;
    }

    /**
     * {@inheritDoc}
     */
    public function iterate($force = false)
    {
        if ($this->jsHandler->count() > 0) {
            $this->jsHandler->iterate($force);
        }

        if ($this->cssHandler->count() > 0) {
            $this->cssHandler->iterate($force);
        }

        // todo: maybe if there is an error - or both have errors return false.
        return true;
    }
}
