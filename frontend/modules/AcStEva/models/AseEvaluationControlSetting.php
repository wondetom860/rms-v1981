<?php

namespace frontend\modules\AcStEva\models;

use Yii;
use backend\models\User;
use backend\models\Course;
use backend\models\FacultyCourseAssignment;
use backend\models\SemesterRegistration2Detail;

/**
 * This is the model class for table "ase_evaluation_control_setting".
 *
 * @property int $id
 * @property int $dept_plan_relId
 * @property int $candidate_staff_relId 
 * @property string $evaluators
 * @property float|null $total_percentage
 * @property int $finalized
 * @property int $evaluated_as
 * @property int $course_relId
 * @property int $faculty_assignment_relId
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $candidateStaffRel
 * @property AseDepartmentEvaluationSchedule $deptPlanRel
 * @property FacultyCourseAssignment $facultyCourseAssignmentRel
 * @property User $createdBy
 * @property User $updatedBy
 * @property AseEvaluationResult[] $aseEvaluationResults
 */
class AseEvaluationControlSetting extends \yii\db\ActiveRecord
{
    public $academic_year;
    public $course;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ase_evaluation_control_setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dept_plan_relId', 'candidate_staff_relId', 'created_by', 'created_at', 'data'], 'required'],
            [['dept_plan_relId', 'academic_year', 'candidate_staff_relId', 'finalized', 'created_by', 'created_at', 'updated_at', 'updated_by', 'course_relId', 'faculty_assignment_relId'], 'integer'],
            [['evaluators', 'evaluated_as'], 'safe'],
            [['total_percentage'], 'number'],
            [['data'], 'string'],
            [['candidate_staff_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['candidate_staff_relId' => 'id']],
            [['dept_plan_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AseDepartmentEvaluationSchedule::class, 'targetAttribute' => ['dept_plan_relId' => 'id']],
            [['faculty_assignment_relId'], 'exist', 'skipOnError' => true, 'targetClass' => FacultyCourseAssignment::class, 'targetAttribute' => ['faculty_assignment_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['course_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'academic_year' => Yii::t('rms', 'Academic Year'),
            'dept_plan_relId' => Yii::t('rms', 'Department'),
            'candidate_staff_relId' => Yii::t('rms', 'Staff to be evaluated'),
            'evaluators' => Yii::t('rms', 'Evaluators'),
            'total_percentage' => Yii::t('rms', 'Total Percentage'),
            'finalized' => Yii::t('rms', 'Finalized'),
            'course_relId' => Yii::t('rms', 'Course'),
            'faculty_assignment_relId' => Yii::t('rms', 'Course Assigned'),
            'evaluated_as' => Yii::t('rms', 'Evaluated As'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData()
    {
        $this->data = md5(serialize((object) [
            'evaluators' => $this->evaluators,
            'evaluated_as' => $this->evaluated_as,
            'finalized' => $this->finalized,
            'dept_plan_relId' => intval($this->dept_plan_relId),
            'candidate_staff_relId' => intval($this->candidate_staff_relId),
            'course_relId' => intval($this->course_relId),
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    public function getDoneCount()
    {
        $evaluationResults = $this->aseEvaluationResults;
        $completedStaffCount = 0;
        if ((count($evaluationResults) == 0)) {
            return 0;
        }

        foreach ($evaluationResults as $evalResult) {
            $completedStaffCount +=
                $evalResult->completed_staffs ?
                count(json_decode($evalResult->completed_staffs, true)) :
                0;
        }

        return $completedStaffCount;
    }

    public function aboveHalfEvaluated()
    {
        $evaluators = $this->evaluators ? count(json_decode($this->evaluators, true)) : 0;
        $completedStaffCount = 0;

        $evaluationResults = $this->aseEvaluationResults;

        if ((count($evaluationResults) == 0) || $evaluators == 0) {
            return [false, 0];
        }

        foreach ($evaluationResults as $evalResult) {
            $completedStaffCount +=
                $evalResult->completed_staffs ?
                count(json_decode($evalResult->completed_staffs, true)) :
                0;
        }

        if ($completedStaffCount == 0) {
            return [false, 0];
        }

        return [true, sprintf('%0.2f', ($completedStaffCount / $evaluators) * 100)];
    }

    public function checkIfExists($id = null)
    {
        if ($id) {
            return self::find()
                ->where([
                    'candidate_staff_relId' => $this->candidate_staff_relId,
                    'dept_plan_relId' => $this->dept_plan_relId,
                    'course_relId' => $this->course_relId,
                ])
                ->andWhere(" id <> $id ")
                ->one();
        }
        return self::find()
            ->where([
                'candidate_staff_relId' => $this->candidate_staff_relId,
                'dept_plan_relId' => $this->dept_plan_relId,
                'course_relId' => $this->course_relId,
            ])
            ->one();
    }

    public function getEvaluators($deptPlanId, $deptPlanModel, $facuId)
    { //$deptPlanId,$deptPlanModel,$facuId
        $bundle = $deptPlanModel->criteriaBundleRel;
        $academicYear = $deptPlanModel->headerRel->academicYearRel;
        $facuMod = FacultyCourseAssignment::findOne($facuId);
        if (strtoupper($bundle->evaluator_role) == 'STUDENT') {
            // code...
            $coff2details = $facuMod->instRel->getCourseOffering2Details();
            $coff2details = $coff2details
                ->innerJoin('course_offering2','course_offering2.id=course_offering2_detail.coff2det_coff2_relId')
                ->andWhere([
                    'coff2_acadYear_relId' => $academicYear->schoolYearRel->id,
                    'coff2_sem_relId' => $academicYear->semesterRel->id,
                ])
                ->all();
            $coff2detIds = [];
            foreach ($coff2details as $coff2det) {
                array_push($coff2detIds, $coff2det->id);
            }

            $srd22 = SemesterRegistration2Detail::find()
                ->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
                ->innerJoin('course_offering2', 'course_offering2.id=semester_registration_2_detail.srd2_coff_relId')
                ->innerJoin('course_offering2_detail', 'course_offering2.id=course_offering2_detail.coff2det_coff2_relId')
                ->innerJoin('student', 'student.id=semester_registration_2.semreg_std_relId')
                ->where([
                    'semreg_acadYear_relId' => $academicYear->schoolYearRel->id,
                    'semreg_semester_relId' => $academicYear->semesterRel->id,
                    'srd2_course_relId' => $facuMod->coff2detRel->coff2det_course_relId,
                    'course_offering2_detail.id' => $coff2detIds
                ])
                ->andWhere(['NOT IN', 'student.std_user_relId', [$facuMod->instRel->inst_user_relId]])
                ->all();
            // $srd2s = $facuMod->coff2detRel->coff2detCoff2Rel->getSemesterRegistration2Details($facuMod->coff2detRel->coff2det_course_relId)->all();
            return [$srd22, $facuMod, 2];
        } else if (strtoupper($bundle->evaluator_role) == 'ACADEMICDEAN') {
            // code...
            $adUser = User::find()
                ->innerJoin('auth_assignment', 'auth_assignment.user_id=user.id')
                ->where([
                    'auth_assignment.item_name' => $bundle->evaluator_role
                ])
                ->all();
            return [$adUser, $facuMod, 3];
        } else if (strtoupper($bundle->evaluator_role) == 'DEPARTMENTHEAD') {
            // fucMod->inst->dept->deptHead->inst->instUserId:
            $HoD = $facuMod->instRel->instDepRel->getDepartmentHeads()->andFilterWhere(['dh_status' => 1])->all();
            $hoDUsers = [];
            if ($HoD && count($HoD) > 0) {
                foreach ($HoD as $hod) {
                    $hoDUsers[] = $hod->dhInstRel->instUserRel;
                }
            }
            return [$hoDUsers, $facuMod, 3];
        }
        // inst_relId != from ifacuModel->id:
        // ->andWhere(" inst_relId <>  $facuMod->inst_relId ")
        $candidates = FacultyCourseAssignment::find()
            ->innerJoin('course_offering2_detail', 'course_offering2_detail.id=faculty_course_assignment.coff2det_relId')
            ->innerJoin('course_offering2', 'course_offering2.id=course_offering2_detail.coff2det_coff2_relId')
            ->innerJoin('instructor', 'instructor.id=faculty_course_assignment.inst_relId')
            ->innerJoin('auth_assignment', 'auth_assignment.user_id=instructor.inst_user_relId')
            ->where([
                'coff2_acadYear_relId' => $academicYear->schoolYearRel->id,
                'coff2_sem_relId' => $academicYear->semesterRel->id,
                'instructor.inst_active' => 1,
                'instructor.inst_dep_relId' => $deptPlanModel->dept_relId,
                'auth_assignment.item_name' => $bundle->evaluator_role
            ])->andWhere(" inst_relId <>  {$facuMod->inst_relId} ");
        if (!$bundle->evaluate_per_course) {
            $candidates = $candidates->groupBy('inst_relId');
        }
        $candidates = $candidates->orderBy(['inst_relId' => SORT_ASC, 'assign_as' => SORT_ASC, 'coff2det_course_relId' => SORT_ASC,])->all();

        return [$candidates, $facuMod, 1];
    }

    public function checkIfEvaluted($user)
    {
        $count = 0;
        $evaled = 0;
        foreach ($this->aseEvaluationResults as $evalresult) {
            if (in_array($user->id, json_decode($evalresult->completed_staffs, true))) {
                $evaled++;
            }
            $count++;
        }
        return [$evaled, $count];
    }

    public function alreadyEvaluated($uid, $candidateId)
    {
        // 'candidate_staff_relId' => $candidateId,
        $evalResults = $this->getAseEvaluationResults()
            ->innerJoinWith('controlSettingRel')
            ->andWhere([
                'ase_evaluation_result.dept_plan_relId' => $this->dept_plan_relId,
                'course_relId' => $this->course_relId,
            ])->all();
        $evaluated = false;
        if ($evalResults) {
            foreach ($evalResults as $evalResult) {
                if (in_array($uid, json_decode($evalResult->completed_staffs))) {
                    $evaluated = true;
                }
            }
        }
        return $evaluated;
    }

    /**
     * Summary of generateControlEntries
     * @param mixed $deptPlanId
     * @param mixed $deptPlanModel
     * @return array|null
     */
    public function generateControlEntries($deptPlanId, $deptPlanModel)
    {
        $bundle = $deptPlanModel->criteriaBundleRel;
        $header = $deptPlanModel->headerRel;
        $academicYear = $header->academicYearRel;

        $candidateStaffRole = $bundle->candidate_staff_role;
        $evalautors_role = $bundle->evaluator_role;

        // if schedule is closed or status id finalized, we don't allow any modification
        // trigger schedule status update:
        $deptPlanModel->getStatus();
        if ($deptPlanModel->status == $deptPlanModel::STATUS_CLOSED) {
            return NULL;
        }

        $alreadySettedCandidates = [];
        // foreach ($deptPlanModel->aseEvaluationControlSettings as $setting) {
        //     $alreadySettedCandidates[] = $setting->candidate_staff_relId;
        // }
        $roles = [$bundle->candidate_staff_role];
        if (strtoupper($bundle->candidate_staff_role) == 'INSTRUCTOR' && strtoupper($bundle->evaluator_role) != 'DEPARTMENTHEAD') {
            $roles = ['INSTRUCTOR', 'DEPARTMENTHEAD'];
        }
        $candidates = FacultyCourseAssignment::find()
            ->innerJoin('course_offering2_detail', 'course_offering2_detail.id=faculty_course_assignment.coff2det_relId')
            ->innerJoin('course_offering2', 'course_offering2.id=course_offering2_detail.coff2det_coff2_relId')
            ->innerJoin('instructor', 'instructor.id=faculty_course_assignment.inst_relId')
            ->innerJoin('auth_assignment', 'auth_assignment.user_id=instructor.inst_user_relId')
            ->where([
                'coff2_acadYear_relId' => $academicYear->schoolYearRel->id,
                'coff2_sem_relId' => $academicYear->semesterRel->id,
                'instructor.inst_active' => 1,
                'instructor.inst_dep_relId' => $deptPlanModel->dept_relId,
                'auth_assignment.item_name' => $roles
            ]);
        if ($alreadySettedCandidates && count($alreadySettedCandidates) > 0) {
            // $candidates = $candidates->andWhere(['NOT IN','inst_user_relId',$alreadySettedCandidates]);
        }

        // var_dump($bundle->evaluate_per_course);die();

        if (!$bundle->evaluate_per_course) {
            $candidates = $candidates->groupBy('inst_relId');
        }
        $candidates = $candidates->orderBy(['inst_relId' => SORT_ASC, 'assign_as' => SORT_ASC, 'coff2det_course_relId' => SORT_ASC,])->all();
        if ($candidates) {
            foreach ($candidates as $candidate) {
                // echo "<p>Creating for : {$candidate->instRel->getFullName()} ....</p>";
                $modelControl = new AseEvaluationControlSetting();
                $modelControl->dept_plan_relId = $deptPlanId;
                $modelControl->candidate_staff_relId = $candidate->instRel->inst_user_relId;
                $modelControl->evaluators = $this->populateEvaluators($deptPlanId, $deptPlanModel, $candidate->id);
                $modelControl->faculty_assignment_relId = $candidate->id;
                $modelControl->evaluated_as = $candidate->assign_as;
                $modelControl->course_relId = $bundle->evaluate_per_course ? $candidate->coff2detRel->coff2det_course_relId : NULL;

                if (($ex = $modelControl->checkIfExists()) != NULL) {
                    // echo "<p>Already created for : {$candidate->instRel->getFullName()} ....</p>";
                    if ($ex->evaluators != $modelControl->evaluators) {
                        $ex->evaluators = $modelControl->evaluators;
                        if (!$ex->save()) {
                            var_dump($ex->getErrors());
                        }
                    }
                    continue;
                } else {
                    $modelControl->created_by = Yii::$app->user->id;
                    $modelControl->created_at = time();
                    $modelControl->getData();
                    if (!$modelControl->save()) {
                        var_dump($modelControl->getErrors());
                    }
                }
            }
        }
        return $deptPlanModel->aseEvaluationControlSettings;
    }
    private function populateEvaluators($deptPlanId, $deptPlanModel, $facuId)
    {
        $evalurors = [];
        $evas = $this->getEvaluators($deptPlanId, $deptPlanModel, $facuId);
        $evass = $evas[0];
        if ($evas[2] == 1) {
            if ($evass && count($evass) > 0) {
                foreach ($evass as $eva) {
                    $evalurors[] = $eva->instRel->inst_user_relId;
                }
            }
        } elseif ($evas[2] == 2) {
            foreach ($evass as $eva) {
                $evalurors[] = $eva->srd2SemregRel->semregStdRel->std_user_relId;
            }
        } else if ($evas[2] == 3) {
            foreach ($evass as $eva) {
                $evalurors[] = $eva->id;
            }
        }

        return json_encode($evalurors);
    }

    public function calculateTotalAverage()
    {
        $total = 0;
        $count = 0;
        foreach ($this->aseEvaluationResults as $result) {
            if ($result->average_total == 0) {
                $result->delete();
                Yii::$app->session->setFlash('error', 'Evaluation result with zero average total has been deleted.');
                Yii::info("Evaluation result with zero average total has been deleted.");
                continue;
            }
            $result->computeAverageLast();
            if ($this->aboveHalfEvaluated()[0] && $result->average_total) {
                $result->average_percentage = ($result->average_total * ($result->criteriaBundleRel->average_percentage / $result->criteriaBundleRel->average_point));
                $result->update(false);
            } else {
                return;
            }
            if ($result->average_total) {
                $total += $result->average_total;
                $count++;
            }
        }
        if ($count > 0) {
            $this->total_percentage = ($total);
            $this->update(false);
            $this->refresh();
            return sprintf('%0.2f', ($total));
        }
        return 0;
    }
    /**
     * Gets query for [[CandidateStaffRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCandidateStaffRel()
    {
        return $this->hasOne(User::class, ['id' => 'candidate_staff_relId']);
    }

    /**
     * Gets query for [[DeptPlanRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeptPlanRel()
    {
        return $this->hasOne(AseDepartmentEvaluationSchedule::class, ['id' => 'dept_plan_relId']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'course_relId']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[AseEvaluationResults]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAseEvaluationResults()
    {
        return $this->hasMany(AseEvaluationResult::class, ['control_setting_relId' => 'id']);
    }

    /**
     * Gets query for [[facultyCourseAssignmentRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFacultyCourseAssignmentRel()
    {
        return $this->hasOne(FacultyCourseAssignment::class, ['id' => 'faculty_assignment_relId']);
    }

    /**
     * Gets query for [[AseEvaluationResults]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFacultyCourseAssignments()
    {
        return $this->hasMany(FacultyCourseAssignment::class, ['faculty_assignment_relId' => 'id']);
    }
}
