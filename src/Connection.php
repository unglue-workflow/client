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

    public function generateMap($folder, $extension, $exclude = [])
    {
        $files = FileHelper::findFiles($folder, $extension);
        $map = [];
        foreach ($files as $name => $value) {
            if (in_array($name, $exclude)) {
                continue;
            }
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
            $this->jsMap = $this->generateMap($this->folder, 'js', [
                $this->createFwccFile('js'),
            ]);
        }

        return true;
    }

    public function createFwccFile($extension)
    {
        return $this->getFwccDir() . DIRECTORY_SEPARATOR . $this->getFwccFile() . '.'.$extension;
    }

    public function getFwccDir()
    {
        return dirname($this->configFile);
    }

    public function getFwccFile()
    {
        return basename($this->configFile, '.fwcc');
    }

    public function iterate($force = false)
    {
        $dir = $this->getFwccDir();
        $baseName = $this->getFwccFile();

        if ($this->getHasCssConfig() && ($this->findMapChange($this->scssMap) || $force)) {
            self::infoMessage($baseName . '.css compile request');
            $css = $this->getCssResponse($this->getHasCssConfig(), $this->scssMap, $dir);
            if ($css) {
                file_put_contents($dir . DIRECTORY_SEPARATOR . $baseName . '.css', $css);
                self::successMessage($baseName.'.css compiled');
            }
        }

        if ($this->getHasJsConfig() && ($this->findMapChange($this->jsMap) || $force)) {
            self::infoMessage($baseName . '.js compile request');
            $js = $this->getJsResponse($this->getHasJsConfig(), $this->jsMap, $dir);
            if ($js) {
                file_put_contents($dir . DIRECTORY_SEPARATOR . $baseName . '.js', $js);
                self::successMessage($baseName.'.js compiled');
            }
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

            $r = $this->generateRequest('http://ec2-52-50-217-165.eu-west-1.compute.amazonaws.com:3000/compile/scss', $payload);

            if ($r) {
                $content .= $r['css'];
            }
        }

        if (empty($content)) {
            return false;
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

        $r = $this->generateRequest('http://ec2-52-50-217-165.eu-west-1.compute.amazonaws.com:3000/compile/js', $payload);

        if ($r) {
            return $r['js'];
        }

        return false;
    }

    public function generateRequest($url, array $payload)
    {
        $json = json_encode($payload);
        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Content-Length', strlen($json));
        $curl->post($url, $json);
        $response = json_decode($curl->response, true);

        if ($curl->isSuccess()) {
            return $response;
        }

        $message = (isset($response['message']) && !empty($response['message'])) ? $response['message'] : $curl->error_message;

        return self::errorMessage($message);
    }

    public static function infoMessage($message)
    {
        echo "[".date("H:i:s")."] ". $message . PHP_EOL;
    }

    public static function errorMessage($message)
    {
        echo "[".date("H:i:s")."] Error: ". Console::ansiFormat($message, [Console::FG_RED]) . PHP_EOL;
        return false;
    }

    public static function successMessage($message)
    {
        echo "[".date("H:i:s")."] ". Console::ansiFormat($message, [Console::FG_GREEN]) . PHP_EOL;
        return true;
    }
}
