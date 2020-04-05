<?php

namespace unglue\client\helpers;

use RegexIterator;
use RecursiveRegexIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveCallbackFilterIterator;
use luya\helpers\FileHelper as BaseFileHelper;
use luya\helpers\StringHelper;
use luya\helpers\ArrayHelper;

/**
 * Helper for file Tasks.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class FileHelper extends BaseFileHelper
{
    const OPTION_FOLLOW_SYM_LINKS = 'followSymLinks';

    /**
     * Find all files for a certain extension.
     *
     * @param string $folder
     * @param string $extension The name of the extension without dot example `unglue`
     * @param array $exclude Its possible to provide an array with regular expression. If the expressions matches the
     * file is exclued from the list , example rege `vendor/` would filter all files inside a folder named vendor.
     * @param array $options An array with options:
     * - followSymLinks: boolean, whether symlinks should be followed or not, default is false.
     * @return array
     */
    public static function findFilesByExtension($folder, $extension, array $exclude = [], array $options = [])
    {
        if (!is_dir($folder)) {
            return [];
        }

        $filter = function ($file, $key, $iterator) use ($exclude) {
            foreach ($exclude as $pattern) {
                if (preg_match('/'.preg_quote(trim($pattern, '/'), '/').'/', $file->getRealPath())) {
                    return false;
                }
            }

            return true;
        };

        if (ArrayHelper::getValue($options, self::OPTION_FOLLOW_SYM_LINKS, false)) {
            $args = RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::FOLLOW_SYMLINKS;
        } else {
            $args = RecursiveDirectoryIterator::SKIP_DOTS;
        }

        $directory = new RecursiveDirectoryIterator($folder, $args);
        
        $iterator = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator($directory, $filter));
        $regex =  new RegexIterator($iterator, '/^.+\.'.preg_quote($extension).'$/i', RecursiveRegexIterator::GET_MATCH);

        return iterator_to_array($regex);
    }

    /**
     * Get an array of files from an extracted wildcard path.
     *
     * If no wildcard defintions are used an array with the input path is provided.
     *
     * @param string $path The path with the wildcard defintion (or without). like `files/lib/*.js`.
     * @return array
     * @since 1.3
     */
    public static function findFilesForWildcardPath($path)
    {
        if (!StringHelper::contains('*', $path)) {
            return [$path];
        }

        return glob($path, GLOB_NOSORT);
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
