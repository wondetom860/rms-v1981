<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "student_type".
 *
 * @property integer $id
 * @property string $stdType_name
 * @property string $stdType_desc
 * @property integer $stdType_created_by
 * @property string $stdType_created_at
 * @property integer $stdType_updated_by
 * @property string $stdType_updated_at
 *
 * @property User $stdTypeCreatedBy
 * @property User $stdTypeUpdatedBy
 */
class StudentType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'student_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stdType_name', 'stdType_desc', 'stdType_created_by', 'stdType_created_at', 'stdType_updated_by', 'stdType_updated_at'], 'required'],
            [['stdType_desc'], 'string'],
            [['stdType_created_by', 'stdType_updated_by'], 'integer'],
            [['stdType_created_at', 'stdType_updated_at'], 'safe'],
            [['stdType_name'], 'string', 'max' => 25],
            [['stdType_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['stdType_created_by' => 'id']],
            [['stdType_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['stdType_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'stdType_name' => Yii::t('rms', 'Std Type Name'),
            'stdType_desc' => Yii::t('rms', 'Std Type Desc'),
            'stdType_created_by' => Yii::t('rms', 'Std Type Created By'),
            'stdType_created_at' => Yii::t('rms', 'Std Type Created At'),
            'stdType_updated_by' => Yii::t('rms', 'Std Type Updated By'),
            'stdType_updated_at' => Yii::t('rms', 'Std Type Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdTypeCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'stdType_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdTypeUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'stdType_updated_by']);
    }
}
