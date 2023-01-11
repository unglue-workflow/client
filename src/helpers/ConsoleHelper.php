<?php

namespace unglue\client\helpers;

use unglue\client\base\BaseCompileController;
use unglue\client\tasks\ConfigConnection;
use yii\console\Exception;
use yii\helpers\Console;

/**
 * Console Helper.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class ConsoleHelper extends Console
{
    /**
     * Outpout info message
     *
     * @param string $message
     */
    public static function infoMessage($message)
    {
        echo $message . PHP_EOL;
    }

    /**
     * Output error message
     *
     * @param string $message
     * @param BaseCompileController $config
     * @return boolean
     */
    public static function errorMessage($message, BaseCompileController $config)
    {
        if ($config->silent) {
            echo Console::ansiFormat('Error: ' . $message, [Console::FG_RED]) . PHP_EOL;
            return false;
        } else {
            throw new Exception($message);
        }
        
    }

    /**
     * Output success message
     *
     * @param string $message
     * @return boolean
     */
    public static function successMessage($message)
    {
        echo Console::ansiFormat($message, [Console::FG_GREEN]) . PHP_EOL;
        return true;
    }
}
