<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/30
 * Time: 23:08
 */
namespace app\controllers;

use app\components\AppController as Controller;
use Yii;

class WechatController extends Controller
{
    public function init()
    {
        parent::init();
        // 微信网页授权:
//        if(Yii::$app->wechat->isWechat && !Yii::$app->wechat->isAuthorized()) {
//            return Yii::$app->wechat->authorizeRequired()->send();
//        }
    }

    /**
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $app = Yii::$app->wechat;
        $server = $app->server;
        $user = $app->user;
        $server->setMessageHandler(function($message) use ($user) {
            $fromUser = $user->get($message->FromUserName);
            switch ($message->MsgType) {
                case 'event':
                    $msg = '收到事件消息';
                    break;
                case 'text':
                    $msg = '收到文字消息';
                    break;
                case 'image':
                    $msg = '收到图片消息';
                    break;
                case 'voice':
                    $msg = '收到语音消息';
                    break;
                case 'video':
                    $msg = '收到视频消息';
                    break;
                case 'location':
                    $msg = '收到坐标消息';
                    break;
                case 'link':
                    $msg = '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    $msg = "{$fromUser->nickname} 您好！欢迎关注 overtrue!";
                    break;
            }
            return $msg;
        });

        return $server->serve()->send();
    }
}