<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "academic_rank".
 *
 * @property integer $id
 * @property string $acr_name
 * @property string $acr_desc
 * @property integer $acr_created_by
 * @property string $acr_created_at
 * @property integer $acr_updated_by
 * @property string $acr_updated_at
 *
 * @property User $acrCreatedBy
 * @property User $acrUpdatedBy
 */
class AcademicRank extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'academic_rank';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['acr_name', 'acr_created_by', 'acr_created_at', 'acr_updated_by', 'acr_updated_at'], 'required'],
            [['acr_desc'], 'string'],
            [['acr_created_by', 'acr_updated_by'], 'integer'],
            [['acr_created_at', 'acr_updated_at'], 'safe'],
            [['acr_name'], 'string', 'max' => 50],
            [['acr_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['acr_created_by' => 'id']],
            [['acr_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['acr_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'acr_name' => Yii::t('rms', 'Academic Rank Name'),
            'acr_desc' => Yii::t('rms', 'Academic Rank Desc'),
            'acr_created_by' => Yii::t('rms', 'Acr Created By'),
            'acr_created_at' => Yii::t('rms', 'Acr Created At'),
            'acr_updated_by' => Yii::t('rms', 'Acr Updated By'),
            'acr_updated_at' => Yii::t('rms', 'Acr Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcrCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'acr_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcrUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'acr_updated_by']);
    }
}
