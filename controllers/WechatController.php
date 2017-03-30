<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/30
 * Time: 23:08
 */
namespace app\controllers;

use yii\web\Controller;
use Yii;

class WechatController extends Controller
{
    /**
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $app = Yii::$app->wechat->app;
        $server = $app->server;
        $user = $app->user;
        $server->setMessageHandler(function ($message) {
            return "您好！欢迎关注我!";
        });
        return $server->serve()->send();
    }
}