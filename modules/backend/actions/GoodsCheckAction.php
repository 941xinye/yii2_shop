<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 2017/1/5
 * Time: 13:23
 * Email:liyongsheng@meicai.cn
 */
namespace app\modules\backend\actions;

use app\models\Goods;
use yii\base\Action;
use Yii;
use yii\base\Exception;
use yii\base\ErrorException;
use yii\web\Response;

/**
 * Class GoodsActions
 * @property \app\modules\backend\components\BackendController $controller
 * @package app\modules\backend\actions
 */
class GoodsCheckAction extends Action
{

    /** @var int 审核通过 */
    public $status = null;
    public $type = null;

    public function init()
    {
        parent::init();
        if($this->type === null){
            throw new ErrorException('type 不能为空');
        }
        if($this->status === null){
            throw new ErrorException('status 不能为空');
        }
    }
    /**
     * @return array
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ids =  Yii::$app->request->post('ids');
        if(empty($ids)){
            return ['data'=>'id不能为空','code'=>1];
        }
        Goods::$currentType = $this->type;
        $attr = ['status'=>$this->status];

        /** @var $query ContentQuery  */
        $query = Goods::find();

        $query->andFilterWhere([
            'in', 'id', $ids
        ]);
        try {
            Goods::updateAll($attr, $query->where);
            return [
                'code'=>0,
                'data'=>'操作成功'
            ];
        }catch(Exception $e){
            return [
                'code'=>1,
                'data'=>$e->getMessage()
            ];
        }
    }
}