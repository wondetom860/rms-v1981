<?php

namespace backend\models;

use common\components\CommonControllerFunctions as CCF;


use Yii;

/**
 * This is the model class for table "add_history".
 *
 * @property integer $id
 * @property integer $ah_srd2_relId
 * @property integer $ah_std_relId
 * @property integer $ah_course_relId
 * @property integer $ah_acadYear_relId
 * @property integer $ah_sem_relId
 * @property integer $ah_coff_relId
 * @property integer $ah_addWithGroup_relId
 * @property integer $ah_approved
 * @property integer $ah_approved_by
 * @property integer $ah_approved_at
 * @property string $ah_created_at
 * @property integer $ah_created_by
 * @property integer $ah_updated_by
 * @property string $ah_updated_at
 *
 * @property SchoolYear $ahAcadYearRel
 * @property WGroupSetup $ahAddWithGroupRel
 * @property User $ahApprovedBy
 * @property Course $ahCourseRel
 * @property User $ahCreatedBy
 * @property Semester $ahSemRel
 * @property SemesterRegistration2Detail $ahSrd2Rel
 * @property Student $ahStdRel
 * @property User $ahUpdatedBy
 */
class AddHistory extends \yii\db\ActiveRecord
{
    public $addWDept = NULL;
    public $addWClslvl = NULL;
    public $possible = true;
    public $message = "";

