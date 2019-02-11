<?php

namespace unglue\client\tasks;

use unglue\client\helpers\ConsoleHelper;
use unglue\client\base\BaseFileHandler;
use unglue\client\helpers\FileHelper;

/**
 * Handles SVG Sprite Files.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class SvgSpriteFileHandler extends BaseFileHandler
{
    /**
     * {@inheritDoc}
     */
    public function name()
    {
        return 'svg-sprite';
    }
    
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        foreach ($this->config->getHasUnglueConfigSection('svg', []) as $file) {
            foreach (FileHelper::findFilesForWildcardPath($this->config->getUnglueConfigFolderPath($file)) as $path) {
                $this->addToMap($path);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function handleUpload()
    {
        $files = $this->getFilesContent($this->config->getUnglueConfigFolder());

        if (empty($files)) {
            ConsoleHelper::errorMessage($this->messagePrefix("No svg files found to transmit. count: " . $this->count()));
            return false;
        }

        $response = $this->generateRequest('/compile/svg-sprite', [
            'files' => $files,
        ]);

        if (!$response) {
            return false;
        }

        return $this->config->writeUnglueConfigFolderDistFile($response['code'], 'svg');
    }
}
