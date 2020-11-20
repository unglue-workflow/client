<?php

/*************************************************************
 * 
 * THIS FILE IS ONLY REQUIRED FOR THE PHAR FILE GENERATOR!
 * 
 * BUG: Until fixed, ensure you cleanup the vendor/luyadev/installer.php file and remove the LUYA modules which are part of the testsuite.
 * 
 * php -d phar.readonly=0 vendor/bin/phar-builder package composer.json --no-interaction
 *
 *## Phar Builder
 *
 * In order to build the unglue client phar file `unglue.phar` run:
 * 
 * > BUG: Until fixd, ensure you cleanup the vendor/luyadev/installer.php file and remove the LUYA modules which are part of the testsuite.
 * 
 * ```
 * php -d phar.readonly=0 vendor/bin/phar-builder package composer.json --no-interaction && chmod +x unglue.phar
 * ```
 * 
 *************************************************************/

require_once(__DIR__ . '/../vendor/autoload.php');

$boot = new \luya\Boot();
$boot->setBaseYiiFile(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
$boot->setConfigArray([
    'id' => 'clientunglue', 
    'basePath' => dirname(__DIR__), 
    'enableCoreCommands' => false,
    'defaultRoute' => 'help',
    'controllerMap' => [
        'help' => 'yii\console\controllers\HelpController',
        'watch' => 'unglue\client\controllers\WatchController',
        'compile' => 'unglue\client\controllers\CompileController',
    ],
]);
$boot->applicationConsole();
