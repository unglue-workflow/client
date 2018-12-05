<?php

namespace fwcc\client;

use Curl\Curl;
use yii\helpers\Console;


class Connection
{
    public $configFile;
    public $folder;
    public $config = [];
    public $scssMap = [];
    public $jsMap = [];

    public function __construct($configFile, $folder)
    {
        $this->configFile = $configFile;   
        $this->folder = $folder;
        $this->config = json_decode(file_get_contents($configFile), true);
    }   

    public function getHasCssConfig()
    {
        return isset($this->config['css']) ? $this->config['css'] : false;
    }

    public function getHasJsConfig()
    {
        return isset($this->config['js']) ? $this->config['js'] : false;
    }

    public function generateMap($folder, $extension)
    {
        $files = FileHelper::findFiles($folder, $extension);
        $map = [];
        foreach ($files as $name => $value) {
            if (is_file($name) && is_readable($name)) {
                $map[] = ['file' => $name, 'filemtime' => filemtime($name)];
            }
        }
        unset($files);
        return $map;
    }

    public function findMapChange(array &$map)
    {
        $hasChange = false;
        foreach ($map as $key => $item) {
            $time = filemtime($item['file']);
            if ($time > $item['filemtime']) {
                $hasChange = true;
                $map[$key]['filemtime'] = $time;
            }
            unset($time);
        }

        return $hasChange;
    }

    public function test()
    {
        if ($this->getHasCssConfig()) {
            $this->scssMap = $this->generateMap($this->folder, 'scss');
        }
        
        if ($this->getHasJsConfig()) {
            $this->jsMap = $this->generateMap($this->folder, 'js');
        }

        return true;
    }

    public function iterate()
    {
        $build = false;

        if ($this->getHasCssConfig() && $this->findMapChange($this->scssMap)) {
            $build = true;
        }

        if ($this->getHasJsConfig() && $this->findMapChange($this->jsMap)) {
            $build = true;
        }

        if ($build) {
            $this->triggerBuild();
        }
    }

    public function triggerBuild()
    {
        $dir = dirname($this->configFile);

        if ($this->getHasCssConfig()) {
            $css = $this->getCssResponse($this->getHasCssConfig(), $this->scssMap, $dir);
            file_put_contents($dir . DIRECTORY_SEPARATOR . basename($this->configFile, '.fwcc') . '.css', $css);
            echo Console::ansiFormat('CSS Compiled', [Console::FG_GREEN]);

        }

        if ($this->getHasJsConfig()) {
            $js = $this->getJsResponse($this->getHasJsConfig(), $this->jsMap, $dir);
            file_put_contents($dir . DIRECTORY_SEPARATOR . basename($this->configFile, '.fwcc') . '.js', $css);
            echo Console::ansiFormat('JS Compiled', [Console::FG_GREEN]);
        }
        
    }

    public function getCssResponse($config, array $maps, $dir)
    {
        $content = '';
        foreach ($config as $scss) {
            $map = [];
            foreach ($maps as $file) {
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

            $r = $this->generateRequest('http://192.168.0.143:3000/compile/scss', $payload);

            if ($r) {
                $content .= $r['css'];
            }
        }

        return $content;
    }

    public function getJsResponse($config, array $maps, $dir)
    {
        $map = [];
        foreach ($config as $js) {
            $p = $dir . DIRECTORY_SEPARATOR . $js;
            $map[] = [
                'file' => $p,
                'content' => file_get_contents($p),
            ];
        }

        $payload = [
            'files' => $map,
        ];

        $r = $this->generateRequest('http://192.168.0.143:3000/compile/js', $payload);

        if ($r) {
            return $r['js'];
        }
    }

    public function generateRequest($url, array $payload)
    {
        $json = json_encode($payload);
        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Content-Length', strlen($json));
        $curl->post($url, $json);

        if ($curl->isSuccess()) {
            return json_decode($curl->response, true);
        }

        var_dump($curl->response);
        echo Console::ansiFormat($curl->error_message, [Console::FG_RED]);
    }
}