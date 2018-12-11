<?php

namespace unglue\client\helpers;

use luya\helpers\FileHelper as BaseFileHelper;

/**
 * Helper for file Tasks.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class FileHelper extends BaseFileHelper
{
    /**
     * Find all files for a certain extension.
     *
     * @param string $folder
     * @param string $extension
     * @return array
     */
    public static function findFilesByExtension($folder, $extension)
    {
        if (!is_dir($folder)) {
            return [];
        }

        $directory = new \RecursiveDirectoryIterator($folder);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex =  new \RegexIterator($iterator, '/^.+\.'.preg_quote($extension).'$/i', \RecursiveRegexIterator::GET_MATCH);

        return iterator_to_array($regex);
    }
}
