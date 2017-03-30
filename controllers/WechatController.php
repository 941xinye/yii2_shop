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
        $app = Yii::$app->wechat;
        var_dump($app);exit();
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