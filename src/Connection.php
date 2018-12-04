<?php

namespace fwcc\client;

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
        foreach ($this->config['scss'] as $scss) {
            $payload = [
                'mainFile' => $scss,
                'files' => $this->map,
                'options' => [
                    'sourcesMaps' => true,
                ]
            ];
            var_dump($payload);
            $json = json_encode($payload);
            $ch = curl_init('http://192.168.0.143:3000/compile/scss');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json))
            );
                                                                                                                                 
            $result = curl_exec($ch);
        }
    }
}