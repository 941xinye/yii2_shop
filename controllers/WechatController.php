<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/30
 * Time: 23:08
 */
namespace app\controllers;

use EasyWeChat\Message\Text;
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
            $text = new Text(['content' => '您好！overtrue。']);
            return $text;
        });

        $server->serve()->send();
        exit;
    }
}