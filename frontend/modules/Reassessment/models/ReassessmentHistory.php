<?php

namespace frontend\modules\Reassessment\models;

use Yii;
use common\models\User;
use backend\models\Student;
use backend\models\ContAssessCatagory2;
use backend\models\SemesterRegistration2Detail;
use backend\models\SchoolYear;
use backend\models\Semester;
use backend\models\GradeScale;
use backend\models\ClassLevel;
use backend\models\Course;
use backend\models\WGroupSetup;

/**
 * This is the model class for table "reassessment_history".
 *
 * @property int $id
 * @property int $ras_std_relId
 * @property int $ras_srd2_relId
 * @property int $ras_course_relId
 * @property int $ras_acadYear_relId
 * @property int $ras_sem_relId
 * @property int|null $ras_group_relId
 * @property int $ras_clslvl_relId
 * @property int|null $ras_status
 * @property int|null $ras_registrarApprove_status
 * @property int|null $ras_registrarApprove_by
 * @property string|null $ras_registrarApprove_at
 * @property int|null $ras_approved_by
 * @property int $ras_bef_grade_relId
 * @property string|null $ras_app_date
 * @property string $ras_created_at
 * @property string|null $ras_updated_at
 * @property int|null $ras_updated_by
 * @property int|null $ras_aft_grade_relId
 * @property float|null $ras_prevMark
 * @property float|null $ras_afterMark
 * @property int|null $ras_contAssCatag_relId
 *
 * @property ReassessmentCommittee[] $reassessmentCommittees
 * @property User $rasApprovedBy
 * @property Student $rasStdRel
 * @property Course $rasCourseRel
 * @property SchoolYear $rasAcadYearRel
 * @property Semester $rasSemRel
 * @property WGroupSetup $rasGroupRel
 * @property ClassLevel $rasClslvlRel
 * @property GradeScale $rasBefGradeRel
 * @property GradeScale $rasAftGradeRel
 * @property ContAssessCatagory2 $rasContAssCatagRel
 * @property ReassessorMark[] $reassessorMarks
 */
class ReassessmentHistory extends \yii\db\ActiveRecord
{

