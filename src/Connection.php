<?php

namespace fwcc\client;

use Curl\Curl;


class Connection
{
    public $configFile;
    public $folder;

    public $config = [];

    public $map = [];

    public function __construct($configFile, $folder)
    {
        $this->configFile = $configFile;   
        $this->folder = $folder;
        
        $content = file_get_contents($configFile);
        
        $config = json_decode($content, true);

        $this->config = $config;
    }   

    public function generateMap($folder)
    {
        $files = FileHelper::findFiles($folder, 'scss');
        $map = [];
        foreach ($files as $name => $value) {
            if (is_file($name) && is_readable($name)) {
                $map[] = ['file' => $name, 'filemtime' => filemtime($name)];
            }
        }
        unset($files);
        return $map;
    }

    public function test()
    {
        $this->map = $this->generateMap($this->folder);
        $this->triggerBuild();
        return true;
    }

    public function iterate()
    {
        $hasChange = false;
        foreach ($this->map as $key => $item) {
            $time = filemtime($item['file']);
            if ($time > $item['filemtime']) {
                $hasChange = true;
                $this->map[$key]['filemtime'] = $time;
            }
            unset($time);
        }

        if ($hasChange) {
            $this->triggerBuild();
        }

        unset($hasChange);
    }

    public function triggerBuild()
    {
        $status = true;
        $dir = dirname($this->configFile);
        $css = null;
        foreach ($this->config['scss'] as $scss) {
            $map = [];
            foreach ($this->map as $file) {
                $map[] = [
                    'file' => $file['file'],
                    'content' => file_get_contents($file['file']),
                ];
            }

            $payload = [
                'mainFile' => $dir . DIRECTORY_SEPARATOR . $scss,
                'files' => $map,
                'options' => [
                    'sourcesMaps' => true,
                ]
            ];
            unset($map);
            $json = json_encode($payload);
            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Content-Length', strlen($json));
            $curl->post('http://192.168.0.143:3000/compile/scss', $json);

            $status = $curl->isSuccess();

            $response = json_decode($curl->response, true);

            $css .= $response['css'];
        }

        if ($status) {
            $cssFilePath = $dir . DIRECTORY_SEPARATOR . basename($this->configFile, '.fwcc') . '.css';
            file_put_contents($cssFilePath, $css);
        } else {
            echo "FEHLER: file nid schriebe!";
        }
    }
}