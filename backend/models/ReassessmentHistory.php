<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "reassessment_history".
 *
 * @property integer $id
 * @property integer $ras_std_relId
 * @property integer $ras_srd2_relId
 * @property integer $ras_course_relId
 * @property integer $ras_acadYear_relId
 * @property integer $ras_sem_relId
 * @property integer $ras_group_relId
 * @property integer $ras_clslvl_relId
 * @property integer $ras_status
 * @property integer $ras_approved_by
 * @property integer $ras_bef_grade_relId
 * @property string $ras_app_date
 * @property string $ras_created_at
 * @property string $ras_updated_at
 * @property integer $ras_updated_by
 * @property integer $ras_aft_grade_relId
 *
 * @property User $rasApprovedBy
 * @property Student $rasStdRel
 * @property Course $rasCourseRel
 * @property SchoolYear $rasAcadYearRel
 * @property Semester $rasSemRel
 * @property WGroupSetup $rasGroupRel 
 * @property ClassLevel $rasClslvlRel
 * @property GradeScale $rasBefGradeRel
 * @property GradeScale $rasAftGradeRel
 */

class ReassessmentHistory extends \yii\db\ActiveRecord
{

    public $first_name,$course_title,$totalMark,$beforemark,$beforegrade, $afterMark, $afterGrade;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reassessment_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ras_std_relId', 'ras_srd2_relId', 'ras_course_relId', 'ras_acadYear_relId', 'ras_sem_relId', 'ras_clslvl_relId', 'ras_bef_grade_relId', 'ras_created_at'], 'required'],
            [['ras_std_relId', 'ras_srd2_relId', 'ras_course_relId', 'ras_acadYear_relId', 'ras_sem_relId', 'ras_group_relId', 'ras_clslvl_relId', 'ras_status', 'ras_approved_by', 'ras_bef_grade_relId', 'ras_updated_by', 'ras_aft_grade_relId'], 'integer'],
            [['ras_app_date', 'ras_created_at', 'ras_updated_at','course_title','first_name'], 'safe'],
            [['ras_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ras_approved_by' => 'id']],
            [['ras_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['ras_std_relId' => 'id']],
            [['ras_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['ras_course_relId' => 'id']],
            [['ras_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['ras_acadYear_relId' => 'id']],
            [['ras_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['ras_sem_relId' => 'id']],
            [['ras_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['ras_group_relId' => 'id']],
            [['ras_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['ras_clslvl_relId' => 'id']],
            [['ras_bef_grade_relId'], 'exist', 'skipOnError' => true, 'targetClass' => GradeScale::class, 'targetAttribute' => ['ras_bef_grade_relId' => 'id']],
            [['ras_aft_grade_relId'], 'exist', 'skipOnError' => true, 'targetClass' => GradeScale::class, 'targetAttribute' => ['ras_aft_grade_relId' => 'id']],
        ];
    } 

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'course_title' => Yii::t('rms', 'Course Title'),
            'first_name' => Yii::t('rms', 'Student Name'),
            'ras_std_relId' => Yii::t('rms', 'Student Name'),
            'ras_srd2_relId' => Yii::t('rms', 'Course Registration Entry'),
            'ras_course_relId' => Yii::t('rms', 'Course'),
            'ras_acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'ras_sem_relId' => Yii::t('rms', 'Semester'),
            'ras_group_relId' => Yii::t('rms', 'Group'),
            'ras_clslvl_relId' => Yii::t('rms', 'Class Year'),
            'ras_status' => Yii::t('rms', 'Status'),
            'totalMark' => Yii::t('rms', 'Total Mark Obtained'),
            'ras_afterMark' => Yii::t('rms', 'Mark Obtained After Correction'),
            'ras_prevMark' => Yii::t('rms', 'Previous Mark'),
            'ras_contAssCatag_relId' => Yii::t('rms', 'Assessment Entry'),
            'ras_approved_by' => Yii::t('rms', 'Approved By'),
            'ras_bef_grade_relId' => Yii::t('rms', 'Before Grade'),
            'ras_app_date' => Yii::t('rms', 'Approve Date'),
            'ras_created_at' => Yii::t('rms', 'Created At'),
            'ras_updated_at' => Yii::t('rms', 'Updated At'),
            'ras_updated_by' => Yii::t('rms', 'Updated By'),
            'ras_aft_grade_relId' => Yii::t('rms', 'After Grade'),
            'ras_registrarApprove_status' => Yii::t('rms', 'Registrar Approval'),
        ];
    }

    public function propagateOperation(){
        $contAss = ContinuousAssessment2::find()
            ->where(['ca_std_relId'=>$this->ras_std_relId])
            ->innerJoin('instance_mark_assignment','instance_mark_assignment.id = continuous_assessment2.ca_markAssignment_relId')
            ->andWhere(['instance_mark_assignment.ima_catagorySetup_relId'=>$this->ras_contAssCatag_relId])
            ->one();

        // var_dump($this);die();

        $modelO = new ContinuousAssessment2();
        $gradeUpdate = $modelO->updateFinalExamMark($contAss,$this->ras_afterMark,$this->ras_course_relId);
        // return $gradeUpdate->gs_ltr_grade;
         if ($gradeUpdate && count($gradeUpdate) > 0) {
                $result = Yii::$app->db->createCommand()
                    ->update('reassessment_history',
                        [
                            'ras_aft_grade_relId'=>$gradeUpdate->id,
                            'ras_updated_by'=>Yii::$app->user->identity->id,
                            'ras_updated_at'=>date('Y-m-d h:i:s'),
                         ],

                        'id='.$this->id
                        )  
                    ->execute(); 
            return true;
        }else{
            return "Final Exam Entry updating failed. Please try again";
        }
    }

    public function hisDeptCourse(){
        if (\Yii::$app->user->can('DepartmentHead')) {
            $instructor = Instructor::findOne(['inst_user_relId' => \Yii::$app->user->identity->id]);
            return $instructor->inst_dep_relId == $this->rasCourseRel->course_dept_relId;
        }else{
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRasApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ras_approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRasStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'ras_std_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRasCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'ras_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRasContAssCatagRel()//rasContAssCatagRel
    {
        return $this->hasOne(ContAssessCatagory2::class, ['id' => 'ras_contAssCatag_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRasSrd2Rel()//rasContAssCatagRel
    {
        return $this->hasOne(SemesterRegistration2Detail::class, ['id' => 'ras_srd2_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRasAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'ras_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRasSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'ras_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRasGroupRel()
    {
        return $this->hasOne(WGroupSetup::class, ['id' => 'ras_group_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRasClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'ras_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRasBefGradeRel()
    {
        return $this->hasOne(GradeScale::class, ['id' => 'ras_bef_grade_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRasAftGradeRel()
    {
        return $this->hasOne(GradeScale::class, ['id' => 'ras_aft_grade_relId']);
    }
}
