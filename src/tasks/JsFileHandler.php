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
        foreach ($this->config->getHasUnglueConfigSection('js', []) as $file) {
            $this->addToMap($this->config->getUnglueConfigFolderPath($file));
        }
    }

    /**
     * {@inheritDoc}
     */
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
        } else {
            // if no map try to unlink the given file cause this options is not set.
            FileHelper::unlink($this->config->getUnglueConfigFolderDistFilePath('js.map'));
        }
    }
}
