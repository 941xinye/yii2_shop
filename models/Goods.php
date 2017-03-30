<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 2016/12/9
 * Time: 10:17
 * Email:liyongsheng@meicai.cn
 */

namespace app\models;

use app\components\behaviors\UploadBehavior;
use Yii;
use app\components\AppActiveRecord;
use yii\behaviors\AttributeBehavior;

/**
 * Class Goods
 * @package app\models
 * @method uploadImgFile()
 */
class Goods extends AppActiveRecord
{
    public $imageFile;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'type', 'status','category_id'], 'required'],
            [['imageFile'], 'file', 'extensions' => 'gif, jpg, png, jpeg','mimeTypes' => 'image/jpeg, image/png',],
            [['type', 'status', 'admin_user_id', 'category_id','created_at', 'updated_at'], 'integer'],
            [['title', 'image', 'description','keywords'], 'string', 'max' => 255],
        ];
    }

    public function init()
    {
        parent::init();
        //添加的时候设置 添加人id
        $this->attachBehavior('set_admin_user_id', [
            'class' => AttributeBehavior::className(),
            'attributes' => [
                static::EVENT_BEFORE_INSERT => 'admin_user_id',
            ],
            'value' => Yii::$app->user->id,
        ]);
    }

    /** @var array  */
    static public $types = [
        self::TYPE_GOODS=>'商品',
    ];

    /** 商品 */
    const TYPE_GOODS =5;

    /**
     * 当前的类型
     */
    static $currentType = self::TYPE_GOODS;

    /** 前台不显示 */
    const STATUS_DISABLE = 0;

    /** 前台显示 */
    const STATUS_ENABLE = 1;

    /** @var array  */
    static public $statusList = [
        self::STATUS_DISABLE=>'未审核',
        self::STATUS_ENABLE=>'审核',
    ];

    /** @var array 此类型下全部的分类 */
    protected static $_categories = [];

    /** @var  ContentDetail */
    protected $_detail;

    /**
     * 自动更新详情
     * @var bool
     */
    public static $autoUpdateDetail = true;

    /**
     * 只读属性
     * 获取当前类型下的全部分类
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getCategories()
    {
        if(empty(static::$_categories)){
            static::$_categories = Category::find()->where(['type'=>static::$currentType])->asArray()->all();
        }
        return static::$_categories;
    }

    public function getDetail()
    {
        if(empty($this->_detail)) {
            $this->_detail = $this->detail();
        }
        return $this->_detail;
    }
    /**
     * @return \app\models\ContentDetail|ActiveRecord|array
     */
    public function detail()
    {
        if ($this->isNewRecord) {
            return new GoodsDetail(['scenario' => GoodsDetail::SCENARIO_PRODUCTS]);
        } else {
            $model = $this->hasOne(GoodsDetail::className(), ['goods_id' => 'id'])->one();
            $model->scenario = GoodsDetail::SCENARIO_PRODUCTS;
            return $model;
        }
    }

    public function load($data, $formName = null){
        $res = parent::load($data, $formName);
        if(static::$autoUpdateDetail && $res){
            return $this->getDetail()->load($data);
        }
        return $res;
    }

    public function beforeSave($insert)
    {
        $res = parent::beforeSave($insert);
        if($res==false){
            return $res;
        }
        if (!$this->validate()) {
            Yii::info('Model not updated due to validation error.', __METHOD__);
            return false;
        }
        $file = $this->uploadImgFile();
        if($file){
            $this->image = $file;
        }
        return true;
    }

    public function behaviors()
    {
        return [
            [
                'class'=>UploadBehavior::className(),
                'saveDir'=>'products-img/'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'typeText'=>'类型',
            'category_id'=>'分类',
            'image' => '图片',
            'imageFile' => '图片',
            'keywords' => 'Keywords',
            'description' => 'Description',
            'status' => '状态',
            'statusText' => '状态',
            'hits' => '点击数',
            'created_at'=>'创建时间'
        ];
    }

    /**
     * 累加点击量
     * @param int $id
     * @return int
     */
    public static function hitCounters($id)
    {
        return self::updateAllCounters(['hits'=>1], ['id'=>$id]);
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return boolean whether the saving succeeded (i.e. no validation errors occurred).
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        $res = parent::save($runValidation, $attributeNames);
        if($res && static::$autoUpdateDetail) {
            if (empty($this->detail->goods_id)) {
                $this->detail->goods_id = $this->id;
            }
            if($this->detail->save()==false){
                $this->delete();
                return false;
            }
        }
        return $res;
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        $res = parent::validate($attributeNames, $clearErrors);
        if(static::$autoUpdateDetail){
            $this->detail->goods_id = 0; //临时设置
            return $this->detail->validate($attributeNames, $clearErrors);
        }
        return $res;
    }

    /**
     * 删除详情
     */
    public function afterDelete()
    {
        parent::afterDelete(); // TODO: Change the autogenerated stub
        $this->getDetail()->delete();
    }

    /**
     * 内容类型
     * @return mixed|null
     */
    public function getTypeText()
    {
        return isset(self::$types[$this->type])?self::$types[$this->type]:null;
    }
    /**
     * 内容状态文字描述
     * return string|null
     */
    public function getStatusText()
    {
        return isset(self::$statusList[$this->status])?self::$statusList[$this->status]:null;
    }
}