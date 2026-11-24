<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "academic_qualification".
 *
 * @property integer $id
 * @property string $acq_name
 * @property string $acq_desc
 * @property integer $acq_created_by
 * @property string $acq_created_at
 * @property integer $acq_updated_by
 * @property string $acq_updated_at
 *
 * @property User $acqCreatedBy
 * @property User $acqUpdatedBy
 * @property Instructor[] $instructors
 */
class AcademicQualification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'academic_qualification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['acq_name', 'acq_desc', 'acq_created_by', 'acq_created_at', 'acq_updated_by', 'acq_updated_at'], 'required'],
            [['acq_desc'], 'string'],
            [['acq_created_by', 'acq_updated_by'], 'integer'],
            [['acq_created_at', 'acq_updated_at'], 'safe'],
            [['acq_name'], 'string', 'max' => 50],
            [['acq_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['acq_created_by' => 'id']],
            [['acq_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['acq_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'acq_name' => Yii::t('rms', 'Academic Qualification Name'),
            'acq_desc' => Yii::t('rms', 'Description'),
            'acq_created_by' => Yii::t('rms', 'Created By'),
            'acq_created_at' => Yii::t('rms', 'Created At'),
            'acq_updated_by' => Yii::t('rms', 'Updated By'),
            'acq_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcqCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'acq_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcqUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'acq_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstructors()
    {
        return $this->hasMany(Instructor::className(), ['inst_acq_reIId' => 'id']);
    }
}
