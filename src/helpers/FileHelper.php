<?php

namespace unglue\client\helpers;

/**
 * Helper for file Tasks.
 * 
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class FileHelper
{
    /**
     * Find all files for a certain extension.
     *
     * @param string $folder
     * @param string $extension
     * @return array
     */
    public static function findFiles($folder, $extension)
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
