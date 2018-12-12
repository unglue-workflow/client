<?php

namespace unglue\client\base;

use Curl\Curl;
use unglue\client\helpers\ConsoleHelper;
use unglue\client\interfaces\FileHandlerInterface;
use unglue\client\tasks\ConfigConnection;

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
    protected $config;

    /**
     * {@inheritDoc}
     */
    public function __construct(ConfigConnection $configConnection)
    {
        $this->config = $configConnection;
    }

    /**
     * Prefix for output messages
     *
     * @return string
     */
    public function messagePrefix()
    {
        return $this->config->getUnglueConfigName() . " [".$this->name()."] ";
    }

    private $_map = [];

    /**
     * Add a file to the map array
     *
     * @param string $file The absolute path to a file.
     */
    public function addToMap($file)
    {
        if (is_file($file) && is_readable($file) && file_exists($file)) {
            $this->_map[] = ['file' => $file, 'filemtime' => filemtime($file)];
        }
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
            $time = filemtime($item['file']);
            $ra = is_readable($item['file']);
            
            if ($this->config->getCommand()->verbose) {
                ConsoleHelper::infoMessage($this->messagePrefix() . 'watch ' . $item['file']);
            }

            if ($time > $item['filemtime']) {
                ConsoleHelper::infoMessage($this->messagePrefix() . "file " .$item['file'] . " has changed.");
                $hasChange = true;
                $this->_map[$key]['filemtime'] = $time;
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
        ConsoleHelper::startProgress(0, $this->count(), $this->messagePrefix() . "Collecting data ");
        $map = [];
        $i=1;
        foreach ($this->getMap() as $item) {
            ConsoleHelper::updateProgress($i++, $this->count());
            $map[] = [
                'file' => $item['file'],
                'relative' => $this->relativePath($relativ, $item['file']),
                'code' => file_get_contents($item['file']),
            ];
        }
        unset($i);
        ConsoleHelper::endProgress();
        return $map;
    }

    public function relativePath($from, $to, $ps = DIRECTORY_SEPARATOR)
    {
        $arFrom = explode($ps, rtrim($from, $ps));
        $arTo = explode($ps, rtrim($to, $ps));
        while(count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0]))
        {
            array_shift($arFrom);
            array_shift($arTo);
        }
        return str_pad("", count($arFrom) * 3, '..'.$ps).implode($ps, $arTo);
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
        ConsoleHelper::infoMessage($this->messagePrefix() . "Send API request");
        $payload['options'] = $this->config->getHasUnglueConfigSection('options', []);

        $json = json_encode($payload);
        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Content-Length', strlen($json));
        $curl->post($this->config->getServer() . $endpoint, $json);
        $response = json_decode($curl->response, true);

        if ($curl->isSuccess()) {
            ConsoleHelper::successMessage($this->messagePrefix() . "Compling done in " . round((microtime(true) - $time), 2) . "s");
            return $response;
        }

        $message = (isset($response['message']) && !empty($response['message'])) ? $response['message'] : $curl->error_message;

        ConsoleHelper::errorMessage($message);

        return false;
    }
}