    public $stdId, $first_name, $courseTitle, $maxCredit, $minCredit, $collectedCredit, $pendingCredit, $freeCreditHrs;
    public $acadYearId;
    public $semId;
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
        return 'add_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ah_std_relId', 'ah_course_relId', 'ah_acadYear_relId', 'ah_sem_relId', 'ah_addWithGroup_relId', 'ah_created_at', 'ah_created_by', 'ah_updated_by', 'ah_updated_at'], 'required'],
            [['ah_srd2_relId', 'ah_std_relId', 'ah_course_relId', 'ah_acadYear_relId', 'ah_sem_relId', 'ah_addWithGroup_relId', 'ah_approved', 'ah_created_by', 'ah_coff_relId',], 'integer'],
            [['ah_created_at', 'ah_updated_at'], 'safe'],
            [['ah_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['ah_acadYear_relId' => 'id']],
            [['ah_coff_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2Detail::class, 'targetAttribute' => ['ah_coff_relId' => 'id']],
            [['ah_addWithGroup_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['ah_addWithGroup_relId' => 'id']],
            [['ah_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ah_approved_by' => 'id']],
            [['ah_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['ah_course_relId' => 'id']],
            [['ah_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ah_created_by' => 'id']],
            [['ah_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['ah_sem_relId' => 'id']],
            [['ah_srd2_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \frontend\models\SemesterRegistration2Detail::class, 'targetAttribute' => ['ah_srd2_relId' => 'id']],
            [['ah_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['ah_std_relId' => 'id']],
            [['ah_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ah_updated_by' => 'id']],
        ];
    }

    public static function getStudentlert()
    {

        $title = "Course Add Request";
        $link = "/add-history/index";
        $count = self::find()->innerJoin('student', 'student.id=add_history.ah_std_relId')
            ->andWhere(['ah_approved_by' => NULL, 'student.std_user_relId' => \Yii::$app->user->identity->id])->count();

        $ex = \yii\helpers\ArrayHelper::keyExists($title, \Yii::$app->params['notifications'], false);
        if (!$ex || Yii::$app->params['notifications'][$title]['count'] != $count) {
            Yii::$app->params['notifications']['count'] += $ex ? ($count - Yii::$app->params['notifications'][$title]['count']) : $count;
            Yii::$app->params['notifications'][$title] = [
                'title' => $title,
                'count' => $count,
                'link' => $link
            ];
        }
    }

    public static function getAlertNotification()
    {

        $title = "Course Add Request";
        $link = "/add-history/index";
        $count = self::find()->andWhere(['ah_approved_by' => NULL])->count();

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

    public function addPossible($studentObj = null)
    {
        $studentObj = $studentObj ? $studentObj : Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);

        if ($studentObj) {
            $this->__constructor();

            $gset = $this->currentStudentGroup($studentObj);
            $gset = $gset ? $gset : $studentObj->stdGroupReal; //if not assigned to any group in this semester, take last group in which the student is assigned.

            $coursesEnrolled = CCF::getStudentGroupCoursesP($gset->id, $studentObj->id, $gset->gsActiveProgRel->semester_relId, $gset->gs_clslvl_relId, $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id);
            $creditsCollected = 0;
            $addCrdtSum = 0;
            if ($coursesEnrolled && count($coursesEnrolled) > 0) {
                // Sum up the credits collected in this semsetr.
                foreach ($coursesEnrolled as $key) {
                    $creditsCollected += $key->srd2CourseRel->course_crdthr;
                }
            } else {
                $creditsCollected = 0;
            }

            $maxAllowedCedit = 0; //default maxCredit for semester registration is 19.

            $maxAllowedObject = CCF::getMaxAllowedCreditHrPerSemester($gset);

            if ($maxAllowedObject) {
                $maxAllowedCedit = max($maxAllowedObject->src_max_crdthr, $maxAllowedObject->src_min_crdthr);
                $this->minCredit = min($maxAllowedObject->src_max_crdthr, $maxAllowedObject->src_min_crdthr);;
            }

            $this->pendingCredit = 0;

            $approvalPendingRequests = self::find()->where(['ah_std_relId' => $studentObj->id, 'ah_approved' => NULL])->all();
            if ($approvalPendingRequests && count($approvalPendingRequests) > 0) {
                foreach ($approvalPendingRequests as $keyAh) {
                    $this->pendingCredit += $keyAh->ahCourseRel->course_crdthr;
                }
            }

            $this->maxCredit = $maxAllowedCedit;
            $this->collectedCredit = $creditsCollected;

            $this->freeCreditHrs = $this->maxCredit - ($this->collectedCredit + $this->pendingCredit);

            if ($this->maxCredit < ($this->collectedCredit + $this->pendingCredit)) {
                $this->message = "No more free Credit hour.";
                $this->possible = false;
            } else {
                if (\Yii::$app->user->can('Student')) {
                    $this->possible = $this->ScheduleOpen($gset);
                } else {
                    return true;
                }
            }
        } else {
            $this->message = "Information will be available for student only";
            return false;
        }
    }

    private function ScheduleOpen($gset)
    {
        $actionName = 'Course add';
        $action = AcademicCalanderAction::findOne(['aca_name' => $actionName]);
        if ($action) {
            $schedule = CCF::getScheduleForAction($gset, $action->id);
            if (!($schedule && count($schedule))) {
                $this->message = "Schedule is not open for course adding";
                return false;
            } else {
                $this->message = "Schedule is open";
                return true;
            }
        } else {
            $this->message = "Unregistered action {$actionName}";
            return false;
        }
    }

    protected function currentStudentGroup($stdObject)
    {
        return CCF::currentStudentGroup($stdObject);

        $this->__constructor();
        $gset = \backend\models\WGroupSetup::find()
            ->where([
                'gs_acadYear_relId' => $this->acadYearId,
                'gs_sem_relId' => $this->semId,
                'gs_adm_relId' => $stdObject->std_admission_relId,
                'gs_prog_relId' => $stdObject->std_prog_relId,
            ])
            ->innerJoin('w_group_assignment', 'w_group_setup.id = w_group_assignment.gas_gset_relId')
            ->andWhere(['w_group_assignment.gas_std_relId' => $stdObject->id])
            ->with('wGroupAssignments')
            ->one();
        return $gset;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ah_srd2_relId' => Yii::t('rms', 'Sem. Registration Id'),
            'ah_std_relId' => Yii::t('rms', 'Student Name'),
            'ah_course_relId' => Yii::t('rms', 'Course'),
            'stdId' => Yii::t('rms', 'Reg. ID'),
            'ah_coff_relId' => Yii::t('rms', 'Course Instructor'),
            'first_name' => Yii::t('rms', 'Student Name'),
            'ah_acadYear_relId' => Yii::t('rms', 'Acad. Year'),
            'ah_sem_relId' => Yii::t('rms', 'Semester'),
            'ah_addWithGroup_relId' => Yii::t('rms', 'Add With Group'),
            'ah_approved' => Yii::t('rms', 'Approved'),
            'ah_approved_by' => Yii::t('rms', 'Approved By'),
            'ah_approved_at' => Yii::t('rms', 'Approved At'),
            'ah_created_at' => Yii::t('rms', 'Created At'),
            'ah_created_by' => Yii::t('rms', 'Created By'),
            'ah_updated_by' => Yii::t('rms', 'Updated By'),
            'ah_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    public function getCourseInstNotification()
    {
        if (Yii::$app->user->can('Instructor') || Yii::$app->user->can('DepartmentHead')) {
            $instdept = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
            // var_dump($instdept);die();
            return $instdept ? $this->find()
                ->innerJoin('course_offering2_detail', 'add_history.ah_coff_relId = course_offering2_detail.id')
                ->andWhere(['course_offering2_detail.coff2det_inst_relId' => $instdept->id])
                ->andWhere(['ah_approved' => NULL])
                ->count() : $instdept;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAhAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'ah_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAhAddWithGroupRel() //ahAddWithGroupRel
    {
        return $this->hasOne(WGroupSetup::class, ['id' => 'ah_addWithGroup_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAhApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ah_approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAhCoffRel()
    {
        return $this->hasOne(CourseOffering2Detail::class, ['id' => 'ah_coff_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAhCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'ah_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAhCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ah_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAhSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'ah_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAhSrd2Rel()
    {
        return $this->hasOne(SemesterRegistration2Detail::class, ['id' => 'ah_srd2_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAhStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'ah_std_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAhUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ah_updated_by']);
    }
}
