<?php

namespace unglue\client\tasks;

use Curl\Curl;
use unglue\client\helpers\ConsoleHelper;

abstract class BaseFileHandler implements FilesMapInterface
{
    public $config;

    public function __construct(ConfigConnection $configConnection)
    {
        $this->config = $configConnection;
    }

    public function messagePrefix()
    {
        return $this->config->getUnglueConfigName() . " [".$this->name()."] ";
    }

    public function init()
    {

    }

    private $_map = [];

    public function addToMap($file)
    {
        if (is_file($file) && is_readable($file) && file_exists($file)) {
            $this->_map[] = ['file' => $file, 'filemtime' => filemtime($file)];
        }
    }

    public function getMap()
    {
        return $this->_map;
    }

    public function iterate($force)
    {
        if ($this->hasFileInMapChanged() || $force) {
            $this->handleUpload();
        }
    }

    public function count()
    {
        return count($this->_map);
    }

    public function hasFileInMapChanged()
    {
        clearstatcache();
        $hasChange = false;
        foreach ($this->getMap() as $key => $item) {
            $time = filemtime($item['file']);
            $ra = is_readable($item['file']);
            ConsoleHelper::infoMessage($this->messagePrefix() . ' watch ' . $item['file'] . ' - time: ' . $time . ' - readable: ' . var_export($ra, true));
            if ($time > $item['filemtime']) {
                ConsoleHelper::infoMessage($this->messagePrefix() . "file " .$item['file'] . " has changed.");
                $hasChange = true;
                $this->_map[$key]['filemtime'] = $time;
            }
            unset($time);
        }

        return $hasChange;
    }

    public function getFilesContent()
    {
        ConsoleHelper::startProgress(0, $this->count(), $this->messagePrefix() . "Collecting data ");
        $map = [];
        $i=1;
        foreach ($this->getMap() as $item) {
            ConsoleHelper::updateProgress($i++, $this->count());
            $map[] = [
                'file' => $item['file'],
                'code' => file_get_contents($item['file']),
            ];
        }
        unset($i);
        ConsoleHelper::endProgress();
        return $map;
    }

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