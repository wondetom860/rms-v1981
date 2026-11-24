<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "region".
 *
 * @property integer $id
 * @property string $region_name
 * @property integer $region_created_by
 * @property string $region_created_at
 * @property integer $region_updated_by
 * @property string $region_updated_at
 *
 * @property User $regionUpdatedBy
 * @property User $regionCreatedBy
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        if (isset($_SESSION['nationality'])) {
            return User::tableName();
        }
        return 'region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region_name', 'region_created_by', 'region_created_at', 'region_updated_by', 'region_updated_at'], 'required'],
            [['region_created_by', 'region_updated_by'], 'integer'],
            [['region_created_at', 'region_updated_at'], 'safe'],
            [['region_name'], 'string', 'max' => 50],
           

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'region_name' => Yii::t('rms', 'Region Name'),
            'region_created_by' => Yii::t('rms', 'Region Created By'),
            'region_created_at' => Yii::t('rms', 'Region Created At'),
            'region_updated_by' => Yii::t('rms', 'Region Updated By'),
            'region_updated_at' => Yii::t('rms', 'Region Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegionUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'region_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegionCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'region_created_by']);
    }
}
