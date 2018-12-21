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
        $mainFiles = [];
        foreach ($this->config->getHasUnglueConfigSection('css', []) as $scss) {
            $mainFiles[] = $this->config->getUnglueConfigFolderPath($scss);
        }

        $response = $this->generateRequest('/compile/css', [
            'distFile' => $this->config->getUnglueConfigFileBaseName().'.css',
            'mainFiles' => $mainFiles,
            'files' => $this->getFilesContent($this->config->getUnglueConfigFolder()),
        ]);

        if ($response) {
            return $this->config->writeUnglueConfigFolderDistFile($response['code'], 'css');
        }

        return false;
    }
}
