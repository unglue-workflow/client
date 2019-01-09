<?php

namespace unglue\client\helpers;

use RegexIterator;
use RecursiveRegexIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveCallbackFilterIterator;
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
     * @param string $extension The name of the extension without dot example `unglue`
     * @param array $exclude Its possible to provide an array with regular expression. If the expressions matches the
     * file is exclued from the list , example rege `vendor/` would filter all files inside a folder named vendor.
     * @return array
     */
    public static function findFilesByExtension($folder, $extension, array $exclude = [])
    {
        if (!is_dir($folder)) {
            return [];
        }

        $filter = function ($file, $key, $iterator) use ($exclude) {
            foreach ($exclude as $pattern) {
                if (preg_match('/'.preg_quote($pattern, '/').'/', $file->getRealPath())) {
                    return false;
                }
            }

            return true;
        };

        $directory = new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator($directory, $filter));
        $regex =  new RegexIterator($iterator, '/^.+\.'.preg_quote($extension).'$/i', RecursiveRegexIterator::GET_MATCH);

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
