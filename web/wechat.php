<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/wechat.php');

$app = new \EasyWeChat\Foundation\Application($config);
$server = $app->server;
$user = $app->user;
$server->setMessageHandler(function($message){
    return "您好！欢迎关注 overtrue!";
});

$server->serve()->send();
