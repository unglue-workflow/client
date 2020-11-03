<?php

namespace unglue\client\base;

use Curl\Curl;
use luya\helpers\StringHelper;
use unglue\client\helpers\ConsoleHelper;
use unglue\client\interfaces\FileHandlerInterface;
use unglue\client\tasks\ConfigConnection;
use unglue\client\helpers\FileHelper;

/**
 * Base class for File Handlers.
 *
 * This class implements common used methods when working with File Handlers.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
abstract class BaseFileHandler implements FileHandlerInterface
{
    /**
     * @var ConfigConnection Contains the config connection object from constructor.
     */
    private $_config;

    /**
     * {@inheritDoc}
     */
    public function __construct(ConfigConnection $configConnection)
    {
        $this->_config = $configConnection;
    }

    /**
     * Access the ConfigConnection object
     *
     * @return ConfigConnection
     * @since 1.4.0
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Prefix for output messages
     *
     * @param string $message The message which should contain the generic prefix.
     * @return string
     */
    public function messagePrefix($message = null)
    {
        return $this->getConfig()->getUnglueConfigName() . " [".$this->name()."] " . $message;
    }

    private $_map = [];

    /**
     * Add a file to the map array
     *
     * @param string $file The absolute path to a file.
     */
    public function addToMap($file)
    {
        $realFilePath = realpath($file);

        // make sure a file is not added to the map twice
        if (isset($this->_map[$realFilePath])) {
            return;
        }

        // check for symbolic links
        if (is_link($file)) {
            $file = readlink($file);
        }

        if (is_file($file) && is_readable($file) && file_exists($file)) {

            $this->_map[$realFilePath] = ['file' => $file, 'filemtime' => filemtime($file)];

            return true;
        }

        if ($this->getConfig()->getCommand()->verbose) {
            ConsoleHelper::infoMessage($this->messagePrefix("file {$file} is not readable."));
        }
    }

    /**
     * Remove a file from the map by its index.
     *
     * @param integer $file The file inside the array.
     * @since 1.1.1
     */
    public function removeFromMap($file)
    {
        unset($this->_map[$file]);
    }

    /**
     * Get all files in the current map array
     *
     * @return array
     */
    public function getMap()
    {
        return $this->_map;
    }

    /**
     * {@inheritDoc}
     */
    public function iterate($force)
    {
        if ($this->hasFileInMapChanged() || $force) {
            $this->handleUpload();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->_map);
    }

    /**
     * Check if the current map has file changes by compare filemtime.
     *
     * @return boolean Whether a file has changed since last check or not.
     */
    public function hasFileInMapChanged()
    {
        clearstatcache();
        $hasChange = false;
        foreach ($this->getMap() as $key => $item) {
            // check if the file exists on the file system, otherwise deleting a file would throw an exception.
            if (!file_exists($item['file'])) {
                $this->removeFromMap($key);
                $hasChange = true;
                break;
            }
            $time = filemtime($item['file']);
            if ($this->getConfig()->getCommand()->verbose) {
                ConsoleHelper::infoMessage($this->messagePrefix('watch ' . $item['file']));
            }

            if ($time > $item['filemtime']) {
                ConsoleHelper::infoMessage($this->messagePrefix("file " .$item['file'] . " has changed."));
                $hasChange = true;
                $this->_map[$key]['filemtime'] = $time;
                break;
            }
            unset($time);
        }

        return $hasChange;
    }

    /**
     * Get an array with file name and content.
     *
     * @return array
     */
    public function getFilesContent($relativ)
    {
        ConsoleHelper::startProgress(0, $this->count(), $this->messagePrefix("Collecting data "));
        $map = [];
        $i=1;
        foreach ($this->getMap() as $item) {
            ConsoleHelper::updateProgress($i++, $this->count());
            $map[] = [
                'relative' => FileHelper::relativePath($relativ, $item['file']),
                'file' => realpath($item['file']),
                'code' => file_get_contents($item['file']),
            ];
        }
        unset($i);
        ConsoleHelper::endProgress();
        return $map;
    }

    /**
     * Generate a request to the api endpoint with the given payload array.
     *
     * @param string $endpoint
     * @param array $payload
     * @return boolean
     */
    public function generateRequest($endpoint, array $payload)
    {
        $time = microtime(true);
        ConsoleHelper::infoMessage($this->messagePrefix("Send API request"));
        $payload['options'] = $this->getConfig()->getHasUnglueConfigSection('options', []);

        $json = json_encode($payload);
        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Content-Length', strlen($json));
        $curl->post($this->getConfig()->getServer() . $endpoint, $json);
        $response = json_decode($curl->response, true);
        $curl->close();

        if ($curl->curl_error) {
            ConsoleHelper::errorMessage($curl->curl_error_message . " (code $curl->curl_error_code)");
            return false;
        }

        if ($curl->isSuccess()) {
            ConsoleHelper::successMessage($this->messagePrefix("Compling done in " . round((microtime(true) - $time), 2) . "s"));
            return $response;
        }

        $message = (isset($response['message']) && !empty($response['message'])) ? $response['message']  : '';

        ConsoleHelper::errorMessage($message . $curl->error_message . " (code $curl->error_code)");

        return false;
    }
}
