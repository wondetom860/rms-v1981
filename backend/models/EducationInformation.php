<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "education_information".
 *
 * @property integer $id
 * @property string $ei_G10_doc_scan
 * @property string $ei_G12_doc_scan
 * @property integer $ei_enteranceMarkPoint
 * @property string $ei_created_at
 * @property string $ei_updated_at
 * @property integer $ei_created_by
 * @property integer $ei_updated_by
 *
 * @property User $eiCreatedBy
 * @property User $eiUpdatedBy
 * @property EnteranceExamMark[] $enteranceExamMarks
 * @property PostSecondaryEducation[] $postSecondaryEducations
 * @property SecondarySchoolAttended[] $secondarySchoolAttendeds
 */
class EducationInformation extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'education_information';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ei_G10_doc_scan', 'ei_G12_doc_scan'], 'string'],
            [['ei_enteranceMarkPoint', 'ei_created_by', 'ei_updated_by'], 'integer'],
            [['ei_created_at', 'ei_updated_at'], 'safe'],
            [['ei_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ei_created_by' => 'id']],
            [['ei_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ei_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ei_G10_doc_scan' => Yii::t('rms', 'Ei  G10 Doc Scan'),
            'ei_G12_doc_scan' => Yii::t('rms', 'Ei  G12 Doc Scan'),
            'ei_enteranceMarkPoint' => Yii::t('rms', 'Enterance Mark Point'),
            'ei_created_at' => Yii::t('rms', 'Ei Created At'),
            'ei_updated_at' => Yii::t('rms', 'Ei Updated At'),
            'ei_created_by' => Yii::t('rms', 'Ei Created By'),
            'ei_updated_by' => Yii::t('rms', 'Ei Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEiCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ei_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEiUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ei_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnteranceExamMarks()
    {
        return $this->hasMany(EnteranceExamMark::class, ['eh_ei_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostSecondaryEducations()
    {
        return $this->hasMany(PostSecondaryEducation::class, ['pse_ei_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecondarySchoolAttendeds()
    {
        return $this->hasMany(SecondarySchoolAttended::class, ['ssa_ei_relId' => 'id']);
    }
}
