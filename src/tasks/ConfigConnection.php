<?php

namespace unglue\client\tasks;

use Curl\Curl;
use yii\helpers\Console;
use yii\helpers\Json;
use unglue\client\helpers\FileHelper;
use unglue\client\helpers\ConsoleHelper;

class ConfigConnection
{
    private $_configFile;
    private $_folder;
    private $_server;

    public $jsConnection;
    public $cssConnection;

    public function __construct($configFile, $folder, $server)
    {
        $this->_configFile = $configFile;
        $this->_folder = $folder;
        $this->_server = $server;
    }

    public function getServer()
    {
        return $this->_server;
    }

    private $_config;

    public function getConfigFile()
    {
        return $this->_configFile;
    }

    public function getUnglueConfig()
    {
        if ($this->_config === null) {

            $this->_config = Json::decode(file_get_contents($this->_configFile));
        }

        return $this->_config;
    }

    public function getWatchFolder()
    {
        return $this->_folder;
    }

    public function getHasUnglueConfigSection($section, $defaultValue = false)
    {
        $config = $this->getUnglueConfig();

        return isset($config[$section]) ? $config[$section] : $defaultValue;
    }

    public function getUnglueConfigFolderPath($name)
    {
        return $this->getUnglueConfigFolder() . DIRECTORY_SEPARATOR . ltrim($name, DIRECTORY_SEPARATOR);
    }

    public function getUnglueConfigFolderDistFilePath($extension)
    {
        return $this->getUnglueConfigFolder() . DIRECTORY_SEPARATOR . $this->getUnglueConfigFileBaseName() . '.'. $extension;
    }

    public function writeUnglueConfigFolderDistFile($content, $extension)
    {
        return FileHelper::writeFile($this->getUnglueConfigFolderDistFilePath($extension), $content);
    }

    public function getUnglueConfigFileBaseName()
    {
        return basename($this->_configFile, '.unglue');
    }

    public function getUnglueConfigName()
    {
        return pathinfo($this->_configFile, PATHINFO_BASENAME);
    }

    public function getUnglueConfigFolder()
    {
        return rtrim(dirname($this->_configFile), DIRECTORY_SEPARATOR);
    }

    /*
    public function createunglueFile($extension)
    {
        return $this->getUnglueDir() . DIRECTORY_SEPARATOR . $this->getUnglueFile() . '.'. $extension;
    }

    public function getUnglueDir()
    {
        return rtrim(dirname($this->configFile), '/');
    }

    public function getUnglueFile()
    {
        return basename($this->configFile, '.unglue');
    }
    */

    public function test()
    {
        $success = false;
        ConsoleHelper::infoMessage($this->getUnglueConfigName() . ': load and test (' . $this->config->getConfigFile().')');

        // test js connection
        $this->jsConnection = new JsFileHandler($this);
        $this->jsConnection->init();
        if ($this->jsConnection->count() > 0) {
            $success = true;
        }

        // test valid css connection
        $this->cssConnection = new CssFileHandler($this);
        $this->cssConnection->init();
        if ($this->cssConnection->count() > 0) {
            $success = true;
        }

        return $success;
    }

