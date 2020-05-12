<?php

namespace unglue\client\tasks;

use unglue\client\helpers\FileHelper;
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
        $files = FileHelper::findFilesByExtension($this->getConfig()->getWatchFolder(), 'scss', [], [
            FileHelper::OPTION_FOLLOW_SYM_LINKS => $this->getConfig()->getCommand()->symlinks,
        ]);

        foreach ($files as $path => $name) {
            $this->addToMap($path);
        }

        foreach ($this->getMainFiles() as $file) {
            $this->addToMap($file);
        }
    }

    /**
     * If css section does not exists in unglue config file return count 0 in order
     * to make sure handler is not started with empty section.
     *
     * @return integer
     */
    public function count()
    {
        if (!$this->getCssFilesFromConfig()) {
            return 0;
        }

        return parent::count();
    }

    /**
     * Get the fullpath for the main files defined in the css section
     *
     * @return array
     * @since 1.4.1
     */
    protected function getMainFiles()
    {
        $mainFiles = [];
        foreach ($this->getCssFilesFromConfig() as $scss) {
            $mainFiles[] = realpath($this->getConfig()->getUnglueConfigFolderPath($scss));
        }

        return $mainFiles;
    }

    /**
     * {@inheritDoc}
     */
    public function handleUpload()
    {
        $response = $this->generateRequest('/compile/css', [
            'distFile' => $this->getConfig()->getUnglueConfigFileBaseName().'.css',
            'mainFiles' => $this->getMainFiles(),
            'files' => $this->getFilesContent($this->getConfig()->getUnglueConfigFolder()),
        ]);

        if ($response) {
            return $this->getConfig()->writeUnglueConfigFolderDistFile($response['code'], 'css');
        }

        return false;
    }

    /**
     * Return an array of files defined in unglue config "css" section.
     *
     * @return array An array where the value is the path to the css file from a relative view of the file.
     * @since 1.0.1
     */
    protected function getCssFilesFromConfig()
    {
        return $this->getConfig()->getHasUnglueConfigSection('css', []);
    }
}
