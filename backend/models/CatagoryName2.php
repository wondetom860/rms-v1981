<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "catagory_name2".
 *
 * @property integer $id
 * @property string $catagory_name
 * @property string $remark
 *
 * @property ContAssessCatagory2[] $contAssessCatagory2s
 */
class CatagoryName2 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catagory_name2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['catagory_name', 'remark'], 'required'],
            [['remark'], 'string'],
            [['catagory_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'catagory_name' => Yii::t('rms', 'Catagory Name'),
            'remark' => Yii::t('rms', 'Remark'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContAssessCatagory2s()
    {
        return $this->hasMany(ContAssessCatagory2::className(), ['cat_catagory_relId' => 'id']);
    }
}