    public function iterate($force = false)
    {
        if ($this->jsConnection->count() > 0) {
            $this->jsConnection->iterate($force);
        }

        if ($this->cssConnection->count() > 0) {
            $this->cssConnection->iterate($force);
        }

        // todo: maybe if there is an error - or both have errors return false.
        return true;
    }

//***** OLD STACK */

/*
    public function getHasCssConfig()
    {
        return isset($this->config['css']) ? $this->config['css'] : false;
    }

    public function getHasJsConfig()
    {
        return isset($this->config['js']) ? $this->config['js'] : false;
    }

    public function getConfigOptions()
    {
        return isset($this->config['options']) ? $this->config['options'] : [];
    }

    public function generateMap($folder, $extension)
    {
        $files = FileHelper::findFiles($folder, $extension);
        $map = [];
        foreach ($files as $name => $value) {
            $item = $this->generateMapItem($name);
            if ($item) {
                $map[] = $item;
            }
            unset($item);
        }
        unset($files);
        return $map;
    }

    private function generateMapItem($file, $exclude = [])
    {
        if (is_file($file) && is_readable($file)) {
            return ['file' => $file, 'filemtime' => filemtime($file)];
        }

        return false;
    }

    public function findMapChange(array &$map)
    {
        $hasChange = false;
        foreach ($map as $key => $item) {
            $time = filemtime($item['file']);
            if ($time > $item['filemtime']) {
                $this->infoMessage("file " .$item['file'] . " has changed.");
                $hasChange = true;
                $map[$key]['filemtime'] = $time;
            }
            unset($time);
        }

        return $hasChange;
    }

    public function test()
    {
        $has = false;

        if ($this->getHasCssConfig()) {
            $this->scssMap = $this->generateMap($this->folder, 'scss');
            $has = true;
        }
        
        if ($this->getHasJsConfig()) {
            $map = [];
            foreach ($this->getHasJsConfig() as $file) {
                $jsFile = $this->getUnglueDir() . DIRECTORY_SEPARATOR . rtrim($file, '/');
                $mapItem = $this->generateMapItem($jsFile);
                if ($mapItem) {
                    $map[] = $mapItem;
                }
            }
            $this->jsMap = $map;
            unset($map);
            $has = true;
        }

        return $has;
    }

    public function createunglueFile($extension)
    {
        return $this->getUnglueDir() . DIRECTORY_SEPARATOR . $this->getUnglueFile() . '.'. $extension;
    }

    public function getUnglueDir()
    {
        return rtrim(dirname($this->configFile), '/');
    }

    public function getUnglueFile()
    {
        return basename($this->configFile, '.unglue');
    }

    public function iterate($force = false)
    {
        $dir = $this->getUnglueDir();
        $baseName = $this->getUnglueFile();

        if ($this->getHasCssConfig() && ($this->findMapChange($this->scssMap) || $force)) {
            self::infoMessage($baseName . '.css compile request');
            $css = $this->getCssResponse($this->getHasCssConfig(), $this->scssMap, $dir);
            if ($css) {
                file_put_contents($dir . DIRECTORY_SEPARATOR . $baseName . '.css', $css['code']);
                $mapPath = $dir . DIRECTORY_SEPARATOR . $baseName . '.css.map';
                if ($css['map']) {
                    file_put_contents($mapPath, $css['map']);
                } elseif (file_exists($mapPath)) {
                    unlink($mapPath);
                }
                self::successMessage($baseName.'.css compiled');
            }
        }

        if ($this->getHasJsConfig() && ($this->findMapChange($this->jsMap) || $force)) {
            self::infoMessage($baseName . '.js compile request');
            $js = $this->getJsResponse($this->getHasJsConfig(), $this->jsMap, $dir);
            if ($js) {
                file_put_contents($dir . DIRECTORY_SEPARATOR . $baseName . '.js', $js['code']);
                $mapPath = $dir . DIRECTORY_SEPARATOR . $baseName . '.js.map';
                if ($js['map']) {
                    file_put_contents($mapPath, $js['map']);
                } elseif (file_exists($mapPath)) {
                    unlink($mapPath);
                }
                self::successMessage($baseName.'.js compiled');
            }
        }
    }

    public function getCssResponse($config, array $maps, $dir)
    {
        $content = [
            'code' => '',
            'map' => ''
        ];
        foreach ($config as $scss) {
            $map = [];
            foreach ($maps as $file) {
                $map[] = [
                    'file' => $file['file'],
                    'code' => file_get_contents($file['file']),
                ];
            }

            $payload = [
                'distFile' => $this->getUnglueFile() . '.css',
                'mainFile' => $dir . DIRECTORY_SEPARATOR . $scss,
                'files' => $map,
            ];

            $r = $this->generateRequest($this->server . '/compile/css', $payload);

            if ($r) {
                $content['code'] .= $r['code'];
                if ($r['map']) {
                    $content['map'] .= $r['map'];
                }
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
                'code' => file_get_contents($p),
            ];
        }

        $payload = [
            'distFile' => $this->getUnglueFile() . '.js',
            'files' => $map,
        ];

        $r = $this->generateRequest($this->server . '/compile/js', $payload);

        if ($r) {
            return $r;
        }

        return false;
    }

    public function generateRequest($url, array $payload)
    {
        $payload['options'] = $this->getConfigOptions();
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
    */
}
