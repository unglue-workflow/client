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

    /**
     * Calculate the relative from a given path to a given path.
     *
     * @param string $from From path
     * @param string $to To path
     * @param string $ps The path seperator
     * @return string
     * @see http://php.net/manual/en/function.realpath.php#105876
     */
    public static function relativePath($from, $to, $ps = DIRECTORY_SEPARATOR)
    {
        $arFrom = explode($ps, rtrim($from, $ps));
        $arTo = explode($ps, rtrim($to, $ps));
        while (count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0])) {
            array_shift($arFrom);
            array_shift($arTo);
        }
        return str_pad("", count($arFrom) * 3, '..'.$ps).implode($ps, $arTo);
    }
}
