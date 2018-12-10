<?php

namespace unglue\client\tasks;

use unglue\client\helpers\FileHelper;


class CssFileHandler extends BaseFileHandler
{
    public function init()
    {
        $files = FileHelper::findFiles($this->config->getWatchFolder(), 'scss');
        foreach ($files as $path => $name) {
            $this->addToMap($path);
        }
    }

    public function handleUpload()
    {
        $files = $this->getFilesContent();

        $distFile = $this->config->getUnglueConfigFolderDistFilePath('css');

        $code = null;
        $map = null;
        foreach ($this->config->getHasUnglueConfigSection('css', []) as $scss) {
            $payload = [
                'distFile' => $distFile,
                'mainFile' => $this->config->getUnglueConfigFolderPath($scss),
                'files' => $files,
            ];

            $r = $this->generateRequest('/compile/css', $payload);

            if ($r) {
                $code .= $r['code'];
                if (!empty($r['map'])) {
                    $map .= $r['map'];
                }
            }
        }

        if ($code) {
            $this->config->writeUnglueConfigFolderDistFile($code, 'js');
        }

        if ($map) {
            $this->config->writeUnglueConfigFolderDistFile($map, 'js.map');
        }
    }
}