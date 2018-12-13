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
        $files = $this->getFilesContent($this->config->getUnglueConfigFolder());

        $distFile = $this->config->getUnglueConfigFolderDistFilePath('css');

        $code = null;
        foreach ($this->config->getHasUnglueConfigSection('css', []) as $scss) {
            $payload = [
                'distFile' => $this->config->getUnglueConfigFileBaseName().'.css',
                'mainFile' => $this->config->getUnglueConfigFolderPath($scss),
                'files' => $files,
            ];

            $r = $this->generateRequest('/compile/css', $payload);

            if ($r) {
                $code .= $r['code'];
            }
        }

        if ($code) {
            $this->config->writeUnglueConfigFolderDistFile($code, 'css');
        }
    }
}
