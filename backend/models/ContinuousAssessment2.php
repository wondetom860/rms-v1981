<?php

namespace backend\models;

use Yii;
use \common\components\CommonControllerFunctions as CCF;

/**
 * This is the model class for table "continuous_assessment2".
 *
 * @property integer $id
 * @property integer $ca_std_relid
 * @property integer $ca_semreg_relId
 * @property integer $ca_markAssignment_relId
 * @property integer $ca_catagorySetup_relId
 * @property double $ca_markObtained
 * @property integer $ca_coff_relId
 * @property integer $ca_created_by
 * @property string $ca_created_at
 * @property integer $ca_updated_by
 * @property string $ca_updated_at
 *
 * @property ContAssessCatagory2 $caCatagorySetupRel
 * @property CourseOffering2Detail $caCoffRel
 * @property InstanceMarkAssignment $caMarkAssignmentRel
 * @property SemesterRegistration2 $caSemregRel
 * @property Student $caStdRel
 * @property User $caCreatedBy
 * @property User $caUpdatedBy
 */
class ContinuousAssessment2 extends \yii\db\ActiveRecord
{
    public $acadYearId;
    public $semId;
    public $transaction = NULL;

    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'continuous_assessment2';
    }

    public function getContAssessUniqueId($courseId, $stdId, $catagoryId, $imaId)
    {
        return $this->find()
            ->where([
                'ca_catagorySetup_relId' => $catagoryId,
                'ca_std_relid' => $stdId,
                'ca_markAssignment_relId' => $imaId
            ])
            ->innerJoin('cont_assess_catagory2', 'cont_assess_catagory2.id = continuous_assessment2.ca_catagorySetup_relId')
            ->andWhere(['ca_markObtained' => NULL])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ca_std_relid', 'ca_semreg_relId', 'ca_markAssignment_relId', 'ca_catagorySetup_relId', 'ca_coff_relId', 'ca_created_by', 'ca_created_at'], 'required'],
            [['ca_std_relid', 'ca_semreg_relId', 'ca_markAssignment_relId', 'ca_catagorySetup_relId', 'ca_coff_relId', 'ca_created_by', 'ca_updated_by'], 'integer'],
            [['ca_markObtained'], 'number'],
            [['ca_created_at', 'ca_updated_at'], 'safe'],
            [['ca_catagorySetup_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ContAssessCatagory2::class, 'targetAttribute' => ['ca_catagorySetup_relId' => 'id']],
            [['ca_coff_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2Detail::class, 'targetAttribute' => ['ca_coff_relId' => 'id']],
            [['ca_markAssignment_relId'], 'exist', 'skipOnError' => true, 'targetClass' => InstanceMarkAssignment::class, 'targetAttribute' => ['ca_markAssignment_relId' => 'id']],
            [['ca_semreg_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \frontend\models\SemesterRegistration2::class, 'targetAttribute' => ['ca_semreg_relId' => 'id']],
            [['ca_std_relid'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['ca_std_relid' => 'id']],
            [['ca_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ca_created_by' => 'id']],
            [['ca_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ca_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ca_std_relid' => Yii::t('rms', 'Student ID'),
            'ca_semreg_relId' => Yii::t('rms', 'Semreg. ID'),
            'ca_markAssignment_relId' => Yii::t('rms', 'Instance Name'),
            'ca_catagorySetup_relId' => Yii::t('rms', 'Catagory Name'),
            'ca_markObtained' => Yii::t('rms', 'Mark Obtained'),
            'ca_coff_relId' => Yii::t('rms', 'Coff  ID'),
            'ca_created_by' => Yii::t('rms', 'Created By'),
            'ca_created_at' => Yii::t('rms', 'Created At'),
            'ca_updated_by' => Yii::t('rms', 'Updated By'),
            'ca_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    // $std->id,$courseId,$srd->srd2_semreg_relId,$coffdetId

    public static function getSum($stdId, $courseId, $semregId, $coffDetId = null)
    {
        // $coffId = CourseOffering2Detail::findOne($coffDetId);
        $srg = SemesterRegistration2::findOne($semregId);
        $sum = 0;
        $catagotyModel = new ContAssessCatagory2();
        $instanceModel = new InstanceMarkAssignment();
        $courseCatagory = $catagotyModel->getCourseCatagory($courseId, null, $srg);
        if ($courseCatagory && count($courseCatagory) > 0) {
            foreach ($courseCatagory as $keyCat) {
                $instEntry = $instanceModel->getCourseAssessInstances($coffDetId, $keyCat->id);
                if ($instEntry) {
                    foreach ($instEntry as $keyInst) {
                        $cast = ContinuousAssessment2::find()
                            ->where([
                                'ca_markAssignment_relId' => $keyInst->id,
                                'ca_std_relid' => $stdId,
                            ])
                            ->andWhere(['IS NOT', 'ca_markObtained', NULL])
                            ->one();

                        $sum += $cast ? $cast->ca_markObtained : 0;
                    }
                }
            }
            return $sum;
        }
        return '-';
    }

    public function deleteContAsssEntry($semregId)
    {
        $ca = $this->find()->where(['ca_semreg_relId' => $semregId])->all();
        if ($ca && count($ca) > 0) {
            foreach ($ca as $CAkey) {
                $CAkey->delete();
            }
        }
    }

    public function calculateTotal($contAssessment, $courseId = NULL)
    {
        // print_r($contAssessment);die();
        if ($contAssessment && count($contAssessment) > 0) {
            $totalObtained = 0;
            $totalAssigned = 0;
            $jumpedAssessment = false;

            foreach ($contAssessment as $key) {

                if (is_null($key->ca_markObtained)) {
                    $jumpedAssessment = true;
                } else {
                    $totalObtained += $key->ca_markObtained;
                }
                // $maxPoint = InstanceMarkAssignment::findOne(['id'=>$key->ca_markAssignment_relId])->ima_possiblePoint;
                // $maxPoint = $key->caMarkAssignmentRel->ima_possiblePoint;
                $totalAssigned += $key->caMarkAssignmentRel->ima_possiblePoint;
            }

            // return $totalAssigned;
            // var_dump($totalAssigned,$totalObtained);die();
            $totalAssigned = $totalAssigned > 100 ? 100 : $totalAssigned;
            if ($totalAssigned >= 100) {
                $srd2 = \backend\models\SemesterRegistration2Detail::findOne(['srd2_semreg_relId' => $contAssessment[0]['ca_semreg_relId'], 'srd2_course_relId' => $courseId]);
                // echo "1";die();
                return $this->setLetterGrade2($totalAssigned, $totalObtained, $contAssessment[0]['ca_std_relid'], $contAssessment[0]['ca_coff_relId'], $srd2->id, $jumpedAssessment);
            } else {
                // die("Total Is Less Than 100");
                return NULL;
            }
        } else {
            // die("ContAssessCatagory Object empty.");
            return NULL;
        }
    }

    // public function ge


    public function setLetterGrade2($totalAssigned, $totalObtained, $stdId, $coffDetId, $srd2, $jumpedAssessment)
    {
        if ($totalAssigned >= 100 && !is_null($totalObtained)) {
            // read interval which contains the mark point obtained by the student.
            // gin first check that the the student fully examined by the assessment instances.
            $ltrGrade = NULL;
            $srdObj = \backend\models\SemesterRegistration2Detail::findOne($srd2);
            // var_dump($totalAssigned, $totalObtained, $stdId, $coffDetId,$srd2,$jumpedAssessment);die();
            $totalObtained = $totalObtained >= 100 ? 100 : $totalObtained;
            if ($jumpedAssessment && $totalAssigned >= 100) { //
                $ltrGrade = GradeScale::find()
                    ->where(['gs_ltr_grade' => 'NG'])
                    ->one();
                // var_dump($ltrGrade);die();
            } else {
                // var_dump($totalObtained);
                // $totalObtained = $totalObtained >= 100 ? 100 : $totalObtained;
                $ltrGrade = GradeScale::find()
                    ->andWhere(':mark between gs_min AND gs_max', [':mark' => $totalObtained])
                    ->andWhere(['gs_forProgram' => $srdObj->srd2GroupRel->gs_prog_relId])
                    ->andWhere(['gs_pass_status' => 1])
                    ->andWhere(['OR', ['<>', 'gs_gradePoint', 0], ['gs_ltr_grade' => 'F']])
                    ->one();
            }

            // $coffdet = CourseOffering2Detail::findOne(['id'=>$coffDetId]);
            if ($ltrGrade) {
                // valid lGrade scale is found.
                // update semreg detail.
                $exists = \backend\models\SemesterRegistration2Detail::findOne(['srd2_gradeApproved' => 0, 'id' => $srd2]);
                if (Yii::$app->user->can('DepartmentHead')) {
                    $exists = \backend\models\SemesterRegistration2Detail::find()
                        ->where(['id' => $srd2])
                        ->one();
                }
                if ($exists) {
                    $result = Yii::$app->db->createCommand()
                        ->update(
                            'semester_registration_2_detail',
                            [
                                'srd2_grade_relId' => $ltrGrade->id,
                                'srd2_total_mark' => $totalObtained,
                                'srd2_letter_grade' => $ltrGrade->gs_ltr_grade,
                                'srd2_grade_point' => $ltrGrade->gs_gradePoint,
                            ],
                            ['id' => $srd2]
                        )
                        ->execute();
                    // exit;
                    $exists->srd2SemregRel->upgradeSrd2();
                    $gset = WGroupSetup::findOne($exists->srd2_group_relId);
                    CCF::getStudentSemesterStatusP($gset->id, $gset->gs_clslvl_relId, $gset->gs_sem_relId, $stdId, $exists);
                    // $this->transaction?$this->transaction->commit():NULL;
                    return $ltrGrade;
                } else {
                    // $this->transaction?$this->transaction->commit():NULL;
                    return $ltrGrade;
                }
            } else {
                return "Grade Interval NOT found.. Total Obtained={$totalObtained}";
                // return NULL;
            }
        }
    }


    public function setLetterGrade($totalAssigned, $totalObtained, $stdId, $coffDetId, $srd2, $jumpedAssessment, $markedSome = null)
    {
        if ($totalAssigned >= 100 && !is_null($totalObtained)) {
            // var_dump($markedSome)
            // read interval which contains the mark point obtained by the student.
            // gin first check that the the student fully examined by the assessment instances.
            // $ltrGrade = 'N/G';
            $totalObtained = $totalObtained >= 100 ? 100 : $totalObtained;
            $srdObj = \backend\models\SemesterRegistration2Detail::findOne($srd2);
            if ($srdObj->srd2CourseRel->course_crdthr == 0) {
                return NULL;
            }
            if ($jumpedAssessment && $totalAssigned >= 100) { //
                // total assigned >= 100 indicates the assessment is completed.
                if ($markedSome) {
                    // for this case we need I grade.
                    $ltrGrade = GradeScale::find()->where(['gs_ltr_grade' => 'I'])->one();
                } else {
                    $ltrGrade = GradeScale::find()->where(['gs_ltr_grade' => 'NG'])->one();
                }
                // var_dump($ltrGrade);die();
            } else {
                // var_dump($totalObtained);
                $ltrGrade = GradeScale::find()
                    ->andWhere(':mark between gs_min AND gs_max', [':mark' => $totalObtained])
                    ->andWhere(['gs_forProgram' => $srdObj->srd2GroupRel->gs_prog_relId])
                    ->andWhere(['gs_pass_status' => 1])
                    ->andWhere(['gs_scale_status' => 1])
                    ->andWhere(['OR', ['<>', 'gs_gradePoint', 0], ['gs_ltr_grade' => 'F']])
                    ->one();
            }

            // var_dump($srd2);die();

            // $coffdet = CourseOffering2Detail::findOne(['id'=>$coffDetId]);
            if ($ltrGrade) {
                // valid lGrade scale is found.
                // update semreg detail.

                // ->andWhere(['OR',['srd2_gradeApproved'=>NULL],['srd2_gradeApproved'=>0]])
                $exists = \backend\models\SemesterRegistration2Detail::findOne($srd2);
                if ($exists) {

                    // course grade of repeated registration will be as it is. 
                    $cGrade = NULL;
                    // $cGrade = $jumpedAssessment?NULL:( (!is_null($ltrGrade) && $ltrGrade->gs_ltr_grade == 'D') ?$ltrGrade:$this->checkIfAddedd($exists));

                    $ltrGrade = ($cGrade && count($cGrade) > 0) ? $cGrade : $ltrGrade;
                    if ($exists->srd2_grade_relId != $ltrGrade->id) {
                        if (($exists->srd2_gradeApproved == 1) || ($exists->srd2_gradeLocked == 1)) {
                            // Updating grade change failed due to previous grade had being approved by the HoD. Please contact HoD if necessary
                            return "Grade already approved.";
                        } else {
                            $exists->srd2_grade_relId = $ltrGrade->id;
                            if ($exists->update()) {
                                $exists->srd2SemregRel->upgradeSrd2();
                                $gset = WGroupSetup::findOne($exists->srd2_group_relId);
                                $rr = !$jumpedAssessment ? CCF::getStudentSemesterStatusP($gset->id, $gset->gs_clslvl_relId, $gset->gs_sem_relId, $stdId, $exists) : NULL;
                                return $exists->srd2GradeRel;
                            } else {
                                return json_encode($exists->getErrors());
                            }
                        }
                    }
                    return "Not Changed";
                } else {
                    return 'Invalid Semester Registration entry.';
                }
            } else {
                // die("Grade Interval NOT found..");
                return 'grade scale intervla error';
            }
        }
    }

    private function checkIfAddedd($srd2)
    {
        if ($srd2 && count($srd2) > 0) {
            if (!is_null($srd2->srd2GradeRel) && $srd2->srd2GradeRel->gs_ltr_grade == 'F') {
                return null;
            }
            if (strtoupper($srd2->srd2GroupRel->gsProgRel->program_name) == 'MASTERS') {
                return NULL;
            }
            $srd2Detail = \backend\models\SemesterRegistration2Detail::findOne($srd2->id);
            $semreg = \frontend\models\SemesterRegistration2::findOne($srd2Detail->srd2_semreg_relId);

            if ($srd2Detail) {
                // $fgrades = \backend\models\GradeScale::find()->where([])->all();
                $exists = \backend\models\SemesterRegistration2Detail::find()
                    ->where(['srd2_course_relId' => $srd2Detail->srd2_course_relId])
                    ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                    ->innerJoin('grade_scale', 'grade_scale.id = semester_registration_2_detail.srd2_grade_relId')
                    ->andWhere(['semreg_std_relId' => $semreg->semreg_std_relId])
                    ->andWhere(['gs_ltr_grade' => 'F'])
                    ->andWhere(['<>', 'semester_registration_2_detail.id', $srd2->id])
                    ->all();
                // ,'D'
                if ($exists) {
                    return \backend\models\GradeScale::findOne(['gs_ltr_grade' => 'C']);
                }
            }
        }
        return null;
    }

    public function updateFinalExamMark($contAssObj, $afterMark, $courseId = NULL)
    {
        $this->transaction = Yii::$app->db->beginTransaction();
        if ($contAssObj && count($contAssObj) > 0) {
            $upResult = Yii::$app->db->createCommand()
                ->update(
                    'continuous_assessment2',
                    [
                        'ca_markObtained' => $afterMark,
                        'ca_updated_by' => Yii::$app->user->identity->id,
                        'ca_updated_at' => date("Y-m-d h:i:s"),
                    ],
                    ['id' => $contAssObj->id]
                )
                ->execute();
            if ($upResult) {
                // update ima as assessed.
                $update4 = Yii::$app->db->createCommand()
                    ->update(
                        'instance_mark_assignment',
                        ['ima_assessed' => 1],
                        ['id' => $contAssObj->ca_markAssignment_relId]
                    )
                    ->execute();
                if ($update4 || 1) {
                    $this->transaction ? $this->transaction->commit() : NULL;
                    $contAssessment =  $this->find()
                        ->innerJoin('cont_assess_catagory2', 'cont_assess_catagory2.id = continuous_assessment2.ca_catagorySetup_relId')
                        ->where([
                            'ca_semreg_relId' => $contAssObj->ca_semreg_relId,
                            'ca_std_relid' => $contAssObj->ca_std_relid,
                            'ca_coff_relId' => $contAssObj->ca_coff_relId
                        ])
                        ->groupBy('ca_markAssignment_relId')
                        ->andWhere(['cat_course_relId' => $courseId])
                        ->all();
                    // return $contAssessment;
                    return $this->calculateTotal($contAssessment, $courseId);
                }
            } else {
                return "Setting obtained mark error.";
                // return NULL;
            }
        } else {
            return "ContinuousAssessment Object Empty.";
            // return NULL;
        }
    }

    // get student semesetr registration record.
    public function getCaSemregRel2($stdId, $courseId, $coff2DetObj = null)
    {
        if (!is_null($coff2DetObj)) {
            $this->acadYearId = $coff2DetObj->coff2detCoff2Rel->coff2_acadYear_relId;
            $this->semId = $coff2DetObj->coff2detCoff2Rel->coff2_sem_relId;
        } else {
            $this->__constructor();
        }

        return \frontend\models\SemesterRegistration2::find()
            ->innerJoin('semester_registration_2_detail', 'semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
            ->where(['semreg_acadYear_relId' => $this->acadYearId, 'semreg_semester_relId' => $this->semId, 'semreg_std_relId' => $stdId])
            ->andWhere(['srd2_course_relId' => $courseId])
            ->one();
    }

    // find all assessments' instances made for this course
    public function getAssessmentInstances($courseId, $coffDetId)
    {
        return $this->find()->where(['ca_coff_relId' => $coffDetId])->distinct('ca_markAssignment_relId')->all();
    }

    // Get student''s mark obtained for the assessment instance.
    public function getStudentMark($courseId, $studentId, $catId, $assignmentId)
    {
        return $this->find()
            ->where(['ca_catagorySetup_relId' => $catId, 'ca_std_relid' => $studentId, 'ca_markAssignment_relId' => $assignmentId])
            ->andWhere(['IS NOT', 'ca_markObtained', NULL])
            ->one();
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaCatagorySetupRel()
    {
        return $this->hasOne(ContAssessCatagory2::class, ['id' => 'ca_catagorySetup_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaCoffRel()
    {
        return $this->hasOne(CourseOffering2Detail::class, ['id' => 'ca_coff_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaMarkAssignmentRel()
    {
        return $this->hasOne(InstanceMarkAssignment::class, ['id' => 'ca_markAssignment_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaSemregRel()
    {
        return $this->hasOne(SemesterRegistration2::class, ['id' => 'ca_semreg_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'ca_std_relid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ca_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ca_updated_by']);
    }
}
