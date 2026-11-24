<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "enterance_exam_mark".
 *
 * @property integer $id
 * @property integer $eem_ei_relId
 * @property string $eem_subject
 * @property string $eem_reference_number
 * @property string $eem_year
 * @property integer $eem_markObtained
 * @property string $eem_created_at
 * @property integer $eem_created_by
 * @property string $eem_updated_at
 * @property integer $eem_updated_by
 *
 * @property EducationInformation $ehEiRel
 */
class EnteranceExamMark extends \yii\db\ActiveRecord
{
    public $attendedPSE = NULL;
    public $ar_request;

    public $std_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enterance_exam_mark';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['eem_ei_relId', 'eem_markObtained', 'eem_created_by', 'eem_updated_by','std_id','ar_request'], 'integer'],
            [['eem_year', 'eem_created_at', 'eem_updated_at'], 'safe'],
            [['eem_subject'], 'string', 'max' => 25],
            [['eem_markObtained'], 'number', 'max' => 100],
            [['eem_markObtained'], 'number', 'min' => 0],
            [['eem_reference_number'], 'string', 'max' => 30],
            [['eem_ei_relId'], 'exist', 'skipOnError' => true, 'targetClass' => EducationInformation::class, 'targetAttribute' => ['eem_ei_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'eem_ei_relId' => Yii::t('rms', 'Eh Ei Rel ID'),
            'eem_subject' => Yii::t('rms', 'Subject'),
            'eem_reference_number' => Yii::t('rms', 'Ref. Number'),
            'eem_year' => Yii::t('rms', 'Year'),
            'eem_markObtained' => Yii::t('rms', 'Mark Obtained'),
            'eem_created_at' => Yii::t('rms', 'Created At'),
            'eem_created_by' => Yii::t('rms', 'Created By'),
            'eem_updated_at' => Yii::t('rms', 'Updated At'),
            'eem_updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    public function suchExists($id = null)
    {
        return $id ? self::find()
            ->where([
                'eem_ei_relId' => $this->eem_ei_relId,
                'eem_subject' => $this->eem_subject,
                'eem_reference_number' => $this->eem_reference_number
            ])
            ->andWhere(' id <>  ' . $id)
            ->count() > 0 :  self::find()
            ->where([
                'eem_ei_relId' => $this->eem_ei_relId,
                'eem_subject' => $this->eem_subject,
                'eem_reference_number' => $this->eem_reference_number
            ])
            ->count() > 0;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEhEiRel()
    {
        return $this->hasOne(EducationInformation::class, ['id' => 'eem_ei_relId']);
    }
}
