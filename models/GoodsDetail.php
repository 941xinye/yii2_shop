<?php

namespace app\models;

use app\components\AppActiveRecord;

/**
 * This is the model class for table "goods_detail".
 *
 * @property integer $id
 * @property integer $content_id
 * @property string $detail
 * @property string $params
 * @property string $file_url
 * @property Content $content
 */
class GoodsDetail extends AppActiveRecord
{
    const SCENARIO_PRODUCTS = 'goods';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_detail';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_PRODUCTS] = ['goods_id','detail', 'params','original_price','price'];
        return $scenarios;
    }

    /** @var  Content */
    protected $_content;
    /**
     * 获取主表数据
     */
    public function getContent()
    {
        if(empty($this->_content)) {
            $this->_content = $this->content();
        }
        return $this->_content;
    }

    /**
     * need rewrite
     * @return \app\models\Content
     */
    public function content()
    {

    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'detail'], 'required'],
            ['params', 'string', 'max' => 3000],
            [['goods_id', 'detail','params','price','original_price'], 'required', 'on'=>self::SCENARIO_PRODUCTS],
            [['price','original_price'], 'double', 'on'=>self::SCENARIO_PRODUCTS],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content_id'=>'主表ID不能为空',
            'detail' => '内容',
            'file_url' => '文件路径',
            'params' => '参数',
            'original_price' => '原价',
            'price' => '售价',
        ];
    }
}
