<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "contact_atagory".
 *
 * @property int $id
 * @property string $cat_name
 * @property string $cat_created_at
 * @property string|null $updated_at
 * @property int $created_by
 * @property int|null $updated_by
 * @property string $data
 */
class ContactCatagory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contact_atagory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cat_name', 'cat_created_at', 'created_by', 'data'], 'required'],
            [['cat_created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['data'], 'string'],
            [['cat_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'cat_name' => Yii::t('rms', 'Cat Name'),
            'cat_created_at' => Yii::t('rms', 'Cat Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }


    public function getData($modelObj){
        $mainData = (object)[
            'cat_name'=>$modelObj->cat_name,
            'cat_created_at'=>$modelObj->cat_created_at,
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered($modelObj){
        return $this->getData($modelObj) == $modelObj->data;
    }
}
