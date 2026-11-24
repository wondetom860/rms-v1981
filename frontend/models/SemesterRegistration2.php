<?php

namespace frontend\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "semester_registration_2".
 *
 * @property integer $id
 * @property integer $semreg_std_relId
 * @property integer $semreg_acadYear_relId
 * @property integer $semreg_clslvl_relId
 * @property integer $semreg_semester_relId
 * @property double $semreg_SGPA
 * @property double $semreg_CGPA
 * @property integer $semreg_approved
 * @property integer $semreg_approved_by
 * @property string $semreg_approved_at
 * @property integer $semreg_created_by
 * @property integer $semreg_updated_by
 * @property string $semreg_created_at
 * @property string $semreg_updated_at
 *
 * @property SchoolYear $semregAcadYearRel
 * @property User $semregApprovedBy
 * @property ClassLevel $semregClslvlRel
 * @property User $semregCreatedBy
 * @property Semester $semregSemesterRel
 * @property Student $semregStdRel
 * @property User $semregUpdatedBy
 * @property SemesterRegistration2Detail[] $semesterRegistration2Details
 */
class SemesterRegistration2 extends \yii\db\ActiveRecord
{
    public $enrollCourses = null;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_registration_2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['semreg_std_relId', 'semreg_acadYear_relId', 'semreg_clslvl_relId', 'semreg_semester_relId', 'semreg_created_by', 'semreg_updated_by', 'semreg_created_at', 'semreg_updated_at'], 'required'],
            [['semreg_std_relId', 'semreg_acadYear_relId', 'semreg_clslvl_relId', 'semreg_semester_relId', 'semreg_approved', 'semreg_approved_by', 'semreg_created_by', 'semreg_updated_by','semreg_last_ssn_relId'], 'integer'],
            [['semreg_SGPA', 'semreg_CGPA'], 'number'],
            [['semreg_approved_at', 'semreg_created_at', 'semreg_updated_at'], 'safe'],
            [['semreg_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\SchoolYear::class, 'targetAttribute' => ['semreg_acadYear_relId' => 'id']],
            [['semreg_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\User::class, 'targetAttribute' => ['semreg_approved_by' => 'id']],
            [['semreg_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\ClassLevel::class, 'targetAttribute' => ['semreg_clslvl_relId' => 'id']],
            [['semreg_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\User::class, 'targetAttribute' => ['semreg_created_by' => 'id']],
            [['semreg_semester_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\Semester::class, 'targetAttribute' => ['semreg_semester_relId' => 'id']],
            [['semreg_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\Student::class, 'targetAttribute' => ['semreg_std_relId' => 'id']],
            [['semreg_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\User::class, 'targetAttribute' => ['semreg_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'semreg_std_relId' => Yii::t('rms', 'Semreg Std Rel ID'),
            'semreg_acadYear_relId' => Yii::t('rms', 'Semreg Acad Year Rel ID'),
            'semreg_clslvl_relId' => Yii::t('rms', 'Semreg Clslvl Rel ID'),
            'semreg_semester_relId' => Yii::t('rms', 'Semester'),
            'semreg_SGPA' => Yii::t('rms', 'Semreg  Sgpa'),
            'semreg_last_ssn_relId' => Yii::t('rms', 'Last Semester Status'),
            'semreg_CGPA' => Yii::t('rms', 'Semreg  Cgpa'),
            'semreg_approved' => Yii::t('rms', 'Semreg Approved'),
            'semreg_approved_by' => Yii::t('rms', 'Semreg Approved By'),
            'semreg_approved_at' => Yii::t('rms', 'Semreg Approved At'),
            'semreg_created_by' => Yii::t('rms', 'Semreg Created By'),
            'semreg_updated_by' => Yii::t('rms', 'Semreg Updated By'),
            'semreg_created_at' => Yii::t('rms', 'Semreg Created At'),
            'semreg_updated_at' => Yii::t('rms', 'Semreg Updated At'),
        ];
    }

    public function getSemesterGrade($studentObj,$prevGset){
        return $this->find()
            ->where([
                'semreg_acadYear_relId'=>$prevGset->gs_acadYear_relId,
                'semreg_semester_relId'=>$prevGset->gs_sem_relId,
                'semreg_std_relId'=>$studentObj->id,
                'semreg_clslvl_relId'=>$prevGset->gs_clslvl_relId,
                'semreg_gradeCompleted'=>1,
            ])
            ->one();
    }

    public function evaluatedAll(){

        if ($this->semreg_evaluatedAll == 1) {
            return true;
        }

        $srd2s = $this->semesterRegistration2Details;

        if ($srd2s && count($srd2s) > 0) {//indicates student enrolled some course
            $updt = true;
            foreach ($srd2s as $keySrd2) {
                if ($keySrd2->srd2_evaluationCompleted) {
                    continue;
                }else{
                    $evaluated = $keySrd2->evaluatedCourseInstructors();
                    if ($evaluated == true) {
                        continue;
                    }else if(is_numeric($evaluated) && $evaluated == 1){
                        $updt = false;
                    }else{
                        return false;
                    }
                }
            }
            if ($updt) {
                $this->semreg_evaluatedAll = 1;
                $this->update();
            }
            
            return true;
        }else{
            return true;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregAcadYearRel()
    {
        return $this->hasOne(\backend\models\SchoolYear::class, ['id' => 'semreg_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'semreg_approved_by']);
    }

    // semregLastSsnRel
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregLastSsnRel()
    {
        return $this->hasOne(\backend\models\StudentSemesterStatusName::class, ['id' => 'semreg_last_ssn_relId']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregClslvlRel()
    {
        return $this->hasOne(\backend\models\ClassLevel::class, ['id' => 'semreg_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'semreg_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregSemesterRel()
    {
        return $this->hasOne(\backend\models\Semester::class, ['id' => 'semreg_semester_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregStdRel()
    {
        return $this->hasOne(\backend\models\Student::class, ['id' => 'semreg_std_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'semreg_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2Detail()
    {
        return $this->hasOne(SemesterRegistration2Detail::class, ['srd2_semreg_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2Details()
    {
        return $this->hasMany(SemesterRegistration2Detail::class, ['srd2_semreg_relId' => 'id']);
    }
}
