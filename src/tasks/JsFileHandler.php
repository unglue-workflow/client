<?php

namespace unglue\client\tasks;

use unglue\client\helpers\ConsoleHelper;
use unglue\client\base\BaseFileHandler;

class JsFileHandler extends BaseFileHandler
{
    public function name()
    {
        return 'js';
    }
    
    public function init()
    {
        foreach ($this->config->getHasUnglueConfigSection('js', []) as $file) {
            $this->addToMap($this->config->getUnglueConfigFolderPath($file));
        }
    }

    public function handleUpload()
    {
        $files = $this->getFilesContent();

        if (empty($files)) {
            ConsoleHelper::errorMessage($this->messagePrefix() . "no js files found to transmit. count: " . $this->count());
            return false;
        }

        $r = $this->generateRequest('/compile/js', [
            'distFile' => $this->config->getUnglueConfigFolderDistFilePath('js'),
            'files' => $files,
        ]);

        if (!$r) {
            return false;
        }

        $this->config->writeUnglueConfigFolderDistFile($r['code'], 'js');
        if (!empty($r['map'])) {
            $this->config->writeUnglueConfigFolderDistFile($r['map'], 'js.map');
        }
    }
}
