<?php

namespace frontend\modules\WithdrawalReadmission\models;

use Yii;
use backend\models\SchoolYear;
use backend\models\Semester;
use backend\models\Student;
use backend\models\User;
use backend\models\WithdrawalReason;
use backend\models\WGroupAssignment;

/**
 * This is the model class for table "w_student_withdrawal_history".
 *
 * @property int $id
 * @property int $swh_acadYear_relId
 * @property int $swh_sem_relId
 * @property int $section_relId
 * @property int $swh_std_relId
 * @property int $swh_reason_relId
 * @property int $swh_course_removed Indicates whether enrolled course removed or not. the default is o meaning NOT
 * @property int|null $swh_approved
 * @property int|null $swh_approved_by
 * @property string|null $swh_approved_at
 * @property string $swh_created_at
 * @property int $swh_created_by
 * @property string|null $swh_updated_at
 * @property int|null $swh_updated_by
 * @property int|null $swh_HOD_approved
 * @property string|null $swh_HOD_approved_at
 * @property int|null $swh_HOD_approved_by
 * @property int|null $swh_returnedBack
 * @property int|null $swh_stdSigned
 * @property string|null $swh_stdSigned_at
 * @property int|null $swh_stdSigned_by
 * @property int|null $swh_lastApproved
 * @property string|null $swh_lastApproved_at
 * @property int|null $swh_lastApproved_by
 * @property string|null $swh_supDoc
 * @property string|null $swh_last_dateAttended
 *
 * @property RemovedCourse[] $removedCourses
 * @property WStudentReadmissionHistory[] $wStudentReadmissionHistories
 * @property SchoolYear $swhAcadYearRel
 * @property Semester $swhSemRel
 * @property User $swhApprovedBy
 * @property User $swhCreatedBy
 * @property User $swhHODApprovedBy
 * @property User $swhLastApprovedBy
 * @property User $swhStdSignedBy
 * @property Student $swhStdRel
 * @property WithdrawalReason $swhReasonRel
 * @property User $swhUpdatedBy
 */
class WStudentWithdrawalHistory extends \yii\db\ActiveRecord
{

