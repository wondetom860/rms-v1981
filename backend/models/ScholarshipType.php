<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "scholarship_type".
 *
 * @property integer $id
 * @property string $schType_name
 * @property string $schType_by
 * @property string $schType_desc
 * @property integer $schType_created_by
 * @property string $schType_created_at
 * @property string $schType_updated_at
 * @property integer $schType_updated_by
 *
 * @property User $schTypeCreatedBy
 * @property User $schTypeUpdatedBy
 */
class ScholarshipType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scholarship_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['schType_name', 'schType_by', 'schType_created_by', 'schType_created_at', 'schType_updated_at', 'schType_updated_by','schType_pay'], 'required'],
            [['schType_desc'], 'string'],
            [['schType_created_by', 'schType_updated_by','schType_pay'], 'integer'],
            [['schType_created_at', 'schType_updated_at'], 'safe'],
            [['schType_name', 'schType_by'], 'string', 'max' => 40],
            [['schType_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['schType_created_by' => 'id']],
            [['schType_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['schType_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'schType_name' => Yii::t('rms', 'Scholarship Type Name'),
            'schType_by' => Yii::t('rms', 'Sponsered By'),
            'schType_pay' => Yii::t('rms', 'Fee free'),
            'schType_desc' => Yii::t('rms', 'Description'),
            'schType_created_by' => Yii::t('rms', 'Created By'),
            'schType_created_at' => Yii::t('rms', 'Created At'),
            'schType_updated_at' => Yii::t('rms', 'Updated At'),
            'schType_updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchTypeCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'schType_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchTypeUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'schType_updated_by']);
    }
}
