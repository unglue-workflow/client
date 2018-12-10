<?php

namespace unglue\client\tasks;

class JsFileHandler extends BaseFileHandler
{
    public function init()
    {
        foreach ($this->config->getHasUnglueConfigSection('js', []) as $file) {
            $this->addToMap($this->config->getUnglueConfigFolderPath($file));
        }
    }

    public function handleUpload()
    {
        $r = $this->generateRequest('/compile/js', [
            'distFile' => $this->config->getUnglueConfigFolderDistFilePath('js'),
            'files' => $this->getFilesContent(),
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