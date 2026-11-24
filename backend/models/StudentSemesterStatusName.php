<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "student_semester_status_name".
 *
 * @property integer $id
 * @property string $sssn_name
 * @property string $sssn_desc
 * @property string $sssn_pass_status
 *
 * @property StdStatusCalculationControlAc[] $stdStatusCalculationControlAcs
 * @property StdStatusCalculationControlDefault[] $stdStatusCalculationControlDefaults
 * @property StudentSemesterStatusHistory[] $studentSemesterStatusHistories
 */
class StudentSemesterStatusName extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'student_semester_status_name';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sssn_name', 'sssn_pass_status', 'sssn_desc'], 'required'],
            [['sssn_desc'], 'string'],
            [['sssn_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'sssn_name' => Yii::t('rms', 'Semester Status Name'),
            'sssn_desc' => Yii::t('rms', 'Description'),
            'sssn_pass_status' => Yii::t('rms', 'Pass Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdStatusCalculationControlAcs()
    {
        return $this->hasMany(StdStatusCalculationControlAc::class, ['sssccAC_sssn_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdStatusCalculationControlDefaults()
    {
        return $this->hasMany(StdStatusCalculationControlDefault::class, ['sssccd_sssn_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentSemesterStatusHistories()
    {
        return $this->hasMany(StudentSemesterStatusHistory::class, ['sssh_sssn_relId' => 'id']);
    }
}