    public $std_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'w_student_withdrawal_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['swh_acadYear_relId', 'swh_sem_relId', 'swh_std_relId', 'swh_reason_relId', 'swh_created_at', 'swh_created_by'], 'required'],
            [['swh_acadYear_relId', 'swh_sem_relId', 'swh_std_relId', 'swh_reason_relId', 'swh_course_removed', 'swh_approved', 'swh_approved_by', 'swh_created_by', 'swh_updated_by', 'swh_HOD_approved', 'swh_HOD_approved_by', 'swh_returnedBack', 'swh_stdSigned', 'swh_stdSigned_by', 'swh_lastApproved', 'swh_lastApproved_by','section_relId'], 'integer'],
            [['swh_approved_at', 'swh_created_at', 'swh_updated_at', 'swh_HOD_approved_at', 'swh_stdSigned_at', 'swh_lastApproved_at', 'swh_last_dateAttended'], 'safe'],
            [['swh_supDoc','std_name'], 'string'],
            [['swh_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::className(), 'targetAttribute' => ['swh_acadYear_relId' => 'id']],
            [['swh_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::className(), 'targetAttribute' => ['swh_sem_relId' => 'id']],
            [['swh_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['swh_approved_by' => 'id']],
            [['swh_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['swh_created_by' => 'id']],
            [['swh_HOD_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['swh_HOD_approved_by' => 'id']],
            [['swh_lastApproved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['swh_lastApproved_by' => 'id']],
            [['swh_stdSigned_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['swh_stdSigned_by' => 'id']],
            [['swh_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['swh_std_relId' => 'id']],
            [['swh_reason_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WithdrawalReason::className(), 'targetAttribute' => ['swh_reason_relId' => 'id']],
            [['swh_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['swh_updated_by' => 'id']],
            [['section_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupAssignment::className(), 'targetAttribute' => ['section_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'std_name' => Yii::t('rms', 'Student Name'),
            'section_relId' => Yii::t('rms', 'Student Section'),
            'swh_acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'swh_sem_relId' => Yii::t('rms', 'Semester'),
            'swh_std_relId' => Yii::t('rms', 'Student Name'),
            'swh_reason_relId' => Yii::t('rms', 'Withdrawal Reason'),
            'swh_course_removed' => Yii::t('rms', 'Course Removed'),
            'swh_approved' => Yii::t('rms', 'Approved'),
            'swh_approved_by' => Yii::t('rms', 'Approved By'),
            'swh_approved_at' => Yii::t('rms', 'Approved At'),
            'swh_created_at' => Yii::t('rms', 'Created At'),
            'swh_created_by' => Yii::t('rms', 'Created By'),
            'swh_updated_at' => Yii::t('rms', 'Updated At'),
            'swh_updated_by' => Yii::t('rms', 'Updated By'),
            'swh_HOD_approved' => Yii::t('rms', 'Hod Approved'),
            'swh_HOD_approved_at' => Yii::t('rms', 'Hod Approved At'),
            'swh_HOD_approved_by' => Yii::t('rms', 'Hod Approved By'),
            'swh_returnedBack' => Yii::t('rms', 'Re-admitted'),
            'swh_stdSigned' => Yii::t('rms', 'Student agreement:'),
            'swh_stdSigned_at' => Yii::t('rms', 'Std Signed At'),
            'swh_stdSigned_by' => Yii::t('rms', 'Std Signed By'),
            'swh_lastApproved' => Yii::t('rms', 'Last Approved'),
            'swh_lastApproved_at' => Yii::t('rms', 'Last Approved At'),
            'swh_lastApproved_by' => Yii::t('rms', 'Last Approved By'),
            'swh_supDoc' => Yii::t('rms', 'Sup Doc'),
            'swh_last_dateAttended' => Yii::t('rms', 'Last attended section'),
        ];
    }

    public function checkIfRequestPossible($semregModel){
        // checks for validity of withdrawal request:
        $reason = $this->swhReasonRel;
        if ($reason->readmittable) {
            if ($semregModel->semreg_gradeCompleted) {
                // check against the readmision entry:
                $modelReadmissionControl = new \backend\models\ReadmissionCalculationControl();
                return $modelReadmissionControl->checkIfReadmissionPossible($semregModel);
            }else{
                // removing enrolled course for re-enrolling upon rejoining is possibel
                if ($reason->remove_course_enrolls) {
                    // emergency cases, pregnancy, family case, medical case, etc....
                    // remove course enrollment entries latter
                    \Yii::$app->session->setFlash('type','info');
                    \Yii::$app->session->setFlash('message','Course enrolled will be removed. >> Emergency cases, medical cases, urgent mission assignment,...');
                    return true;
                }else{
                    //readmittable - without removing enrollments: => readmission control shall be satisfied.
                    
                    \Yii::$app->session->setFlash('type','error');
                    \Yii::$app->session->setFlash('message','Withdrawal impossible, unable to determine.');
                    return false;
                }
            }
        }else{
            if ($reason->remove_course_enrolls) {
                // remove course enrollment entries latter
                \Yii::$app->session->setFlash('type','info');
                \Yii::$app->session->setFlash('message','Course enrolled will be removed. >> complete dismissal, no readmission');
                return true;
            }else{
                // no need of removing course enrollemnt entries
                // academic dismission for good
                \Yii::$app->session->setFlash('type','info');
                \Yii::$app->session->setFlash('message','Course enrolled will NOT be removed. >> academic dismission for good');
                return true;
            }
        }
    }

    public function hookUpSrd2s($semregModel){
        if ($semregModel) {
            foreach ($semregModel->semesterRegistration2Details as $sdr2) {
                $rc = new \backend\models\RemovedCourse();
                $rc->rc_acadYear_relId = $this->swh_acadYear_relId;
                $rc->rc_sem_relId = $this->swh_sem_relId;
                $rc->rc_std_relId = $semregModel->semreg_std_relId;
                $rc->rc_withdrawal_relId = $this->id;
                $rc->rc_course_relId = $sdr2->srd2_course_relId;
                $rc->rc_coff_relId = $sdr2->srd2_coff_relId;

                $rc->rc_grade = is_null($sdr2->srd2_grade_relId)?NULL:\backend\models\GradeScale::findOne($sdr2->srd2_grade_relId)->gs_ltr_grade;

                if (!$rc->save()) {
                    $message = "Error while buck_uping course enrollment information. not all courses bucked up.";
                    // var_dump($rc->getErrors());die();
                }
                unset($rc);
            }
        }
    }

    public function checkIfRequestedBefore(){
        // student has already in-porcess withdrawal request: return it else null
        return self::find()
            ->where([
                'swh_std_relId' => $this->swh_std_relId,
            ])
            ->andWhere([ 'OR',
                ['swh_stdSigned' => NULL],
                ['swh_HOD_approved' => NULL],
                ['swh_lastApproved' => NULL],
                ['swh_returnedBack' => NULL],
            ])
            ->one();
    }

    public function getSetStudentSection(){
        $gAssModel = new WGroupAssignment();
        $gas = $gAssModel->getStudentGroupAssignment($this->swhAcadYearRel,$this->swhSemRel,$this->swhStdRel);
        if ($gas) {
            $this->section_relId = $gas->id;
            $this->update(false);
            return;
        }
    }

    /**
     * Gets query for [[RemovedCourses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRemovedCourses()
    {
        return $this->hasMany(RemovedCourse::class, ['rc_withdrawal_relId' => 'id']);
    }

    /**
     * Gets query for [[WStudentReadmissionHistories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWStudentReadmissionHistories()
    {
        return $this->hasMany(WStudentReadmissionHistory::className(), ['srh_swh_relId' => 'id']);
    }

    /**
     * Gets query for [[SwhAcadYearRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSwhAcadYearRel()
    {
        return $this->hasOne(SchoolYear::className(), ['id' => 'swh_acadYear_relId']);
    }

    /**
     * Gets query for [[SwhAcadYearRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSectionRel()
    {
        return $this->hasOne(WGroupAssignment::className(), ['id' => 'section_relId']);
    }

    /**
     * Gets query for [[SwhSemRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSwhSemRel()
    {
        return $this->hasOne(Semester::className(), ['id' => 'swh_sem_relId']);
    }

    /**
     * Gets query for [[SwhApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSwhApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'swh_approved_by']);
    }

    /**
     * Gets query for [[SwhCreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSwhCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'swh_created_by']);
    }

    /**
     * Gets query for [[SwhHODApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSwhHODApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'swh_HOD_approved_by']);
    }

    /**
     * Gets query for [[SwhLastApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSwhLastApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'swh_lastApproved_by']);
    }

    /**
     * Gets query for [[SwhStdSignedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSwhStdSignedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'swh_stdSigned_by']);
    }

    /**
     * Gets query for [[SwhStdRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSwhStdRel()
    {
        return $this->hasOne(Student::className(), ['id' => 'swh_std_relId']);
    }

    /**
     * Gets query for [[SwhReasonRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSwhReasonRel()
    {
        return $this->hasOne(WithdrawalReason::className(), ['id' => 'swh_reason_relId']);
    }

    /**
     * Gets query for [[SwhUpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSwhUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'swh_updated_by']);
    }
}
