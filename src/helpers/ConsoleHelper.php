<?php

namespace unglue\client\helpers;

use yii\helpers\Console;

class ConsoleHelper extends Console
{
    public static function infoMessage($message)
    {
        echo $message . PHP_EOL;
    }

    public static function errorMessage($message)
    {
        echo Console::ansiFormat('Error: ' . $message, [Console::FG_RED]) . PHP_EOL;
        return false;
    }

    public static function successMessage($message)
    {
        echo Console::ansiFormat($message, [Console::FG_GREEN]) . PHP_EOL;
        return true;
    }
}
