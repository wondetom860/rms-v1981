<?php

namespace backend\models;

use Yii;
use \common\components\CommonControllerFunctions as CCF;
use frontend\modules\ClassSchedule\models\CsClassScheduleDetail;
use frontend\modules\AcademicCalendar\models\AcActivePrograms;
use frontend\modules\AcademicCalendar\models\AcAcademicYear;

/**
 * This is the model class for table "course_offering2_detail".
 *
 * @property integer $id
 * @property integer $coff2det_coff2_relId
 * @property integer $coff2det_cocat_relId
 * @property integer $coff2det_course_relId
 * @property integer $coff2det_inst_relId
 * @property integer $coff2det_TA_relId
 * @property integer $coff2det_acProg_relId
 * @property integer $coff2det_created_by
 * @property integer $coff2det_bypassEvaluation
 * @property integer $coff2det_registrarApproved
 * @property integer $coff2det_registrarApproved_by
 * @property integer $coff2det_registrarApproved_at
 * @property string $coff2det_created_at
 * @property string $coff2det_updated_at
 * @property integer $coff2det_updated_by
 * 
 * @property AcActivePrograms $coff2detAcProgRel
 * @property FacultyCourseAssignment[] $facultyAssignments
 * @property AddHistory[] $addRequests
 * @property Instructor $coff2detTARel
 * @property CourseCatalog $coff2detCocatRel
 * @property CourseOffering2 $coff2detCoff2Rel
 * @property Course $coff2detCourseRel
 * @property User $coff2detCreatedBy
 * @property Instructor $coff2detInstRel
 * @property User $coff2detUpdatedBy
 */
class CourseOffering2Detail extends \yii\db\ActiveRecord
{

    public $dept = null;
    public $dept2 = null;
    public $admission = NULL;
    public $program = NULL;
    public $department = NULL;
    public $clslvl = NULL;
    public $spec = NULL;

    public $acadYear = NULL;
    public $sem = NULL;

    public $scheduled = 0;


    public $coff2det_dept_relId = NULL;
    public $courseTitle = NULL;
    public $coff2det_clslvl_relId = NULL;

    public $acadYearId;
    public $semId;

    public $inst;
    public $ta;

