<?php

namespace unglue\client\helpers;

class FileHelper
{
    public static function findFiles($folder, $extension)
    {
        $directory = new \RecursiveDirectoryIterator($folder);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex =  new \RegexIterator($iterator, '/^.+\.'.preg_quote($extension).'$/i', \RecursiveRegexIterator::GET_MATCH);

        return iterator_to_array($regex);
    }
}