    public $image;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reassessment_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ras_std_relId', 'ras_srd2_relId', 'ras_course_relId', 'ras_acadYear_relId', 'ras_sem_relId', 'ras_clslvl_relId', 'ras_bef_grade_relId', 'ras_created_at'], 'required'],
            [['ras_std_relId', 'ras_srd2_relId', 'ras_course_relId', 'ras_acadYear_relId', 'ras_sem_relId', 'ras_group_relId', 'ras_clslvl_relId', 'ras_status', 'ras_registrarApprove_status', 'ras_registrarApprove_by', 'ras_approved_by', 'ras_bef_grade_relId', 'ras_updated_by', 'ras_aft_grade_relId', 'ras_contAssCatag_relId'], 'integer'],
            [['ras_registrarApprove_at', 'ras_app_date', 'ras_created_at', 'ras_updated_at'], 'safe'],
            [['ras_prevMark', 'ras_afterMark'], 'number'],
            [['image'], 'file'],
            [['ras_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ras_approved_by' => 'id']],
            [['ras_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['ras_std_relId' => 'id']],
            [['ras_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['ras_course_relId' => 'id']],
            [['ras_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['ras_acadYear_relId' => 'id']],
            [['ras_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['ras_sem_relId' => 'id']],
            [['ras_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['ras_group_relId' => 'id']],
            [['ras_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['ras_clslvl_relId' => 'id']],
            [['ras_bef_grade_relId'], 'exist', 'skipOnError' => true, 'targetClass' => GradeScale::class, 'targetAttribute' => ['ras_bef_grade_relId' => 'id']],
            [['ras_aft_grade_relId'], 'exist', 'skipOnError' => true, 'targetClass' => GradeScale::class, 'targetAttribute' => ['ras_aft_grade_relId' => 'id']],
            [['ras_contAssCatag_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ContAssessCatagory2::class, 'targetAttribute' => ['ras_contAssCatag_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ras_std_relId' => Yii::t('rms', 'Student'),
            'ras_srd2_relId' => Yii::t('rms', 'Enrollment'),
            'ras_course_relId' => Yii::t('rms', 'Course'),
            'ras_acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'ras_sem_relId' => Yii::t('rms', 'Semester'),
            'ras_group_relId' => Yii::t('rms', 'Grouped With'),
            'ras_clslvl_relId' => Yii::t('rms', 'Class Year'),
            'ras_status' => Yii::t('rms', 'Request Status'),
            'ras_registrarApprove_status' => Yii::t('rms', 'Registrar Approve Status'),
            'ras_registrarApprove_by' => Yii::t('rms', 'Registrar Approve By'),
            'ras_registrarApprove_at' => Yii::t('rms', 'Registrar Approve At'),
            'ras_approved_by' => Yii::t('rms', 'HoD Approval Status'),
            'ras_bef_grade_relId' => Yii::t('rms', 'Original Grade'),
            'ras_app_date' => Yii::t('rms', 'Date of Request'),
            'ras_created_at' => Yii::t('rms', 'Created At'),
            'ras_updated_at' => Yii::t('rms', 'Updated At'),
            'ras_updated_by' => Yii::t('rms', 'Updated By'),
            'ras_aft_grade_relId' => Yii::t('rms', 'Grade Changed To'),
            'ras_prevMark' => Yii::t('rms', 'Prev Mark'),
            'ras_afterMark' => Yii::t('rms', 'After Mark'),
            'ras_contAssCatag_relId' => Yii::t('rms', 'Assessments'),
            'ras_report_scan' => Yii::t('rms', 'Report Scan File')
        ];
    }

    public function updateContinousAssessment(){
        // reassessor_marks:
        // courseAssessmentEntries:
        $contAssessments = $this->rasSrd2Rel->getContinuousAssessmentEntries();
        $eror = 0;
        $erro_msg = "";
        foreach($contAssessments as $contAs){
            // get reassessor_mark:
            $reassessorMark = ReassessorMark::find()
                ->where([
                    'ras_ima_relId' => $contAs->ca_markAssignment_relId,
                    'ras_relId' => $this->id,
                    'ras_original_mark' => $contAs->ca_markObtained
                ])
                ->one();
            if(!empty($reassessorMark)){
                $contAs->ca_markObtained = $reassessorMark->ras_changed_to;
                $contAs->ca_updated_at = date('Y-m-d H:i:s');
                $contAs->ca_updated_by = Yii::$app->user->id;
                // $contAs->getData();
                if($contAs->save()){
                    continue;
                }else{
                    $erro_msg .= $contAs->getErrors();
                    $eror++;
                }
            }
        }
        if($erro_msg != ''){
            var_dump($erro_msg);
            exit;
        }else{
            return $this->rasSrd2Rel->getTotalMark();
        }
    }

    public function getImagePath()
    {
        // std_reg_id+course_code+request_id
        $text = $this->rasStdRel->std_regId .'_'. $this->rasCourseRel->course_code.'_'.$this->id;
        $text = str_replace(['/','-',' '],'',$text);
        $file_name = "/uploads/{$text}_barcode.jpg";
        return $file_name;
    }

    public function oneApproved(){
        return $this->getReassessmentCommittees()
            ->andWhere(['approval_status'=>1])
            ->count() >= 1;
    }

    public function allApproved(){
        return $this->getReassessmentCommittees()
            ->andWhere(['approval_status'=>NULL])
            ->count() == 0;
    }

    public function getRasHeader()
    {
        return $this->rasCourseRel->getCourseCredits();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRasSrd2Rel() //rasContAssCatagRel
    {
        return $this->hasOne(SemesterRegistration2Detail::class, ['id' => 'ras_srd2_relId']);
    }

    /**
     * Gets query for [[ReassessmentCommittees]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReassessmentCommittees()
    {
        return $this->hasMany(ReassessmentCommittee::class, ['ras_relId' => 'id']);
    }

    /**
     * Gets query for [[RasApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ras_approved_by']);
    }

    /**
     * Gets query for [[RasStdRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'ras_std_relId']);
    }

    /**
     * Gets query for [[RasCourseRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'ras_course_relId']);
    }

    /**
     * Gets query for [[RasAcadYearRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'ras_acadYear_relId']);
    }

    /**
     * Gets query for [[RasSemRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'ras_sem_relId']);
    }

    /**
     * Gets query for [[RasGroupRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasGroupRel()
    {
        return $this->hasOne(WGroupSetup::class, ['id' => 'ras_group_relId']);
    }

    /**
     * Gets query for [[RasClslvlRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'ras_clslvl_relId']);
    }

    /**
     * Gets query for [[RasBefGradeRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasBefGradeRel()
    {
        return $this->hasOne(GradeScale::class, ['id' => 'ras_bef_grade_relId']);
    }

    /**
     * Gets query for [[RasAftGradeRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasAftGradeRel()
    {
        return $this->hasOne(GradeScale::class, ['id' => 'ras_aft_grade_relId']);
    }

    /**
     * Gets query for [[RasRegistrarApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasRegistrarApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ras_registrar_approved_by']);
    }

    /**
     * Gets query for [[RasContAssCatagRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasContAssCatagRel()
    {
        return $this->hasOne(ContAssessCatagory2::class, ['id' => 'ras_contAssCatag_relId']);
    }

    /**
     * Gets query for [[ReassessorMarks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReassessorMarks()
    {
        return $this->hasMany(ReassessorMark::class, ['ras_relId' => 'id']);
    }
}