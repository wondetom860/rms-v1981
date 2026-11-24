<?php

namespace backend\models;
use \backend\models\ContactCatagory;

use Yii;

/**
 * This is the model class for table "contact_list".
 *
 * @property int $id
 * @property int $cl_catagory_relId
 * @property string $cl_full_name
 * @property string $cl_phone_office
 * @property string $cl_phone_mobile
 * @property string $cl_phone_ext
 * @property string $cl_created_at
 * @property string|null $cl_updated_at
 * @property int $cl_created_by
 * @property int|null $cl_updated_by
 * @property string $data
 */
class ContactList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contact_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cl_catagory_relId', 'cl_full_name', 'cl_phone_mobile', 'cl_phone_ext', 'cl_created_at', 'cl_created_by', 'data'], 'required'],
            [['cl_catagory_relId', 'cl_created_by', 'cl_updated_by'], 'integer'],
            [['cl_created_at', 'cl_updated_at'], 'safe'],
            [['data'], 'string'],
            [['cl_full_name'], 'string', 'max' => 255],
            [['cl_phone_office', 'cl_phone_mobile'], 'string', 'max' => 15],
            [['cl_phone_ext'], 'string', 'max' => 5],
            [['cl_catagory_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ContactCatagory::className(), 'targetAttribute' => ['cl_catagory_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'cl_catagory_relId' => Yii::t('rms', 'Category'),
            'cl_full_name' => Yii::t('rms', 'Full Name'),
            'cl_phone_office' => Yii::t('rms', 'Phone (Office)'),
            'cl_phone_mobile' => Yii::t('rms', 'Phone (Mobile)'),
            'cl_phone_ext' => Yii::t('rms', 'Phone (Ext)'),
            'cl_created_at' => Yii::t('rms', 'Created At'),
            'cl_updated_at' => Yii::t('rms', 'Updated At'),
            'cl_created_by' => Yii::t('rms', 'Created By'),
            'cl_updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClCatagoryRel()
    {
        return $this->hasOne(ContactCatagory::className(), ['id' => 'cl_catagory_relId']);
    }

    public function getData($modelObj){
        $mainData = (object)[
            'cl_catagory_relId'=>intval($modelObj->cl_catagory_relId),
            'cl_full_name'=>$modelObj->cl_full_name,
            'cl_phone_mobile'=>$modelObj->cl_phone_mobile,
            'cl_phone_office'=>$modelObj->cl_phone_office,
            'cl_phone_ext'=>$modelObj->cl_phone_ext,
            'cl_created_at'=>$modelObj->cl_created_at,
            'cl_created_by'=>$modelObj->cl_created_by,
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered($modelObj){
        return $this->getData($modelObj) == $modelObj->data;
    }
}
