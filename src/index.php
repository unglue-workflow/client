<?php

include '../vendor/autoload.php';

$boot = new \luya\Boot();
$boot->setBaseYiiFile('../vendor/yiisoft/yii2/Yii.php');
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
