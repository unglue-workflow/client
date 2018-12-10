<?php

namespace unglue\client\helpers;

use yii\helpers\Console;

class ConsoleHelper extends Console
{
    public static function infoMessage($message)
    {
        echo "[".date("H:i:s")."] ". $message . PHP_EOL;
    }

    public static function errorMessage($message)
    {
        echo "[".date("H:i:s")."] ". Console::ansiFormat('Error: ' . $message, [Console::FG_RED]) . PHP_EOL;
        return false;
    }

    public static function successMessage($message)
    {
        echo "[".date("H:i:s")."] ". Console::ansiFormat($message, [Console::FG_GREEN]) . PHP_EOL;
        return true;
    }
}