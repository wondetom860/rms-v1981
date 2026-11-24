<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "clearance_allowed_student".
 *
 * @property int $id
 * @property string $cas_full_name
 * @property string $cas_regId
 * @property string $cas_acadYear
 * @property string $cas_semester
 * @property string $cas_admission
 * @property string $cas_program
 *
 * @property string $cas_gender
 * @property string $cas_reason//
 * @property string $cas_nationality
 * @property string $cas_dob
 *
 * @property string $cas_department
 * @property string $cas_clslvl
 * @property string|null $cas_spec
 * @property string $created_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $updated_at
 */
class ClearanceAllowedStudent extends \yii\db\ActiveRecord
{
    public $_reasons = [
        'GRADUATION' => 'Graduation',
        'WITHDRAWAL' => 'WithDrawal',
        'ACADEMIC DISMISSAL' => 'Academic Dismissal',
        'URGENT MISSION' => 'Urgent Assignment',
        'OTHER' => 'Other',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clearance_allowed_student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cas_full_name', 'cas_regId', 'cas_acadYear', 'cas_semester', 'cas_admission', 'cas_program', 'cas_department', 'cas_clslvl', 'created_at', 'created_by'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['cas_full_name'], 'string', 'max' => 255],
            [['cas_regId'], 'string', 'max' => 15],
            [['cas_reason'], 'string', 'max' => 67],
            [['cas_acadYear'], 'string', 'max' => 8],
            [['cas_semester'], 'string', 'max' => 5],
            [['cas_gender'], 'string', 'max' => 10],
            [['cas_dob'], 'string', 'max' => 20],
            [['cas_admission', 'cas_program', 'cas_department', 'cas_clslvl', 'cas_spec', 'cas_nationality'], 'string', 'max' => 50],
            [['created_by', 'updated_by'], 'string', 'max' => 78],
        ];
    }

    public function requestedForThis($modelSemreg)
    {
        return $this->find()
            ->where([
                'cas_regId' => $modelSemreg->semregStdRel->std_regId,
                'cas_acadYear' => $modelSemreg->semregAcadYearRel->sch_year_desc,
                'cas_semester' => $modelSemreg->semregSemesterRel->semester_name,
            ])
            ->count() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'cas_full_name' => Yii::t('rms', 'Student Name'),
            'cas_regId' => Yii::t('rms', 'Reg. ID'),
            'cas_acadYear' => Yii::t('rms', 'Acad. Year'),
            'cas_semester' => Yii::t('rms', 'Semester'),
            'cas_admission' => Yii::t('rms', 'Admission'),
            'cas_nationality' => Yii::t('rms', 'Nationlaity'),
            'cas_dob' => Yii::t('rms', 'Date of Birth'),
            'cas_reason' => Yii::t('rms', 'Reason'),
            'cas_gender' => Yii::t('rms', 'Gender'),
            'cas_program' => Yii::t('rms', 'Program'),
            'cas_department' => Yii::t('rms', 'Department'),
            'cas_clslvl' => Yii::t('rms', 'Class Year'),
            'cas_spec' => Yii::t('rms', 'Specialization'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }
}
