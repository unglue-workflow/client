<?php

/*************************************************************
 * 
 * THIS FILE IS ONLY REQUIRED FOR THE PHAR FILE GENERATOR.
 * 
 * php -d phar.readonly=0 vendor/bin/phar-builder package composer.json --no-interaction
 * 
 *************************************************************/

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
