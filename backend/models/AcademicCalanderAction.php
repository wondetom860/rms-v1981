<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "academic_calander_action".
 *
 * @property integer $id
 * @property string $aca_name
 * @property integer $aca_created_by
 * @property string $aca_created_at
 * @property integer $aca_updated_by
 * @property string $aca_updated_at
 *
 * @property AcademicCalander[] $academicCalanders
 * @property User $acaCreatedBy
 * @property User $acaUpdatedBy
 */
class AcademicCalanderAction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'academic_calander_action';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aca_name', 'aca_created_by', 'aca_created_at', 'aca_updated_by', 'aca_updated_at'], 'required'],
            [['aca_created_by', 'aca_updated_by'], 'integer'],
            [['aca_created_at', 'aca_link', 'aca_updated_at'], 'safe'],
            [['aca_name','aca_link'], 'string', 'max' => 250],
            [['aca_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['aca_created_by' => 'id']],
            [['aca_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['aca_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        // aca_link
        return [
            'id' => Yii::t('rms', 'ID'),
            'aca_name' => Yii::t('rms', 'Academic Action Name'),
            'aca_link' => Yii::t('rms', 'Action Link (Optional)'),
            'aca_created_by' => Yii::t('rms', 'Created By'),
            'aca_created_at' => Yii::t('rms', 'Created At'),
            'aca_updated_by' => Yii::t('rms', 'Updated By'),
            'aca_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicCalanders()
    {
        return $this->hasMany(AcademicCalander::className(), ['acad_action_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcaCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'aca_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcaUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'aca_updated_by']);
    }
}