    public $HoDDept;
    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    /**
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_offering2_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['coff2det_coff2_relId', 'coff2det_cocat_relId', 'coff2det_course_relId', 'coff2det_created_by', 'coff2det_created_at', 'coff2det_updated_at', 'coff2det_updated_by'], 'required'],
            [['coff2det_coff2_relId', 'coff2det_cocat_relId', 'coff2det_course_relId', 'coff2det_inst_relId', 'coff2det_TA_relId', 'coff2det_created_by', 'coff2det_updated_by', 'coff2det_bypassEvaluation', 'coff2det_acProg_relId'], 'integer'],
            [['coff2det_created_at', 'coff2det_updated_at', 'inst', 'ta'], 'safe'],
            [['coff2det_TA_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Instructor::class, 'targetAttribute' => ['coff2det_TA_relId' => 'id']],
            [['coff2det_acProg_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcActivePrograms::class, 'targetAttribute' => ['coff2det_acProg_relId' => 'id']],
            [['coff2det_cocat_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseCatalog::class, 'targetAttribute' => ['coff2det_cocat_relId' => 'id']],
            [['coff2det_coff2_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2::class, 'targetAttribute' => ['coff2det_coff2_relId' => 'id']],
            [['coff2det_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['coff2det_course_relId' => 'id']],
            [['coff2det_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['coff2det_created_by' => 'id']],
            [['coff2det_inst_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Instructor::class, 'targetAttribute' => ['coff2det_inst_relId' => 'id']],
            [['coff2det_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['coff2det_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'coff2det_coff2_relId' => Yii::t('rms', 'Coff2det Coff2 Rel ID'),
            'coff2det_cocat_relId' => Yii::t('rms', 'Coff2det Cocat Rel ID'),
            'coff2det_course_relId' => Yii::t('rms', 'Course'),
            'coff2det_bypassEvaluation' => Yii::t('rms', 'Evaluation'),
            'coff2det_inst_relId' => Yii::t('rms', 'Instructor'),
            'inst' => Yii::t('rms', 'Instructor'),
            'coff2det_TA_relId' => Yii::t('rms', 'Tech. Ass.'),
            'ta' => Yii::t('rms', 'Tech. Ass.'),
            'coff2det_created_by' => Yii::t('rms', 'Created By'),
            'coff2det_created_at' => Yii::t('rms', 'Created At'),
            'coff2det_updated_at' => Yii::t('rms', 'Updated At'),
            'coff2det_updated_by' => Yii::t('rms', 'Updated By'),
            'clslvl' => Yii::t('rms', 'Class Lvl.'),
            'coff2det_registrarApproved' => Yii::t('rms', 'Approval Status'),
        ];
    }

    public function getAttendaceReport($std)
    {
        $att = new Attendance();
        $a_rep = $att->getAttendanceReport2($std, $this->coff2det_course_relId, $this->coff2detCoff2Rel->coff2_group_relId);
        $attendance = $a_rep[0];
        $attendance = is_numeric($attendance) ? sprintf('%0.2f', 100 - $attendance) : $attendance;
        return [$attendance,$a_rep[1]];
    }

    public function getCourseAddRequests()
    {
        return $this->getAddRequests()
            ->andWhere(['ah_approved' => NULL])
            ->count();
    }

    public function getMD()
    {
        return $this->find()
            ->all();
    }
    public function toggleBypassStatus()
    {
        $this->coff2det_bypassEvaluation = $this->coff2det_bypassEvaluation ? NULL : 1;
        return $this->update();
    }
    public function getOfferingAcadYearInfo()
    {
        return $this->coff2detCoff2Rel->getOfferingAcadeYearInfo();
    }

    public function getScheduledCount()
    {
        return "[ " . $this->getScheduledCountLectureHr() . " - " . $this->getScheduledCountLabHr() . " - " . $this->getScheduledCountTutorialHr() . "]";
    }

    public function getOfferedGroups()
    {
        // 'coff2det_course_relId' => $this->coff2det_course_relId,
        return self::find()
            ->innerJoin('course_offering2', 'course_offering2.id=course_offering2_detail.coff2det_coff2_relId')
            ->where([
                'coff2det_inst_relId' => $this->coff2det_inst_relId,
                'coff2_acadYear_relId' => $this->coff2detCoff2Rel->coff2_acadYear_relId,
                'coff2_sem_relId' => $this->coff2detCoff2Rel->coff2_sem_relId,
            ])
            ->all();
    }

    public function getEvaluationResultV2()
    {
        $facus = $this->facultyAssignments;
        $btns = '';
        foreach ($facus as $fac) {
            // check for evaluation control enforcements -- above_half_requirement
            $printable = $fac->evaluationPrintable();
            if (is_bool($printable) && $printable == true) {
                $course = $fac->coff2detRel->coff2detCourseRel->getCourseCredits();
                $btns .= "<button class='btn btn-xs btn-warning' title='Print Evalution result for course {$course} as {$fac->assign_as}' style='margin: 0px 4px;' onclick='printEvaluationResultV2({$fac->id}); return false;'>Print</button>";
            } else {
                $btns = $printable;
                break;
            }
        }
        return $btns;
    }

    public function updateFaclutyAssignmentData()
    {
        $count = FacultyCourseAssignment::find()
            ->where([
                'coff2det_relId' => $this->id,
                'inst_relId' => $this->coff2det_inst_relId,
                'assign_as' => 'Instructor',
            ])
            ->count();
        if ($this->coff2det_inst_relId && ($count == 0)) {
            $modelFCA = new FacultyCourseAssignment();
            $modelFCA->coff2det_relId = $this->id;
            $modelFCA->inst_relId = $this->coff2det_inst_relId;
            $modelFCA->assign_as = 'Instructor';
            if (!$modelFCA->exists()) {
                $modelFCA->getData();
                $modelFCA->created_by = Yii::$app->user->id;
                $modelFCA->created_at = time();

                if ($modelFCA->save()) {
                    unset($modelFCA);
                } else {
                    var_dump($modelFCA->getErrors());
                    die();
                }
            }
        }
        // collect TA inforamtion if there is any set
        $count = FacultyCourseAssignment::find()
            ->where([
                'coff2det_relId' => $this->id,
                'inst_relId' => $this->coff2det_TA_relId,
                'assign_as' => 'Technical Assistant',
            ])
            ->count();
        if ($this->coff2det_TA_relId && ($count == 0)) {
            $modelFCA = new \backend\models\FacultyCourseAssignment();
            $modelFCA->coff2det_relId = $this->id;
            $modelFCA->inst_relId = $this->coff2det_TA_relId;
            $modelFCA->assign_as = 'Technical Assistant';

            if (!$modelFCA->exists()) {
                $modelFCA->getData();
                $modelFCA->created_by = Yii::$app->user->id;
                $modelFCA->created_at = time();

                if ($modelFCA->save()) {
                    unset($modelFCA);
                } else {
                    var_dump($modelFCA->getErrors());
                    die();
                }
            }
        }
    }

    public function getTBACredits($activeProgramId, $role)
    {
        $TBACredits = ['iTBA' => 0, 'tTBA' => 0];
        if (Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
            $offerings = self::find()
                ->innerJoin('ac_active_programs', 'ac_active_programs.id=course_offering2_detail.coff2det_acProg_relId')
                ->innerJoin('course', 'course.id=course_offering2_detail.coff2det_course_relId')
                ->where([
                    'academic_year_relId' => $activeProgramId,
                    'course_dept_relId' => $inst->inst_dep_relId,
                ])
                ->andWhere(['OR', ['coff2det_inst_relId' => NULL], ['coff2det_TA_relId' => NULL]])
                ->all();
        } else if (\Yii::$app->user->can('AcademicDean')) {
            $offerings = self::find()
                ->innerJoin('ac_active_programs', 'ac_active_programs.id=course_offering2_detail.coff2det_acProg_relId')
                ->where(['academic_year_relId' => $activeProgramId])
                ->andWhere(['OR', ['coff2det_inst_relId' => NULL], ['coff2det_TA_relId' => NULL]])
                ->all();
        }

        if ($offerings && count($offerings) > 0) {
            foreach ($offerings as $coff2det) {
                if (($coff2det->coff2detCourseRel->course_lecture_hour != 0) && is_null($coff2det->coff2det_inst_relId)) {
                    $TBACredits['iTBA']++;
                }
                if (($coff2det->coff2detCourseRel->course_lab_hour != 0) && is_null($coff2det->coff2det_TA_relId)) {
                    $TBACredits['tTBA']++;
                }
            }
        }
        return $TBACredits;
    }

    public function getStaffCourseOffering($role, $staffId, $acadYearId)
    {

        // 

        if ($role == 'INSTRUCTOR') {
            return self::find()
                ->innerJoin('course', 'course.id=coff2det_course_relId')
                ->innerJoin('ac_active_programs', 'ac_active_programs.id=course_offering2_detail.coff2det_acProg_relId')
                ->where(['academic_year_relId' => $acadYearId])
                ->andWhere(['coff2det_inst_relId' => $staffId])
                ->all();
        } else if ($role == 'TA') {
            return self::find()
                ->innerJoin('course', 'course.id=coff2det_course_relId')
                ->innerJoin('ac_active_programs', 'ac_active_programs.id=course_offering2_detail.coff2det_acProg_relId')
                ->where(['academic_year_relId' => $acadYearId])
                ->andWhere(['coff2det_TA_relId' => $staffId])
                ->all();
        }
    }
    public function getOfferingsforThisAcademicYear2()
    {
        $AcademicYearModel = new AcAcademicYear();
        $instDept = null;
        $staffs = null;
        $staffIds = [];
        if (Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
            if ($inst) {
                $instDept = $inst->inst_dep_relId;
                $staffs = \backend\models\Instructor::find()
                    ->where(['inst_dep_relId' => $instDept])
                    ->orderBy([
                        'inst_dep_relId' => SORT_ASC,
                        'inst_acq_relId' => SORT_DESC,
                        'inst_acr_relId' => SORT_DESC
                    ])
                    ->all();
            } else {
                return null;
            }
        } else {
            $staffs = \backend\models\Instructor::find()
                ->orderBy([
                    'inst_dep_relId' => SORT_ASC,
                    'inst_acq_relId' => SORT_DESC,
                    'inst_acr_relId' => SORT_DESC
                ])
                ->all();
        }

        return $staffs;
    }
    public function getOfferingsforThisAcademicYear()
    {
        $AcademicYearModel = new AcAcademicYear();
        $instDept = null;
        $staffs = null;
        $staffIds = [];
        if (\Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
            if ($inst) {
                $instDept = $inst->inst_dep_relId;
                $staffs = \backend\models\Instructor::find()->where(['inst_dep_relId' => $instDept])->all();
            } else {
                return null;
            }
        } else {
            $staffs = \backend\models\Instructor::find()->all();
        }

        foreach ($staffs as $staff) {
            $staffIds[] = $staff->id;
        }

        return self::find()
            ->innerJoin('course', 'course.id=coff2det_course_relId')
            ->innerJoin('ac_active_programs', 'ac_active_programs.id=course_offering2_detail.coff2det_acProg_relId')
            ->where(['ac_active_programs.status' => 1])
            ->andWhere(['OR', ['coff2det_inst_relId' => $staffIds], ['coff2det_TA_relId' => $staffIds]])
            ->orderBy(['coff2det_inst_relId' => SORT_DESC])
            ->all();


        // $ActiveAcademicYears = $AcademicYearModel->getActiveAcademicYear(null);
        // $activeProgIds = [];
        // if ($ActiveAcademicYears && count($ActiveAcademicYears) > 0) {
        //     foreach ($ActiveAcademicYears as $activeYear) {
        //         foreach ($activeYear->acActivePrograms as $activeProg) {
        //             if (!is_null($instDept) && $instDept != $activeProg->dept_relId) {
        //                 continue;
        //             }
        //             $activeProgIds[] = $activeProg->id;
        //         }
        //     }
        // }
        // if (!is_null($instDept)) {
        //     return self::find()
        //         ->innerJoin('course', 'course.id=coff2det_course_relId')
        //         ->innerJoin('ac_active_programs', 'ac_active_programs.id=course_offering2.coff2_acProg_relId')
        //         ->where(['ac_active_programs.status' => 1])
        //         ->andWhere('OR', ['coff2det_inst_relId' => $staffIds], ['coff2det_TA_relId' => $staffIds])
        //         ->orderBy(['coff2det_inst_relId' => SORT_DESC])
        //         ->all();
        // }
        // return self::find()
        //     ->where(['coff2det_acProg_relId' => $activeProgIds])
        //     ->groupBy('coff2det_inst_relId')
        //     ->orderBy(['coff2det_inst_relId' => SORT_DESC])
        //     ->all();
    }

    public function haveExtraScheduleTime($type)
    {
        $result = false;

        if (strtoupper($type) == 'LEC') {
            return count($this->getCsClassScheduleDetails()->andFilterWhere(['class_type' => 'lec'])->all()) < $this->coff2detCourseRel->course_lecture_hour;
        } elseif (strtoupper($type) == 'LAB') {
            return count($this->getCsClassScheduleDetails()->andFilterWhere(['class_type' => 'lab'])->all()) < $this->coff2detCourseRel->course_lab_hour;
        } elseif (strtoupper($type) == 'TUT') {
            return count($this->getCsClassScheduleDetails()->andFilterWhere(['class_type' => 'tut'])->all()) < $this->coff2detCourseRel->course_tutorial_hour;
        } else {
            return false;
        }
    }

    public function getScheduledCountLectureHr()
    {
        return count($this->getCsClassScheduleDetails()->andFilterWhere(['class_type' => 'lec'])->all()) . "/" . $this->coff2detCourseRel->course_lecture_hour;
    }
    public function getScheduledCountLabHr()
    {
        return count($this->getCsClassScheduleDetails()->andFilterWhere(['class_type' => 'lab'])->all()) . "/" . $this->coff2detCourseRel->course_lab_hour;
    }

    public function getScheduledCountTutorialHr()
    {
        return count($this->getCsClassScheduleDetails()->andFilterWhere(['class_type' => 'tut'])->all()) . "/" . $this->coff2detCourseRel->course_tutorial_hour;
    }

    public function getCoursesForAdd()
    {
        // get all courses offered in this semester:

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCsClassScheduleDetails()
    {
        return $this->hasMany(CsClassScheduleDetail::class, ['coff2det_relId' => 'id']);
    }


    public function getOfferedProjectCourses($gsetIds, $filterThesisII = NULL, $intId = null)
    {
        // 
        if (!is_null($filterThesisII)) {
            return $this->find()
                ->innerJoin('course', 'course.id = coff2det_course_relId')
                ->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                ->andFilterWhere(['coff2_group_relId' => $gsetIds,])
                ->andFilterWhere(['like', 'course.course_title', 'THESIS PHASE II'])
                ->distinct('coff2det_course_relId')
                ->all();
        }
        return $this->find()
            ->innerJoin('course_catalog', 'course_catalog.id = coff2det_cocat_relId')
            ->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
            ->innerJoin('course', 'course.id = coff2det_course_relId')
            ->andWhere([
                'coff2_group_relId' => $gsetIds,
            ])
            ->andWhere(['course.course_type' => [2,3]])//other than graded courses
            ->distinct('coff2det_course_relId')
            ->all();
    }


    public static function getOfferedCourses($gsetObject)
    {
        return CourseOffering2Detail::find()
            ->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
            ->where([
                'coff2_group_relId' => $gsetObject->id,
                'coff2det_registrarApproved' => 1,
            ])
            ->all();
    }

    public function getHodAlert()
    {
        $instDept = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id])->inst_dep_relId;

        // $this->__constructor();
        // course Offering
        // ->andWhere([
        //         'course_offering2.coff2_acadYear_relId'=>$this->acadYearId,
        //         'course_offering2.coff2_sem_relId'=>$this->semId,
        //     ])
        return $this->find()
            ->innerJoin('course', 'course.id = course_offering2_detail.coff2det_course_relId')
            ->innerJoin('course_offering2', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
            ->innerJoin('course_catalog', 'course_catalog.id = coff2det_cocat_relId')
            ->where([
                'course.course_dept_relId' => $instDept,
            ])
            ->andWhere('course_offering2_detail.coff2det_inst_relId IS NULL OR (course.course_lab_hour <> 0 AND course_offering2_detail.coff2det_TA_relId IS NULL)')
            ->andWhere(['<>', 'cocat_courseProject', 1])
            ->andWhere(['<>', 'course.course_entry_type', 2])
            ->count();
    }

    public static function getAlertNotification()
    {

        $title = "Course Offering Approval";
        $link = "/course-offering2-ext";
        $count = self::find()
            ->innerJoin('course_offering2', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
            ->where(['coff2det_registrarApproved' => NULL])
            ->count();
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

    public function RegistrarApproveNotification()
    {
        $this->__constructor();
        return $this->find()
            ->innerJoin('course_offering2', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
            ->where(['coff2det_registrarApproved' => NULL])
            ->andWhere([
                'course_offering2.coff2_acadYear_relId' => $this->acadYearId,
                'course_offering2.coff2_sem_relId' => $this->semId,
            ])
            ->count();
    }

    // get groups , instructors of the course
    public function getDepts($courseId)
    {
        $this->__constructor();
        $courses = $this->find()
            ->innerJoin('course', 'course.id = course_offering2_detail.coff2det_course_relId')
            ->innerJoin('course_offering2', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
            ->where([
                'coff2det_course_relId' => $courseId,
            ])
            ->andWhere([
                'course_offering2.coff2_acadYear_relId' => $this->acadYearId,
                'course_offering2.coff2_sem_relId' => $this->semId,
            ])
            ->groupBy('course_offering2_detail.coff2det_inst_relId')
            ->all();
        $ps = "";
        $seles = "";
        if ($courses && count($courses)) {
            $seles .= "<select class ='form-control' id='inst_select_{$courses[0]->id}' onchange='assignCordinator($(this).val(),{$courses[0]->id})'> ";
            $seles .= "<option data-course='' data-adm='' data-inst= value=''> - </option>";
            $inst = NULL;
            $user = NULL;
            // check if already assigned.
            $exists = \backend\models\CourseCordinator::find()
                ->where([
                    'cc_acadYear_relId' => $this->acadYearId,
                    'cc_sem_relId' => $this->semId,
                    'cc_course_relId' => $courseId,
                ])
                ->one();
            foreach ($courses as $key) {
                $group = \backend\models\WGroupSetup::findOne($key->coff2detCoff2Rel->coff2_group_relId);
                $gname = \backend\models\WGroup::findOne($group->gs_group_relId)->group_name;
                $dept = \backend\models\Department::findOne($group->gs_dept_relId)->dept_code;

                $inst = NULL;
                if (is_null($key->coff2det_inst_relId)) {
                    // $seles .= "<option data-course='{$courseId}' data-adm='{$group->gs_adm_relId}' data-inst='' value=''>TBA</option>";
                    $ps .= "<p class='btn-primary' style='border-bottom:1px solid black; border-radius: 10px; padding : 5px;'>{$gname} ($dept) [TBA]";
                } else {

                    $inst = \backend\models\Instructor::findOne($key->coff2det_inst_relId);
                    $user = \common\models\User1::findOne($inst->inst_user_relId);

                    $seles .= "<option data-course='{$courseId}' data-adm='{$group->gs_adm_relId}' data-inst={$inst->id} value='{$inst->id}'>{$user->first_name} {$user->middle_name}</option>";
                    $ps .= "<p class='btn-primary' style='border-bottom:1px solid black; border-radius: 10px; padding : 5px;'>{$gname} ($dept) [{$user->first_name} {$user->middle_name[0]}]";
                }

                if ($exists) {
                    $btn = "<button class='btn btn-warning btn-xs' title='change cordinator' onclick='removeCordinator({$exists->id})'>Change</button>";
                    $inst2 = \backend\models\Instructor::findOne($exists->cc_inst_relId);
                    $user2 = \common\models\User1::findOne($inst2->inst_user_relId);

                    $seles = "<p data-course='{$courseId}' data-inst={$inst2->id} data-adm='{$group->gs_adm_relId}' value='{$inst2->id}'>{$user2->first_name} {$user2->middle_name} | {$btn}</p>";
                }
            }
        }

        $seles .= "</select>";
        return ['ps' => $ps, 'seles' => $seles];
        // die();

    }

    public function checkRequired($courseId)
    {
        $this->__constructor();
        // course Offering
        $courses = $this->find()
            ->innerJoin('course', 'course.id = course_offering2_detail.coff2det_course_relId')
            ->innerJoin('course_offering2', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
            ->where([
                'course.id' => $courseId,
            ])
            ->andWhere([
                'course_offering2.coff2_acadYear_relId' => $this->acadYearId,
                'course_offering2.coff2_sem_relId' => $this->semId,
            ])
            ->groupBy('course_offering2_detail.coff2det_course_relId')
            ->all();

        $coffdets = [];
        if ($courses && count($courses) > 0) {
            foreach ($courses as $key) {
                $counts = $this->find()
                    ->innerJoin('course_offering2', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
                    ->where([
                        'coff2det_course_relId' => $key->coff2det_course_relId,
                    ])
                    ->andWhere([
                        'course_offering2.coff2_acadYear_relId' => $this->acadYearId,
                        'course_offering2.coff2_sem_relId' => $this->semId,
                    ])
                    ->groupBy('course_offering2_detail.coff2det_inst_relId')
                    ->count();
                if ($counts > 1) {
                    // this course has multiple insts.
                    $coffdets[] = $key;
                }
            }
        }

        return $coffdets;
    }

    // get courses that have multiple instructors assigned.
    public function getmInstedCourses()
    {
        if (\Yii::$app->user->can('DepartmentHead')) {
            $instDept = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id])->inst_dep_relId;

            $this->__constructor();
            // course Offering
            $courses = $this->find()
                ->innerJoin('course', 'course.id = course_offering2_detail.coff2det_course_relId')
                ->innerJoin('course_offering2', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
                ->where([
                    'course.course_dept_relId' => $instDept,
                ])
                ->andWhere([
                    'course_offering2.coff2_acadYear_relId' => $this->acadYearId,
                    'course_offering2.coff2_sem_relId' => $this->semId,
                ])
                ->groupBy('course_offering2_detail.coff2det_course_relId')
                ->all();

            $coffdets = [];
            if ($courses && count($courses) > 0) {
                foreach ($courses as $key) {
                    $counts = $this->find()
                        ->innerJoin('course_offering2', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
                        ->where([
                            'coff2det_course_relId' => $key->coff2det_course_relId,
                        ])
                        ->andWhere([
                            'course_offering2.coff2_acadYear_relId' => $this->acadYearId,
                            'course_offering2.coff2_sem_relId' => $this->semId,
                        ])
                        ->groupBy('course_offering2_detail.coff2det_inst_relId')
                        ->count();
                    if ($counts > 1) {
                        // this course has multiple insts.
                        $coffdets[] = $key;
                    }
                }
            }

            return $coffdets;
        }
    }

    public function byPassEvaluation()
    {
        return $this->coff2det_bypassEvaluation;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detTARel()
    {
        return $this->hasOne(Instructor::class, ['id' => 'coff2det_TA_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detAcProgRel()
    {
        return $this->hasOne(AcActivePrograms::class, ['id' => 'coff2det_acProg_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detCocatRel()
    {
        return $this->hasOne(CourseCatalog::class, ['id' => 'coff2det_cocat_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detCoff2Rel()
    {
        return $this->hasOne(CourseOffering2::class, ['id' => 'coff2det_coff2_relId']);
    }

    public function getCourseOfferingHistory()
    {
        return $this->find()
            ->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
            ->innerJoin('w_group_setup', 'w_group_setup.id = course_offering2.coff2_group_relId')
            ->innerJoin('w_group', 'w_group.id = w_group_setup.gs_group_relId')
            ->where(['coff2det_course_relId' => $this->coff2det_course_relId])
            ->orderBy([
                'gs_acadYear_relId' => SORT_DESC,
                'gs_sem_relId' => SORT_DESC,
                'group_name' => SORT_ASC,
            ])
            ->all();
    }

    public function getEnrollsCount()
    {
        return $this->coff2detCoff2Rel
            ->getSemesterRegistration2Details($this->coff2det_course_relId)
            ->count();
    }

    public function getEnrolls()
    {
        return $this->coff2detCoff2Rel
            ->getSemesterRegistration2Details($this->coff2det_course_relId)
            ->all();
    }

    public function getEnrolls2()
    {
        return $this->coff2detCoff2Rel
            ->getSemesterRegistration2Details($this->coff2det_course_relId);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'coff2det_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContAssessCatagory2s()
    {
        return $this->hasMany(ContAssessCatagory2::class, ['cat_coff_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContAssessCatagories($coffDetModel)
    {
        return ContAssessCatagory2::find()
            ->innerJoin('course_offering2_detail', 'course_offering2_detail.id=cont_assess_catagory2.cat_coff_relId')
            ->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
            ->where([
                'coff2_acadYear_relId' => $coffDetModel->coff2detCoff2Rel->coff2_acadYear_relId,
                'coff2_sem_relId' => $coffDetModel->coff2detCoff2Rel->coff2_sem_relId,
                'coff2det_course_relId' => $coffDetModel->coff2det_course_relId,
            ])
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'coff2det_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detInstRel()
    {
        return $this->hasOne(Instructor::class, ['id' => 'coff2det_inst_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContinuousAssessment2s()
    {
        return $this->hasMany(ContinuousAssessment2::class, ['ca_coff_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacultyAssignments()
    {
        return $this->hasMany(FacultyCourseAssignment::class, ['coff2det_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddRequests()
    {
        return $this->hasMany(AddHistory::class, ['ah_coff_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'coff2det_updated_by']);
    }
}
