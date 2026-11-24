<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "w_student_withdrawal_history".
 *
 * @property integer $id
 * @property integer $swh_acadYear_relId
 * @property integer $swh_sem_relId
 * @property integer $swh_std_relId
 * @property integer $swh_reason_relId
 * @property integer $swh_course_removed
 * @property integer $swh_approved//
 * @property string $swh_igraded_srd2s
 * @property integer $swh_approved_by
 * @property string $swh_approved_at
 * @property string $swh_created_at
 * @property integer $swh_created_by
 * @property string $swh_updated_at
 * @property integer $swh_updated_by
 * @property integer $swh_HOD_approved
 * @property string $swh_HOD_approved_at
 * @property integer $swh_HOD_approved_by
 *
 * @property SchoolYear $swhAcadYearRel
 * @property User $swhApprovedBy
 * @property User $swhCreatedBy
 * @property WithdrawalReason $swhReasonRel
 * @property Semester $swhSemRel
 * @property Student $swhStdRel
 * @property User $swhUpdatedBy
 */
class WStudentWithdrawalHistory extends \yii\db\ActiveRecord
{

    public $applyForm = 0;
    public $removedCourseList;
    public $reasonType;
    public $gsetId;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_student_withdrawal_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['swh_acadYear_relId', 'swh_sem_relId', 'swh_std_relId', 'swh_reason_relId', 'swh_created_at', 'swh_created_by'], 'required'],
            [['swh_acadYear_relId', 'swh_sem_relId', 'swh_std_relId', 'swh_reason_relId', 'swh_course_removed', 'swh_approved', 'swh_approved_by', 'swh_created_by', 'swh_updated_by', 'swh_HOD_approved', 'swh_HOD_approved_by'], 'integer'],
            [['swh_approved_at', 'swh_created_at', 'swh_updated_at', 'swh_HOD_approved_at'], 'safe'],
            [['removedCourseList', 'gsetId'], 'string'],
            [['reasonType'], 'integer'],
            [['swh_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['swh_acadYear_relId' => 'id']],
            [['swh_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['swh_approved_by' => 'id']],
            [['swh_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['swh_created_by' => 'id']],
            [['swh_reason_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WithdrawalReason::class, 'targetAttribute' => ['swh_reason_relId' => 'id']],
            [['swh_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['swh_sem_relId' => 'id']],
            [['swh_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['swh_std_relId' => 'id']],
            [['swh_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['swh_updated_by' => 'id']],
        ];
    }

    public function iGradeSrd2s()
    {
        $success = false;
        $message = '';
        // var_dump([explode(',', $this->swh_igraded_srd2s)]);exit;
        if ($this->swh_igraded_srd2s) {
            $iGrade = GradeScale::getIGrade();
            $NGrade = GradeScale::getNGrade();
            if ($iGrade) {
                $uodate = Yii::$app->db->createCommand()
                    ->update(
                        'semester_registration_2_detail',
                        [
                            'srd2_grade_relId' => $iGrade->id,
                            'srd2_gradeApproved' => 1
                        ],
                        [
                            'id' => explode(',', $this->swh_igraded_srd2s),
                            'srd2_grade_relId' => $NGrade->id
                        ]
                    )
                    ->execute();
                if ($uodate) {
                    $message = "All non graded courses set to I-Grade.";
                    $success = true;
                } else {
                    $message = "Setting I grade to non-graded courses failed.".json_encode($uodate);
                    $success = false;
                }
            } else {
                $message = "I grade not found in the Grade Scale List";
                $success = false;
            }
        } else {
            $message = "No course to I grade.";
            $success = false;
        }

        return [$success, $message];
    }

    public function readmitScheduleCheck($semObj, $yearObj, $modelSWH)
    {
        if (is_object($semObj) && is_object($yearObj)) {
            $scheduledAcadYear = $this->readmitSchedule($modelSWH->id, 1);
            if ($scheduledAcadYear && is_array($scheduledAcadYear)) {
                // 0=>semester
                // 1=>Year 1st
                // 2=>Year 2nd

                // for MTech students, let it be flexible  :

                // update: student can request for readmission at any active academic semester.

                if (strtoupper($modelSWH->swhStdRel->stdProgRel->program_name) == 'MASTERS' || 1) {
                    return true;
                }
                if ($semObj->semester_name == $scheduledAcadYear[0] && ($yearObj->sch_year_desc == $scheduledAcadYear[1] || $yearObj->sch_year_desc == $scheduledAcadYear[2])) {
                    return true;
                }
                return "It is NOT time for readmission, Readmission Academic Year is Semester {$scheduledAcadYear[0]} of {$scheduledAcadYear[1]} OR {$scheduledAcadYear[2]}";
            }
        }
        return "Academic year / Semester error!";
    }
    public function hasValidWithdrawal($sdtObj)
    {
        return $this->find()
            ->where([
                'swh_approved' => 1,
                'swh_HOD_approved' => 1,
                'swh_stdSigned' => 1,
                'swh_lastApproved' => 1,
            ])
            ->andWhere(['swh_std_relId' => $sdtObj->id])
            ->andWhere(['swh_returnedBack' => NULL])
            ->one();
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'swh_acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'swh_sem_relId' => Yii::t('rms', 'Semester'),
            'swh_std_relId' => Yii::t('rms', 'Student Reg. ID'),
            'swh_reason_relId' => Yii::t('rms', 'Withdrawal Reason'),
            'swh_course_removed' => Yii::t('rms', 'Course Removed'),
            'swh_approved' => Yii::t('rms', 'Approved'),
            'swh_igraded_srd2s' => Yii::t('rms', 'I Graded Courses'),
            'swh_approved_by' => Yii::t('rms', 'Approved By'),
            'swh_approved_at' => Yii::t('rms', 'Approved At'),
            'swh_created_at' => Yii::t('rms', 'Created At'),
            'swh_created_by' => Yii::t('rms', 'Created By'),
            'swh_updated_at' => Yii::t('rms', ' Updated At'),
            'swh_updated_by' => Yii::t('rms', 'Updated By'),
            'swh_HOD_approved' => Yii::t('rms', 'HoD Approved'),
            'swh_HOD_approved_at' => Yii::t('rms', 'HoD Approved At'),
            'swh_HOD_approved_by' => Yii::t('rms', 'HoD Approved By'),
        ];
    }

    // get Notification for the Registrar.
    public function checkregistrarRequest()
    {
        if (Yii::$app->user->can('Registrar')) {
            return $this->find()
                ->where([
                    'swh_approved' => NULL,
                    'swh_returnedBack' => NULL,
                    'swh_HOD_approved' => 1,
                ])
                ->count();
        }
    }

    private function checkColumnName($col_name)
    {
        $sql = "SHOW COLUMNS FROM `w_student_withdrawal_history` LIKE 'swh_igraded_srd2s'";
        $result = Yii::$app->db->createCommand($sql)->queryAll();
        // var_dump(count($result) > 0);exit;
        if (count($result) > 0) {
            return;
        } else {
            $sql = "ALTER TABLE `w_student_withdrawal_history` ADD COLUMN `swh_igraded_srd2s` VARCHAR(45) NULL AFTER `section_relId`";
            $result = Yii::$app->db->createCommand($sql)->execute();
            $this->refresh();
            // $this->refreshTableSchema('course_catalog');
        }
    }

    public function checkHodRequest()
    {
        $this->checkColumnName('swh_igraded_srd2s');
        if (Yii::$app->user->can('DepartmentHead')) {
            $instdept = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id])->inst_dep_relId;
            return $this->find()
                ->innerJoin('student', 'student.std_dept_relId = w_student_withdrawal_history.swh_std_relId')
                ->where([
                    'swh_approved' => NULL,
                    'swh_returnedBack' => NULL,
                ])
                ->andWhere(['std_dept_relId' => $instdept])
                ->count();
        }
    }

    public static function getStudentlert()
    {

        $title = "Request Withdrawal";
        $link = "/request-withdrawal/index";
        $count = self::find()
            ->innerJoin('student', 'student.id=w_student_withdrawal_history.swh_std_relId')
            ->where([
                'swh_approved' => 1,
                'swh_returnedBack' => NULL,
                'swh_HOD_approved' => 1,
                'swh_stdSigned' => NULL,
            ])
            ->andWhere(['student.std_user_relId' => \Yii::$app->user->identity->id])
            ->count();

        $ex = \yii\helpers\ArrayHelper::keyExists($title, \Yii::$app->params['notifications'], false);
        if (!$ex || Yii::$app->params['notifications'][$title]['count'] != $count && $count > 0) {
            Yii::$app->params['notifications']['count'] += $ex ? ($count - Yii::$app->params['notifications'][$title]['count']) : $count;
            Yii::$app->params['notifications'][$title] = [
                'title' => $title,
                'count' => $count,
                'link' => $link
            ];
        }
    }

    public function getStudentlertCount()
    {
        if (Yii::$app->user->can('Student')) {
            $std = Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
            return $this->find()
                ->where([
                    'swh_approved' => 1,
                    'swh_returnedBack' => NULL,
                    'swh_HOD_approved' => 1,
                    'swh_stdSigned' => NULL,
                ])
                ->andWhere(['swh_std_relId' => $std->id])
                ->count();
        }
    }

    public function readmitSchedule($id, $forcheck = NULL)
    {
        $req = $this->findOne($id);
        $sem = $req->swhSemRel->semester_name;
        $year = $req->swhAcadYearRel->sch_year_desc;
        $ayy = 0;

        if ($req->swh_approved == 1 &&  $req->swh_HOD_approved == 1 && $req->swh_stdSigned == 1 && $req->swh_lastApproved == 1) {

            if ($req->swh_course_removed == 0) {
                $sem = $sem == 'I' ? 'II' : ($sem == 'II' ? ($req->swhStdRel->stdAdmissionRel->admission_name == 'Extension' ? 'Summer' : 'I') : ($sem == 'Summer' ? 'I' : 'Uknown'));
                if ($sem == 'I') {
                    // shift to next year,  - student may not get courses of the semester where he she completed, 
                    $ayy = 1;
                }
            }

            $year = $this->nextYear($req->swhAcadYearRel->sch_year_desc, $ayy);

            if (!is_null($forcheck)) {
                $year2 = $this->nextYear($year);
                return [$sem, $year, $year2];
            }
            return [$sem, $year];
        } else {
            return "Un-approved Withdrawal request.";
        }
    }

    private function nextYear($year, $adyear = 0)
    {
        $yA = explode('/', $year);
        return (string)($yA[0] + $adyear + 1) . '/' . ($yA[1] + $adyear + 1);
    }

    // get Notification for the HoD.

    public function getHoDNotification()
    {
        if (\Yii::$app->user->can('DepartmentHead')) {
            $instDept = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id])->inst_dep_relId;

            return $this->find()
                ->innerJoin('student', 'student.id = w_student_withdrawal_history.swh_std_relId')
                ->where([
                    'student.std_dept_relId' => $instDept,
                    'swh_HOD_approved' => NULL,
                ])
                ->one();
        }
        return NULL;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSwhAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'swh_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSwhApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'swh_approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSwhCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'swh_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSwhReasonRel()
    {
        return $this->hasOne(WithdrawalReason::class, ['id' => 'swh_reason_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSwhSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'swh_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSwhStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'swh_std_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSwhUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'swh_updated_by']);
    }
}
