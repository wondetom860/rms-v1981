<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "cont_assess_catagory_name".
 *
 * @property integer $id
 * @property string $cont_cat_name
 * @property string $remark
 *
 * @property ContAssessCatagory[] $contAssessCatagories
 */
class ContAssessCatagoryName extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cont_assess_catagory_name';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cont_cat_name', 'remark'], 'required'],
            [['remark'], 'string'],
            [['cont_cat_name'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'cont_cat_name' => Yii::t('rms', 'Category Name'),
            'remark' => Yii::t('rms', 'Remark'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContAssessCatagories()
    {
        return $this->hasMany(ContAssessCatagory::className(), ['cat_name_relId' => 'id']);
    }
}
