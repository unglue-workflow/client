<?php

namespace unglue\client\tasks;

use unglue\client\helpers\FileHelper;
use unglue\client\helpers\ConsoleHelper;
use unglue\client\base\BaseFileHandler;

/**
 * Handles Css Files.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class CssFileHandler extends BaseFileHandler
{
    /**
     * {@inheritDoc}
     */
    public function name()
    {
        return 'css';
    }
    
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $files = FileHelper::findFilesByExtension($this->config->getWatchFolder(), 'scss');
        foreach ($files as $path => $name) {
            $this->addToMap($path);
        }
    }

    /**
     * {@inheritDoc}
     */
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
            $this->config->writeUnglueConfigFolderDistFile($code, 'css');
        }

        if ($map) {
            $this->config->writeUnglueConfigFolderDistFile($map, 'css.map');
        } else {
            // if no map try to unlink the given file cause this options is not set.
            FileHelper::unlink($this->config->getUnglueConfigFolderDistFilePath('css.map'));
        }
    }
}
