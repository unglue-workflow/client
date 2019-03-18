<?php

namespace unglue\client\tasks;

use unglue\client\helpers\ConsoleHelper;
use unglue\client\base\BaseFileHandler;
use unglue\client\helpers\FileHelper;

/**
 * Handles Js Files.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class JsFileHandler extends BaseFileHandler
{
    /**
     * {@inheritDoc}
     */
    public function name()
    {
        return 'js';
    }
    
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        foreach ($this->getConfig()->getHasUnglueConfigSection('js', []) as $file) {
            foreach (FileHelper::findFilesForWildcardPath($this->getConfig()->getUnglueConfigFolderPath($file)) as $path) {
                $this->addToMap($path);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function handleUpload()
    {
        $files = $this->getFilesContent($this->getConfig()->getUnglueConfigFolder());

        if (empty($files)) {
            ConsoleHelper::errorMessage($this->messagePrefix("No js files found to transmit. count: " . $this->count()));
            return false;
        }

        $response = $this->generateRequest('/compile/js', [
            'distFile' => $this->getConfig()->getUnglueConfigFileBaseName() . '.js',
            'files' => $files,
        ]);

        if (!$response) {
            return false;
        }

        return $this->getConfig()->writeUnglueConfigFolderDistFile($response['code'], 'js');
    }
}
