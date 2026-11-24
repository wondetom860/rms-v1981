<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "honor_list_control".
 *
 * @property integer $id
 * @property double $hl_acadYear_relId
 * @property double $hl_sem_relId
 * @property double $hl_maxSGPA
 * @property double $hl_minSGPA
 * @property integer $hl_created_by
 * @property integer $hl_created_at
 * @property integer $hl_updated_at
 * @property integer $hl_updated_by
 *
 * @property User $hlCreatedBy
 * @property User $hlUpdatedBy
 */
class HonorListControl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'honor_list_control';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hl_maxSGPA', 'hl_minSGPA', 'hl_created_by', 'hl_created_at','hl_acadYear_relId','hl_sem_relId'], 'required'],
            [['hl_maxSGPA', 'hl_minSGPA'], 'number'],
            [['hl_created_at','hl_updated_at'], 'safe'],
            [['hl_created_by', 'hl_updated_by','hl_acadYear_relId','hl_sem_relId'], 'integer'],
            [['hl_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['hl_created_by' => 'id']],
            [['hl_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['hl_updated_by' => 'id']],
            [['hl_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\SchoolYear::className(), 'targetAttribute' => ['hl_acadYear_relId' => 'id']],
            [['hl_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\Semester::className(), 'targetAttribute' => ['hl_sem_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'hl_maxSGPA' => Yii::t('rms', 'Max. SGPA'),
            'hl_minSGPA' => Yii::t('rms', 'Min. SGPA'),
            'hl_created_by' => Yii::t('rms', 'Created By'),
            'hl_created_at' => Yii::t('rms', 'Created At'),
            'hl_updated_at' => Yii::t('rms', 'Updated At'),
            'hl_acadYear_relId' => Yii::t('rms', 'Aacademic Year'),
            'hl_sem_relId' => Yii::t('rms', 'Semester'),
            'hl_updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHlCreatedBy()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'hl_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHlUpdatedBy()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'hl_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHlAcadYearRel()
    {
        return $this->hasOne(\backend\models\SchoolYear::className(), ['id' => 'hl_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHlSemRel()
    {
        return $this->hasOne(\backend\models\Semester::className(), ['id' => 'hl_sem_relId']);
    }
}

