<?php

namespace common\components;

use yii;
// use frontend\models\AddHistory;
use frontend\models\SemesterRegistration2;
use frontend\models\SemesterRegistration2Detail;
use backend\models\SchoolYear;
use \backend\models\ContAssessCatagory2;
use \backend\models\InstanceMarkAssignment;
use backend\models\ReassessmentHistory;
use backend\models\StudentSemesterStatusName;
use backend\models\WGroupAssignment;
use ErrorException;

class CommonControllerFunctions extends \yii\web\Controller
{
    // date_default_timezone_set('Ethiopia/Addis Abeba');

    //define('SYSTEM_START_YEAR', '2018/19');
    public static $acadYearId = null;
    public static $semId = null;

    public static $sched = NULL;
    protected static function __constructor()
    {

        // var_dump(Yii::$app->params['C']['Y']);die();
        static::$semId = static::getCurrentSemester(Yii::$app->params['C']['Y']);
        if (is_null(static::$semId) || is_string(static::$semId)) {
            $acadYear = SchoolYear::findOne(['sch_year_desc' => Yii::$app->params['C']['Y']]);
            if ($acadYear) {
                static::$acadYearId = $acadYear->id;
            } else {
                static::$acadYearId = static::createSchoolYear(Yii::$app->params['C']['Y']);
            }
        } else {
            Yii::$app->params['C']['S'] = is_null(static::$semId) || is_string(static::$semId) ? 'NONE' : static::$semId->semesterRel;
            Yii::$app->params['C']['Y'] = is_null(static::$semId) ? Yii::$app->params['C']['Y'] : static::$semId->schoolYearRel;

            static::$acadYearId = is_null(static::$semId) ? NULL : static::$semId->schoolYearRel->id;
            static::$semId = is_null(static::$semId) ? NULL : static::$semId->semesterRel->id;
        }


        if (Yii::$app->params['C']['S'] && !is_string(Yii::$app->params['C']['S'])) {
            // var_dump(Yii::$app->params['C']['S']);die();
            Yii::$app->params['C']['S'] = Yii::$app->params['C']['S']->semester_name;
            Yii::$app->params['C']['Y'] = Yii::$app->params['C']['Y']->sch_year_desc;
        }
    }


    protected static function getCurrentSemester($acadYear)
    { // acadYear = like 2018/19
        // returns current academic year and semester
        $activeProgram = Yii::$app->AcademicCalendar->getActiveProgramModels();

        if ($activeProgram && count($activeProgram) > 0) {
            // var_dump($activeProgram[0]);
        } else {
            return NULL;
        }

        // getActiveAcademicYear

        try {
            $acadYearId = SchoolYear::findOne(['sch_year_desc' => $activeProgram[0]->school_year]);

            if (is_null($acadYearId)) {
                $acaYearNew = static::createSchoolYear(Yii::$app->params['C']['Y']);
                if (!is_null($acaYearNew)) {
                    $acadYearId = $acaYearNew;
                } else {
                    echo "<p class='bg-danger'>Academic Year Initialization error!</p>";
                    return;
                }
            }

            // static::$acadYearId = $acadYearId->id;
            return $activeProgram[0];


            // $actionName = \backend\models\AcademicCalanderAction::find()->where(['aca_name'=>['Semester I','Semester II','Summer']])->all();

            // $semActions  =[];
            // if ($actionName && count($actionName) > 0) {
            //     foreach ($actionName as $key) {
            //         $semActions[] = $key['id'];
            //     }
            // }else{
            //     return '<span class="bg-danger">NONE</span>';
            // }

            // // ->andWhere(':sDate between acad_starttime AND acad_finishtime', [':sDate' => date('Y-m-d h:i:s'] )
            // $schedule = \backend\models\AcademicCalander::find()
            //             ->where([
            //                 'acad_action_relId'=>$semActions,
            //             ])
            //             ->andWhere(['IS NOT','acad_sem_relId',NULL])
            //             ->andWhere([
            //                         '<=','acad_starttime',date('Y-m-d h:i:s'),
            //                     ])
            //             ->andWhere(['>=','acad_finishtime',date('Y-m-d h:i:s')
            //                     ])
            //             ->one();

            // if ($schedule && count($schedule) > 0) {
            //     // var_dump($schedule);die9)
            //     return $schedule;
            // }else{
            //     return  "<span class='bg-info' style='font-size:8pt; color:red;'>Not Set.</span>";
            //     // if (Yii::$app->user->can('Admin')) {
            //     //     // if(Yii::$app->session->hasFlash('redirected')) {
            //     //     //     exit();
            //     //     // }else{
            //     //     //     Yii::$app->session->setFlash('redirected', 1);
            //     //     //     // return Yii::$app->response->redirect(['academic-calander/create'])->send(false);
            //     //     // }
            //     // }else{

            //     // }

            // }
        } catch (\Exception $e) {
            print_r($e);
            die();
        }
    }


    public static function isGCStudent($gsetObj)
    {
        return static::GCStudent($gsetObj);
    }

    public static function getSemesterStatusBYWONDE($semregObj)
    {
        if ($semregObj) {
            //incase if this is last grade approval and ready for calculating status of student!
            foreach ($semregObj as $semregKey) {
                // 1
                static::getSemesterGrade($semregKey->semreg_clslvl_relId, $semregKey->semreg_semester_relId, $semregKey->semreg_std_relId);
            }
        }
    }

    private static function GCStudent($gset)
    {
        if (strtoupper($gset->gsProgRel->program_name) == 'MASTERS') {
            return $gset->gsClslvlRel->clslvl_num * 2 == 4;
        } else if (strtoupper($gset->gsProgRel->program_name) == 'DEGREE') {
            return $gset->gsClslvlRel->clslvl_num * 2 == 10;
        } else {
            return false;
        }
    }

    // return back the grades for re-assessment requested courses if the request is rejected or the date is over.
    public static function returnBackGrade($reqId)
    {
        $request  = ReassessmentHistory::findOne([
            'id' => $reqId,
            'ras_aft_grade_relId' => NULL,
        ]);
        if ($request) {
            $result = Yii::$app->db
                ->createCommand()
                ->update(
                    'semester_registration_2_detail',
                    [
                        'srd2_grade_relId' => $request->ras_bef_grade_relId,
                    ],
                    [
                        'id' => $request->ras_srd2_relId,
                    ]
                )
                ->execute();
            if ($result) {
                return TRUE;
            } else {
                throw new ErrorException("Error while rejecteting the request, Please try again.", 1);
            }
        } else {
            throw new ErrorException("Such request not found.", 1);
        }
    }

    public static function currentTime()
    {
        static::__constructor();
        static::nextYear();
        $activeAcademicYear = null;
        $ac = Yii::$app->AcademicCalendar->getActiveProgramModels(null);
        if ($ac && count($ac) > 0) {
            $activeAcademicYear = $ac[0];
        } else {
            return ['Y' => static::$acadYearId, 'S' => static::$semId, 'schedule' => static::$sched];
        }
        $thisSem = $activeAcademicYear->semesterRel;
        static::$acadYearId = $activeAcademicYear->schoolYearRel->id;
        static::$semId = $thisSem->id;
        static::$sched = $activeAcademicYear->getSchedule();
        return ['Y' => static::$acadYearId, 'S' => static::$semId, 'schedule' => static::$sched];
    }

    private static function createSchoolYear($year)
    {
        $ct = $year;
        // $transation = Yii::$app->db->beginTransaction();
        $acadYear = SchoolYear::findOne(['sch_year_desc' => $ct]);
        if (empty($acadYear)) {
            $model = new SchoolYear();
            $model->sch_year_desc = $ct;
            $model->sch_year_status = 1;
            $model->sch_created_at = date("Y-m-d h:i:s");
            $model->sch_updated_at = date("Y-m-d h:i:s");

            if (!$model->save()) {
                // $transation->rollBack();
                // var_dump($model);
                print_r($model->errors);
                die();
            } else {
                // $transation->commit();
                return $model->id;
            }
        }
    }

    // checks if the schedule is open for the specified action.
    public static function getScheduleForAction($gset, $actionId, $actionUniqueId = NULL)
    {
        // var_dump($gset->gsAdmRel->admission_name);die();
        if (!is_null($gset)) {
            $activeProgram = $gset->gsActiveProgRel;
            $modelAps = new \frontend\modules\AcademicCalendar\models\AcProgramSchedule();
            return $modelAps->checkIfScheduleActive($actionId, $gset->gs_active_prog_relId);

            // $schedule = AcademicCalander::find()
            //     ->where([
            //         'acad_acadYear_relId'=>$gset->gs_acadYear_relId,
            //         'acad_sem_relId'=>$gset->gs_sem_relId,
            //         'acad_action_relId'=>$actionId,
            //     ])
            //     ->andWhere(['OR', ['acad_adm_relId' => $gset->gs_adm_relId], ['acad_adm_relId' => NULL]])
            //     ->andWhere(['OR', ['acad_prog_relId' => $gset->gs_prog_relId], ['acad_prog_relId' => NULL]])
            //     ->andWhere(['OR', ['acad_clslvl_relId' => $gset->gs_clslvl_relId], ['acad_clslvl_relId' => NULL]])
            //     ->andWhere(['<=', 'acad_starttime', date('Y-m-d h:i:s')])
            //     ->andWhere(['>=', 'acad_finishtime', date('Y-m-d h:i:s')])
            //     ->all();
            // // var_dump($schedule[0]->acadAdmRel->admission_name);die();
            // return $schedule;
        } else {
            // check only for action Id and academic calaneder status for this semsetr.
            static::__constructor();
            $model = new \backend\models\AcademicCalander();
            // $actionName = ;
            $schedule = $model->checkIfOpen(strtoupper('Semester evaluation'));
            // $schedule = \backend\models\AcademicCalander::find()
            //     ->where([
            //         'acad_acadYear_relId' => static::$acadYearId,
            //         'acad_sem_relId' => static::$semId,
            //         'acad_action_relId' => $actionId,
            //     ])
            //     ->andWhere([
            //         '<=',
            //         'acad_starttime',
            //         date('Y-m-d h:i:s'),
            //     ])
            //     ->andWhere([
            //         '>=',
            //         'acad_finishtime',
            //         date('Y-m-d h:i:s')
            //     ]);
            // $schedule = $schedule->one();
            // var_dump($schedule,$actionId,$gset,static::$acadYearId,static::$semId);exit;
            return $schedule ? $schedule[0] : NULL;
        }
        // return NULL;
    }

    public static function autoCalculateStudentSemesterGrade()
    {
        // check if it is 10 days before current semester ends:

        // get all enrolled students in this academic year and semester by their group

        // for each student with his / her gset, calculate his / her grade report,

        // No reporting is required.
    }

    // return the list of courses that are in catalog.
    public static function getCatalogCourse2($gset)
    {
        return static::getCocat($gset);
    }

    // return the list of courses that are in catalog.
    public static function getCatalogCourse($gset)
    {
        return static::getCocat($gset);
    }

    // // get courses enrolled by the student per semester.
    // public static function getStudentEnrolledCoursesPerSemester($studentId,$semesterI){

    // }

    protected static function getMaxAllowedCreditHrPerSemesterAC($gsetObject, $stdObject = NULL)
    {
        // check if any AC decission exists for the student indiviudaually =>semester_registration_controll

        // initiate current acadYear and sem
        static::__constructor();

        // Not made available yet. for student. let's check for the batch

        $ccc = \backend\models\SemesterRegistrationControlac::find()
            ->where([
                'src_acadYear_relId' => $gsetObject->gs_acadYear_relId,
                'src_sem_relId' => $gsetObject->gs_sem_relId,
                'src_adm_relId' => $gsetObject->gs_adm_relId,
                'src_prog_relId' => $gsetObject->gs_prog_relId,
                'src_clslvl_relId' => $gsetObject->gs_clslvl_relId,
            ])
            ->andWhere(['OR', ['src_group_relId' => $gsetObject->id], ['src_group_relId' => NULL]])
            ->andWhere(['OR', ['src_dept_relId' => $gsetObject->gs_dept_relId], ['src_dept_relId' => NULL]]);
        if (isset($gsetObject->gs_spec_relId)) {
            $ccc = $ccc->andWhere(['OR', ['src_spec_relId' => $gsetObject->gs_spec_relId], ['src_spec_relId' => NULL]]);
        }
        $ccc = $ccc->one();
        return $ccc;
    }

    // Get allowed semester creditHr for the batch ---> StudentLevel MaxCreditAllowance?
    public static function getMaxAllowedCreditHrPerSemester($gsetObject, $stdObject = NULL)
    {

        static::__constructor();
        // check if any AC decision exists for the batch =>semester_registration_controll
        $ccc = null;
        $ccc = static::getMaxAllowedCreditHrPerSemesterAC($gsetObject, $stdObject);
        if ($ccc) {
        } else {
            // If there is no any, follow the default.
            $ccc = \backend\models\SemesterRegistrationControld::find()
                ->where([
                    'src_adm_relId' => $gsetObject->gs_adm_relId,
                    'src_prog_relId' => $gsetObject->gs_prog_relId,
                ])
                ->andWhere([
                    'OR',
                    ['src_sem_relId' => $gsetObject->gs_sem_relId],
                    ['src_sem_relId' => NULL]
                ])
                ->andWhere([
                    'OR',
                    ['src_clslvl_relId' => $gsetObject->gs_clslvl_relId],
                    ['src_clslvl_relId' => NULL]
                ])
                ->one();
        }
        return $ccc;
    }
    // Get list of students who enrolled the course in this semester.
    public static function getTotalEnrolledStudents($gsetObject, $courseObject)
    {
        return static::getTotalEnrolledStudentsInner($gsetObject, $courseObject);
    }
    // count total students whp enrolled the course in the group.
    protected static function getTotalEnrolledStudentsInner($gsetObject, $courseObject)
    {
        static::__constructor();
        $semreg = \frontend\models\SemesterRegistration2::find()
            ->where([
                'semreg_acadYear_relId' => static::$acadYearId,
                'semreg_semester_relId' => static::$semId,
            ])
            ->innerJoin('semester_registration_2_detail', 'semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
            ->andWhere([
                'srd2_group_relId' => $gsetObject->id,
                'srd2_course_relId' => $courseObject->id
            ])
            ->with('semesterRegistration2Detail');

        return $semreg;
    }


    protected static function nextYear()
    {
        $m = intval(date('m')) + 7;
        $y = date('Y');
        $nYear = '';
        // slice year to take only the last two digits.
        $sYear = $y % 1000;
        if ($m >= 9) {
            $nYear = $y . '/' . ($y + 1) % 1000;
        } else if ($m < 9) {
            $nYear = ($y - 1) . '/' . $y % 1000;
        }
        static::createSchoolYear($nYear);
    }

    public static function getCocat2P($gset)
    {
        return static::getCocat22($gset);
    }

    // common get file for course adding
    protected static function getCocat22($gset)
    {
        if (!is_object($gset)) {
            return NULL;
        }
        $cocats = \backend\models\CourseCatalog::find()->where([
            'cocat_admission_relId' => $gset->gs_adm_relId,
            'cocat_dept_relId' => $gset->gs_dept_relId,
            'cocat_program_relId' => $gset->gs_prog_relId,
        ]);

        if (isset($gset->gs_spec_relId)) {
            $cocats = $cocats->andWhere(['OR', ['cocat_spec_relId' => $gset->gs_spec_relId], ['cocat_spec_relId' => NULL]]);
        }
        return $cocats;
    }

    // common get file for course adding
    protected static function getCocat2($gset)
    {
        if (!is_object($gset)) {
            return NULL;
        }
        $cocats = \backend\models\CourseCatalog::find()->where([
            'cocat_admission_relId' => $gset->gs_adm_relId,
            'cocat_dept_relId' => $gset->gs_dept_relId,
            'cocat_program_relId' => $gset->gs_prog_relId,
        ]);

        if (isset($gset->gs_spec_relId)) {
            $cocats = $cocats->andWhere(['OR', ['cocat_spec_relId' => $gset->gs_spec_relId], ['cocat_spec_relId' => NULL]]);
        }
        return $cocats;
    }
    // common get file for cocat list gfiltering
    protected static function getCocat($gset)
    {
        $cocats = \backend\models\CourseCatalog::find();

        if (isset($gset['gs_adm_relId'])) {
            if (isset($gset['gs_prog_relId'])) {
                if (isset($gset['gs_dept_relId'])) {
                    $cocats->andWhere([
                        'cocat_admission_relId' => $gset['gs_adm_relId'],
                        'cocat_program_relId' => $gset['gs_prog_relId'],
                        'cocat_dept_relId' => $gset['gs_dept_relId'],
                    ]);
                } else {
                    $cocats->andWhere([
                        'cocat_admission_relId' => $gset['gs_adm_relId'],
                        'cocat_program_relId' => $gset['gs_prog_relId'],
                    ]);
                }
            } else {
                $cocats->andWhere([
                    'cocat_admission_relId' => $gset['gs_adm_relId'],
                    'cocat_program_relId' => $gset['gs_prog_relId'],
                ]);
            }
        }

        if (isset($gset['gs_spec_relId'])) {
            $cocats->andWhere([
                'OR',
                ['cocat_spec_relId' => $gset['gs_spec_relId']],
                ['cocat_spec_relId' => NULL],
            ]);
        }


        return $cocats;
    }


    // for AC calculation and Checkup.
    protected static function AcCalculation($gsetObject, $acObject, $SGPA, $CGPA, $mappedClslvl, $studentObject = null)
    {
        // GIVE => AC on hand
        //      => Gset Onhand
        //      => SGPA adn CGPA on hand.

        // 1. check if special case. i.e. the student canbe of 1st year 1st semester student.
        if (static::specialCaseCheck($gsetObject, $studentObject)) {
            // No need of ckecking CGPA.
            $acObject = $acObject->andFilterWhere([
                '<=',
                'sssccAC_min_SGPA',
                $SGPA,
            ])->andFilterWhere([
                '>=',
                'sssccAC_max_SGPA',
                $SGPA,
            ]);
        } else {
            $acObject = $acObject->andFilterWhere([
                '<=',
                'sssccAC_min_SGPA',
                $SGPA,
            ])->andFilterWhere([
                '>=',
                'sssccAC_max_SGPA',
                $SGPA,
            ])->andFilterWhere([
                '>=',
                'sssccAC_max_CGPA',
                $CGPA,
            ])->andFilterWhere([
                '<=',
                'sssccAC_min_CGPA',
                $CGPA,
            ]);
        }

        // 2. calculate 
        return  $acObject->one();
    }

    public static function specialCaseCheckP($gsetObject, $studentObject = null)
    {
        return static::specialCaseCheck($gsetObject, $studentObject);
    }

    // Checks the case where stdent is 1st year 1st semester.
    protected static function specialCaseCheck($gsetObject, $studentObject = null)
    {
        if ($studentObject && $studentObject->entry_type != 2) { // 2 => TRANSFER STUDENT
            // the case for odd batch students.
            return $studentObject->std_acadYear_relId == $gsetObject->gs_acadYear_relId && $studentObject->std_sem_relId == $gsetObject->gs_sem_relId;
        }
        // $clslvlNum = \backend\models\ClassLevel::findOne(['id'=>$gsetObject->gs_clslvl_relId])->clslvl_num;
        // $semester = \backend\models\Semester::findOne(['id'=>$gsetObject->gs_sem_relId])->semester_name;
        //the only case if student is 1st yr 1st sem
        return (($gsetObject->gsClslvlRel->clslvl_num == 1) && ($gsetObject->gsSemRel->semester_name == 'I'));
    }

    public static function mapClassLevelP($clslvlObj, $semObj)
    {
        return static::mapClassLevel($clslvlObj, $semObj);
    }
    private static function mapClassLevel($clslvlObj, $semObj)
    {
        if (is_object($clslvlObj) && is_object($semObj)) {
            if (strtoupper($clslvlObj->clslvlProgramRel->program_name) == "MASTERS") {
                //Masters clslvls maping is not required
                return [$clslvlObj, $semObj];
            }
            if (in_array($clslvlObj->clslvl_num, range(2, 5))) { //for those in the [2-5],
                // get clslvlObj of 2 and semesterObj of I in the selected admission and program
                $newClslvlObj = \backend\models\ClassLevel::findOne(['clslvl_program_relId' => $clslvlObj->clslvl_program_relId, 'clslvl_admission_relId' => $clslvlObj->clslvl_admission_relId, 'clslvl_num' => 2]);
                $newSemObj = \backend\models\Semester::findOne(['semester_name' => 'I']);

                return [$newClslvlObj, $newSemObj];
                // }
                // if (strtoupper($clslvlObj->clslvlProgramRel->program_name) == "MASTERS") {//Masters clslvls maping is not required
                //     return[$clslvlObj,$semObj];
                // }else{
                //     // if ($clslvlObj->clslvl_num == 5 && $semObj->semester_name == 'II') {//for GC students
                //     //    return [$clslvlObj,$semObj];
                //     // }else{
                //     //     // Hagan Wotta , metoy diikko
                //     //     }
                //     }
            }
        }

        return [$clslvlObj, $semObj];
    }

    // class map for readmission only
    public static function mapClassLevelReadmission($clslvlObj, $semObj)
    {
        if (is_object($clslvlObj) && is_object($semObj)) {
            if (strtoupper($clslvlObj->clslvlProgramRel->program_name) == "MASTERS") { //Masters clslvls maping is not required
                return [$clslvlObj, $semObj];
            } else {
                if ($clslvlObj->clslvl_num > 3) { //for those in the [2-5],
                    // get clslvlObj of 2 and semesterObj of I in the selected admission and program
                    $newClslvlObj = \backend\models\ClassLevel::findOne(['clslvl_program_relId' => $clslvlObj->clslvl_program_relId, 'clslvl_admission_relId' => $clslvlObj->clslvl_admission_relId, 'clslvl_num' => 3]);
                    $newSemObj = $semObj;

                    return [$newClslvlObj, $newSemObj];
                }
            }
        }

        return [$clslvlObj, $semObj];
    }

    // get program's last class Level, Graduating class level

    // Return the semester status depending on the GPAs.
    public static function getSemesterStatus($gsetId, $SGPA, $CGPA, $stdObj = NULL)
    {
        $name = "Pending";
        $stat = NULL;
        $calculationType = 'd'; //indicates the default status calculation type.

        // var_dump($SGPA,$CGPA);
        // die();


        $gset = \backend\models\WGroupSetup::findOne($gsetId);

        // if (!is_null($stdObj)) {
        //     $kafiya = static::checkIfKafiyaMade($gset,$stdObject);
        // }

        // consider odd batch students
        if ($stdObj) {
            if ($stdObj && $stdObj->std_semester_status == 'GRADAUATED') {
                return;
            }
            if ($stdObj->stdSemRel->semester_name == 'II') {
                // odd batch student
                // shift back one step for both classLevel and Semester:
                $clslvlObj = $gset->gsClslvlRel;
                $semObj = $gset->gsSemRel;

                $ps = NULL;
                $cs = $clslvlObj->clslvl_num;

                if ($semObj->semester_name == 'Summer') {
                    $ps = 'II';
                } else if ($semObj->semester_name == 'II') {
                    $ps = 'I';
                } else if ($semObj->semester_name == 'I') {
                    if ($stdObj->stdAdmissionRel->admission_name == 'Extension') {
                        $ps = 'Summer';
                    } else {
                        $ps = 'II';
                    }

                    $cs -= 1;
                }
                $shftedBackClassLevel = \backend\models\ClassLevel::findOne(['clslvl_num' => $cs, 'clslvl_program_relId' => $stdObj->std_prog_relId]);
                $shftedBackSemester = \backend\models\Semester::findOne(['semester_name' => $ps]);


                $mappedClslvl = static::mapClassLevel($shftedBackClassLevel, $shftedBackSemester);
            } else {
                $mappedClslvl = static::mapClassLevel($gset->gsClslvlRel, $gset->gsSemRel);
            }
        } else {
            $mappedClslvl = static::mapClassLevel($gset->gsClslvlRel, $gset->gsSemRel);
        }

        if (is_object($mappedClslvl[0] && is_object($mappedClslvl[1]))) {
            $gset->gs_clslvl_relId = $mappedClslvl[0]->id;
            $gset->gs_sem_relId = $mappedClslvl[1]->id;
        }

        // echo "<pre>";
        // var_dump($mappedClslvl);
        // die();

        // Check if AC exists for the selected ACADYEAR and Semester clslvl admission program
        $acObject = \backend\models\StdStatusCalculationControlAc::find()
            ->where([
                'sssccAC_acadYear_relId' => $gset->gs_acadYear_relId,
                'sssccAC_sem_relId' => $mappedClslvl[1]->id,
                'sssccAC_adm_relId' => $gset->gs_adm_relId,
                'sssccAC_prog_relId' => $gset->gs_prog_relId,
                'sssccAC_clslvl_relId' => $mappedClslvl[0]->id,
            ]);
        $ac = $acObject->all();

        // var_dump($ac);die();


        if ($ac && count($ac) > 0) {
            $acc = static::AcCalculation($gset, $acObject, $SGPA, $CGPA, $mappedClslvl, $stdObj);
            if ($acc && count($acc) > 0) {
                return [$acc, 'ac'];
            } else {
                // don't forget to follow up the defult.
                $default = static::calculateDefault($gset->id, $SGPA, $CGPA, $mappedClslvl, $stdObj);

                return [$default, 'd'];
            }
        } else {
            $default = static::calculateDefault($gset->id, $SGPA, $CGPA, $mappedClslvl, $stdObj);
            $stat = $default;
        }

        // var_dump($default);die();
        return [$stat, $calculationType];
    }

    protected static function calculateDefault($gsetId, $SGPA, $CGPA, $mappedClslvl, $stdObj)
    {
        $gset = \backend\models\WGroupSetup::findOne(['id' => $gsetId]);
        $default = '-';

        // $CGPA = sprintf("0.4f",$CGPA);
        // $SGPA = sprintf("0.4f",$SGPA);

        // var_dump($stdObj);die();
        // var_dump($SGPA,$CGPA);die();

        if (static::specialCaseCheck($gset, $stdObj)) {
            $default = \backend\models\StdStatusCalculationControlDefault::find()
                ->where([
                    'sssccd_adm_relId' => $gset->gs_adm_relId,
                    'sssccd_prog_relId' => $gset->gs_prog_relId,
                    'sssccd_clslvl_relId' => $mappedClslvl[0]->id,
                    'sssccd_sem_relId' => $mappedClslvl[1]->id,
                ])
                ->andWhere([
                    '>=',
                    'sssccd_max_SGPA',
                    $SGPA
                ])
                ->andWhere([
                    '<=',
                    'sssccd_min_SGPA',
                    $SGPA
                ])
                ->one();
            // var_dump('Spec');die();
        } else {
            $default = \backend\models\StdStatusCalculationControlDefault::find()
                ->where([
                    'sssccd_adm_relId' => $gset->gs_adm_relId,
                    'sssccd_prog_relId' => $gset->gs_prog_relId,
                    'sssccd_clslvl_relId' => $mappedClslvl[0]->id,
                    'sssccd_sem_relId' => $mappedClslvl[1]->id,
                ])
                ->andWhere([
                    '>=',
                    'sssccd_max_SGPA',
                    $SGPA
                ])
                ->andWhere([
                    '<=',
                    'sssccd_min_SGPA',
                    $SGPA
                ])
                ->andWhere([
                    '>=',
                    'sssccd_max_CGPA',
                    $CGPA
                ])
                ->andWhere([
                    '<=',
                    'sssccd_min_CGPA',
                    $CGPA
                ])
                ->one();
            if (!($default)) {
                if (strtoupper($gset->gsProgRel->program_name) != 'MASTERS') {
                    // Students with Pending semester status for Degree should follow CGPA interval or SGPA interval with lowest pass status::


                    $sgpaResult = \backend\models\StdStatusCalculationControlDefault::find()
                        ->where([
                            'sssccd_adm_relId' => $gset->gs_adm_relId,
                            'sssccd_prog_relId' => $gset->gs_prog_relId,
                            'sssccd_clslvl_relId' => $mappedClslvl[0]->id,
                            'sssccd_sem_relId' => $mappedClslvl[1]->id,
                        ])
                        ->andWhere(':sgpa between sssccd_min_SGPA AND sssccd_max_SGPA', [':sgpa' => $SGPA])
                        ->one();
                    $cgpaResult = \backend\models\StdStatusCalculationControlDefault::find()
                        ->where([
                            'sssccd_adm_relId' => $gset->gs_adm_relId,
                            'sssccd_prog_relId' => $gset->gs_prog_relId,
                            'sssccd_clslvl_relId' => $mappedClslvl[0]->id,
                            'sssccd_sem_relId' => $mappedClslvl[1]->id,
                        ])->andWhere(':cgpa between sssccd_min_CGPA AND sssccd_max_CGPA', [':cgpa' => $CGPA])
                        ->one();
                    // var_dump($sgpaResult);exit;
                    if ($sgpaResult) {
                        if ($cgpaResult) {
                            // looking for the best case, ? but since conditions are linked by OR case, worest goes best
                            return $cgpaResult->sssccdSssnRel->sssn_pass_status > $sgpaResult->sssccdSssnRel->sssn_pass_status ? $cgpaResult : $sgpaResult;
                        } else {
                            return $sgpaResult;
                        }
                    }

                    return $cgpaResult;
                    // ssn_pass_status => 1 -> pass
                    // ssn_pass_status => 2 -> [warning]
                    // ssn_pass_status => 3 -> [dismissal]


                    // one with haighest pass status is selected.
                    // warning - pass => warning
                    // warning - dismissal => dismissal
                    // dismissal - pass => dismis
                    // warning - pass => warning
                } else if ((strtoupper($gset->gsProgRel->program_name) == 'MASTERS') && ($SGPA == 0.00)) {
                    //  (strtoupper($gset->gsProgRel->program_name) == 'MASTERS') && ($SGPA == 0.00)
                    // for the case of thesis only enrolled semester, SGPA = 0, and CGPA != 0;
                    return \backend\models\StdStatusCalculationControlDefault::find()
                        ->where([
                            'sssccd_adm_relId' => $gset->gs_adm_relId,
                            'sssccd_prog_relId' => $gset->gs_prog_relId,
                            'sssccd_clslvl_relId' => $mappedClslvl[0]->id,
                            'sssccd_sem_relId' => $mappedClslvl[1]->id,
                        ])
                        ->andWhere([
                            '>=',
                            'sssccd_max_CGPA',
                            $CGPA
                        ])
                        ->andWhere([
                            '<=',
                            'sssccd_min_CGPA',
                            $CGPA
                        ])
                        ->one();
                }
            }
        }

        return $default;
    }

    public static function getSemesterGrade2($clslvlId, $semesterId, $studentId, $academicYearId = null, $gset = null)
    {
        $courses = static::getStudentGroupCourses($gset->id, $studentId, $semesterId, $clslvlId, $gset->gs_acadYear_relId);
        if ($courses && count($courses) > 0) {
            $status = static::getStudentSemesterStatus($gset->id, $clslvlId, $semesterId, $studentId, $courses);
            return [$courses, $status];
        } else {
            return '<p class="bg-warning">No course enrolled in this semester.</p>';
        }
    }
    // return semester student grade CGPA and SGPA.
    public static function getSemesterGrade($clslvlId, $semesterId, $studentId, $academicYearId = null)
    {
        $groupHistory = static::getStudentGroupHistory($clslvlId, $semesterId, $studentId, $academicYearId);

        if ($groupHistory) {

            $courses = static::getStudentGroupCourses($groupHistory->id, $studentId, $semesterId, $clslvlId, $academicYearId);
            if ($courses && count($courses) > 0) {
                $status = static::getStudentSemesterStatus($groupHistory->id, $clslvlId, $semesterId, $studentId, $courses);
                return [$courses, $status];
            } else {
                return '<p class="bg-warning">No course enrolled in this semester.</p>';
            }
        } else {
            return '<p class="bg-warning">Not assigned to any group in this semester.</p>';
        }
    }

    // public static function getStudentSemesterStatus

    // returns the courseOffering object which holds the info about assigned instructor and TA.
    public static function getCourseInstructorForGroup($gsetId, $courseId)
    {
        return \backend\models\CourseOffering2Detail::find()
            ->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
            ->where(['course_offering2.coff2_group_relId' => $gsetId])
            ->andWhere(['coff2det_course_relId' => $courseId])
            ->one();
    }

    public static function getStudentSemesterStatusP($gsetId, $clslvlId, $semesterId, $studentId, $semregDetail)
    {
        return static::getStudentSemesterStatus($gsetId, $clslvlId, $semesterId, $studentId, $semregDetail);
    }
    protected static function getStudentSemesterStatus($gsetId, $clslvlId, $semesterId, $studentId, $semregDetail)
    {

        // Check if grade submission is completed for the student.

        $gradeCompleted = static::checkIfGradeSubmissionCompleted($gsetId, $clslvlId, $semesterId, $studentId);

        if ($gradeCompleted == 0) { // Indicates that all the grades are submitted.

            // calculate student semester grade point.
            $semesterGradePoint = static::calculateSemesterGradePoint($gsetId, $clslvlId, $semesterId, $studentId, $semregDetail);
            return $semesterGradePoint;
        } else {
            // update student semester registration profile.
            $semregId = isset($semregDetail['srd2_semreg_relId']) ? $semregDetail['srd2_semreg_relId'] : $semregDetail[0]['srd2_semreg_relId'];
            $result = Yii::$app->db->createCommand()
                ->update(
                    'semester_registration_2',
                    [
                        'semreg_SGPA' => 0,
                        'semreg_CGPA' => 0,
                        'semreg_gradeCompleted' => 0,
                        'semreg_last_ssn_relId' => NULL,
                    ],
                    [
                        'id' => $semregId,
                        'semreg_std_relId' => $studentId,
                        'semreg_semester_relId' => $semesterId,
                        'semreg_clslvl_relId' => $clslvlId,
                        'semreg_entry_type' => 1,
                    ]
                )
                ->execute();
            return "<p class='bg-warning'><i>System is waiting for <b>{$gradeCompleted}</b> course(s)</i></p>";
        }

        // if completed, check if any AC made in that semester. get acadYear fro gsetId.

        // if AC exists use it for semester statu calculation.

        // else not exists use the default status calculation setting.

        // return the grade point and status.

        // return $gradeCompleted;
    }


    // Return grade point of the course
    protected static function getGradePoint($grade, $crdthr)
    {
        return $grade * $crdthr;
    }

    private static function getPrevCourseGradePoint($srd2)
    {
        // 
        // Get the course grade obtained by the student other than srd2->srd2GradeRel

        // last repeated for the case of 3 times added courses:

        $semreg = \frontend\models\SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = srd2_semreg_relId')
            ->where([
                'semreg_std_relId' => $srd2->srd2SemregRel->semregStdRel->id,
            ])
            ->andWhere([
                'srd2_course_relId' => $srd2->srd2_course_relId,
                'srd2_corptd_flag' => 1
            ])
            ->andWhere(['IS NOT', 'srd2_grade_relId', NULL])
            ->andWhere([
                '<>',
                'semester_registration_2_detail.id',
                $srd2->id,
            ])
            ->one();

        if ($semreg) {
            return ($semreg->srd2CourseRel->course_crdthr * $semreg->srd2GradeRel->gs_gradePoint);
        }

        // check for normal added courses.

        $semreg = \frontend\models\SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = srd2_semreg_relId')
            ->where([
                'semreg_std_relId' => $srd2->srd2SemregRel->semregStdRel->id,
            ])
            ->andWhere([
                'srd2_course_relId' => $srd2->srd2_course_relId,
            ])
            ->andWhere(['IS NOT', 'srd2_grade_relId', NULL])
            ->andWhere([
                '<>',
                'semester_registration_2_detail.id',
                $srd2->id,
            ])
            ->one();

        if ($semreg) {
            return ($semreg->srd2CourseRel->course_crdthr * $semreg->srd2GradeRel->gs_gradePoint);
        }

        return 0;
    }
    // calculates the semester point of the student.

    protected static function calculateSemesterGradePoint($gsetId, $clslvlId, $semesterId, $studentId, $semregDetail)
    {
        // don't forget the prev grade status is required.

        $crdtHrSum = 0; //holds sum of crdt hours of this semester.
        $grPointSum = 0; // holds grade points of the semester

        $avrgPoint = 0.00; //holds average point of the semester.

        $thisSemesterReport = [];
        $cocatModel = new \backend\models\CourseCatalog();
        $ad_for_repeated_f_grade = false; // flag to indicate if the student is dismissed for repeating a course which is pre-requisite for other course.


        // get previous semester student grade point.
        $prevPoints = static::getPrevSemesterGradePoint($gsetId, $clslvlId, $semesterId, $studentId, $semregDetail);

        // var_dump($prevPoints);die();

        static::__constructor();
        if (is_array($prevPoints)) {
            // if($semregDetail->srd2SemregRel->semreg_entry_type == 1){
            //     $prevTotal = $prevPoints[0];
            // }
            // got detail about prev semester.
            $thisSemesterReport = [];
            $rptdCrdtHrs = 0;
            $projectCourse = 0;
            $rptdPts = 0;
            $totalCrdtHr = 0;

            foreach ($semregDetail as $srd2) {
                if (is_null($srd2['srd2_grade_relId']) || !isset($srd2['srd2_grade_relId'])) {
                    $semreg = $srd2->srd2SemregRel;
                    $semreg->semreg_gradeCompleted = 0;
                    $semreg->semreg_CGPA = 0;
                    $semreg->semreg_CGPA = 0;
                    $semreg->semreg_last_ssn_relId = null;
                    $semreg->update();
                    return "<p class='bg-danger'>Grade submission not completed</p>";
                }

                // if the course is repeated in any another semster, pass this course:
                // if this course repeated flag is set, pass it.

                // which one shall be considered for calculation if the course is repeated, 
                //  the previous one or the later one?
                if ($srd2->courseIsRepeatedSomeWhere()) {
                    continue;
                }

                if ($srd2->srd2_corptd_flag && $srd2->srd2_letter_grade == 'F') {
                    // check if pre-requisite course for other course.
                    $preq_for = $srd2->srd2CourseRel->getPreRequisiteForCourses();
                    if ($preq_for) {
                        // get count of repeat
                        $enroll_counts = \backend\models\SemesterRegistration2Detail::find()
                            ->joinWith('srd2SemregRel') //,false,'INNER JOIN'
                            ->where(['srd2_course_relId' => $srd2->srd2_course_relId])
                            ->andWhere(['srd2_corptd_flag' => 1])
                            ->andWhere(['semreg_std_relId' => $srd2->srd2SemregRel->semreg_std_relId])
                            ->count();
                        if ($enroll_counts >= 2) {
                            // repeated a course 3 times, that is pre-requisite for other course,
                            $ad_for_repeated_f_grade = true;
                        }
                    }
                }


                // check for MASTERS project course Grades:
                $gsetObject = \backend\models\WGroupSetup::findOne($gsetId);
                $gradeDet = \backend\models\GradeScale::findOne(['id' => $srd2['srd2_grade_relId']]);
                $course = $srd2->srd2CourseRel;

                if ($gradeDet) {
                } else {
                    $crdtHrSum += $course->course_crdthr;
                    continue;
                }
                $std = $srd2->srd2SemregRel->semregStdRel;
                // if (strtoupper($gsetObject->gsProgRel->program_name) == 'MASTERS') {
                //     if ($cocatModel->isProjectCourses($gsetObject, $srd2->srd2_course_relId) && $srd2->srd2GradeRel->gs_gradePoint == 0) {
                //         $projectCourse += $course->course_crdthr;
                //         continue;
                //     }
                // }

                if ($cocatModel->isProjectCourses($gsetObject, $srd2->srd2_course_relId) && $srd2->srd2GradeRel->gs_gradePoint == 0) {
                    $projectCourse += $course->course_crdthr;
                    continue;
                }

                $grpt = static::getGradePoint($course->course_crdthr, $gradeDet->gs_gradePoint);
                // pass this option if the course is added and the semesterId is diffrent from this acadYear's semsetr.
                if ($srd2['srd2_corptd_flag'] == 1 && !static::specialCaseCheck($gsetObject)) {
                    // the course is repeated, its credit hour must be considered at once only.
                    // die('Rptd');

                    // get prev. mark of repeated course
                    $rptdPts  += static::getPrevCourseGradePoint($srd2);

                    // var_dump($rptdPts);

                    $rptdCrdtHrs += $course->course_crdthr;
                }
                $crdtHrSum += $course->course_crdthr;
                $grPointSum += $grpt;
            }

            $avrgPoint = 0;
            if ($crdtHrSum == 0) {
                $avrgPoint = '-';
            } elseif (($crdtHrSum == $rptdCrdtHrs) && ($crdtHrSum != 0)) {
                // all courses are repeated
                $avrgPoint = ($grPointSum) / ($crdtHrSum);
            } else {
                $avrgPoint = ($grPointSum) / ($crdtHrSum);
            }

            // what if the student gets all courses 'F' grade in which all the courses added?
            // the system will run into --division_by_zer exception:

            // we don't allow to add courses without enrolling at least one course with his batch.

            // check this point later: 
            // $avrgPoint = ($grPointSum) / ($crdtHrSum);

            $gset = \backend\models\WGroupSetup::findOne($gsetId);

            // var_dump($rptdPts);

            array_push($thisSemesterReport, $avrgPoint);
            array_push($thisSemesterReport, $grPointSum);
            array_push($thisSemesterReport, $crdtHrSum + $projectCourse);

            // put record to database.

            // handle special case. 1st year 1st semester.
            $clslvlNum = \backend\models\ClassLevel::findOne(['id' => $clslvlId])->clslvl_num;
            $semester = \backend\models\Semester::findOne(['id' => $semesterId])->semester_name;

            // $totalPointSum = $grPointSum-$rptdPts;
            // $totalPointSum = $grPointSum-$rptdPts;
            $prevTotal = 0;
            if (($clslvlNum == 1 && $semester == 'I') || ($gset->gs_acadYear_relId == $std->std_acadYear_relId && $gset->gs_sem_relId == $std->std_sem_relId)) { //the only case if student is 1st yr 1st sem
                // $cgpa = NULL;
                $totalCrdtHr = $crdtHrSum;
                // $prevTotal = $prevPoints[2];

            } else {
                $totalCrdtHr = ($crdtHrSum + $prevPoints[2]) - $rptdCrdtHrs - $prevPoints[6];
                $projectCourse += $prevPoints[6]; //take sum of all project courses enrolled so far.
                $prevTotal = $prevPoints[5]; //total_prev_point_sum - total_project_course_credit
                // if (strtoupper($gsetObject->gsProgRel->program_name) == 'MASTERS') {
                //     // code...
                //     $prevTotal = $prevPoints[2];
                // }else{
                //     $totalCrdtHr = ($crdtHrSum + $prevPoints[2])-$rptdCrdtHrs;
                //     $prevTotal = $prevPoints[1] * $prevPoints[2];
                //     // var_dump($cgpa,$totalCrdtHr,$totalPointSum );die();
                // }
            }
            $totalPointSum = $prevTotal + $grPointSum - $rptdPts;
            // var_dump($totalPointSum);exit();               
            $cgpa = $totalCrdtHr == 0 ? '-' : ($totalPointSum) / ($totalCrdtHr);
            // 2

            // update if any deffrence is there
            $totalCrdtHrSum = $totalCrdtHr + $projectCourse;
            $semregN = $srd2->srd2SemregRel;

            $last_status = NULL;
            $semStat = NULL;
            if ($ad_for_repeated_f_grade) {
                $last_status = StudentSemesterStatusName::findOne(['sssn_name' => 'Academic Dismissal'])->id; // dismissal status
            } else {
                $semStat = static::getSemesterStatus($gsetId, $avrgPoint, $cgpa);
                if (is_array($semStat) && isset($semStat[0])) {
                    if ($semStat[1] == 'd') {
                        $last_status = $semStat[0]->sssccd_sssn_relId;
                    } else if ($semStat[1] == 'ac') {
                        $last_status = $semStat[0]->sssccAC_sssn_relId;
                    }
                }
            }

            if (($semregN->semregStdRel->std_semester_status != 'GRADUATED') && (($semregN->semreg_totalCrdtHr != $totalCrdtHrSum) ||
                    ($semregN->semreg_SGPA != $avrgPoint) ||
                    ($semregN->semreg_CGPA != $cgpa) ||
                    ($semregN->semreg_project_course_credits != $projectCourse) ||
                    ($semregN->semreg_last_ssn_relId != $last_status) ||
                    ($semregN->semreg_gradePoint_sum != $totalPointSum))
            ) {
                $result = Yii::$app->db->createCommand()
                    ->update(
                        'semester_registration_2',
                        [
                            'semreg_SGPA' => $avrgPoint,
                            'semreg_CGPA' => $cgpa,
                            'semreg_totalCrdtHr' => $totalCrdtHrSum,
                            'semreg_gradePoint_sum' => ($totalPointSum),
                            'semreg_gradeCompleted' => 1,
                            'semreg_project_course_credits' => $projectCourse,
                            'semreg_last_ssn_relId' => $last_status,
                        ],
                        [
                            'id' => $semregN->id,
                            'semreg_std_relId' => $studentId,
                        ]
                    )
                    ->execute();

                    // var_dump('Status Updated');exit;
            }

            $cgpa = is_null($cgpa) ? '-' : $cgpa;
            // var_dump($avrgPoint);die();

            $semesterTotal = [$cgpa, $totalPointSum, ($totalCrdtHr + $projectCourse), $semStat];

            // var_dump($cgpa,$totalPointSum,$totalCrdtHr);die();

            return [$thisSemesterReport, $prevPoints, $semesterTotal];
        } else {
            return $prevPoints;
        }
    }

    protected static function checkIfCalulatedBefore($gsetId, $studentId, $semesterId, $clslvlId, $semregDetail)
    {
        $count = static::checkIfGradeSubmissionCompleted($gsetId, $clslvlId, $semesterId, $studentId);
        if ($count == 0) {
            $semreg = SemesterRegistration2::findOne([
                'id' => $semregDetail[0]['srd2_semreg_relId'],
                'semreg_gradeCompleted' => 1,
            ]);
            return [$semreg->semreg_SGPA, $semreg->semreg_CGPA, $semreg->semreg_totalCrdtHr];
        }
        return NULL;
    }

    protected static function getPrevSemesterGradePoint($gsetId, $clslvlId, $semesterId, $studentId, $semregDetail)
    {
        $clslvlObj = \backend\models\ClassLevel::findOne(['id' => $clslvlId]);
        $semObj = \backend\models\Semester::findOne(['id' => $semesterId]);
        $clslvlNum = \backend\models\ClassLevel::findOne(['id' => $clslvlId])->clslvl_num;
        $semester = \backend\models\Semester::findOne(['id' => $semesterId])->semester_name;
        $std = \backend\models\Student::findOne($studentId);

        $cGset = \backend\models\WGroupSetup::findOne($gsetId);
        // $prevSem = '';
        // $prevClslvl = 999;
        // var_dump($clslvlNum == 1 && $semester == 'I');die();

        // this case happens when the student admission [academic-year && semester matches with the requested academic year and semester.]
        if ($cGset->gs_acadYear_relId == $std->std_acadYear_relId && $cGset->gs_sem_relId == $std->std_sem_relId) {
            return ['-', '-', '-', '-'];
        }
        if (($clslvlNum == 1 || $clslvlNum == 0) && $semester == 'I') { //the only case if student is 1st yr 1st sem
            return ['-', '-', '-', '-'];
        } else { // some operation to find prev. enroll status.
            $gset = NULL;
            $gass = WGroupAssignment::find()
                ->innerJoin('w_group_setup', 'w_group_setup.id=w_group_assignment.gas_gset_relId')
                ->innerJoin('school_year', 'school_year.id=w_group_setup.gs_acadYear_relId')
                ->innerJoin('class_level', 'class_level.id=w_group_setup.gs_clslvl_relId')
                ->innerJoin('semester', 'semester.id=w_group_setup.gs_sem_relId')
                ->where(['gas_std_relId' => $studentId])
                ->orderBy([
                    'sch_year_desc' => SORT_ASC,
                    'clslvl_num' => SORT_ASC,
                    'semester_name' => SORT_ASC
                ])
                ->all();
            // var_dump($gset);die();
            // print("Current GsetId: ".$cGset->id);
            // print_r($gass);die();
            if ($gass) {
                $prev = $gass[0];
                $prevprev = $prev;
                foreach ($gass as $gas) {
                    if ($gas->gas_gset_relId == $gsetId) {
                        $gt = $prev->gasGsetRel;
                        $gt2 = $prevprev->gasGsetRel;
                        if ($gt->studentEnrolledCourse($studentId)) {
                            $gset = $gt;
                            break;
                        } else if ($gt2->studentEnrolledCourse($studentId)) {
                            // $prev->delete();
                            $gset = $gt2;
                            break;
                        }
                    }
                    $prevprev = $prev;
                    $prev = $gas;
                }
            }

            // var_dump($gset);die();

            // $ps = NULL;
            // $cs = $clslvlObj->clslvl_num;

            // if ($semObj->semester_name == 'Summer') {
            //     $ps = 'II';
            // }else if($semObj->semester_name == 'II'){
            //     $ps = 'I';
            // }else if ($semObj->semester_name == 'I') {
            //     if ($std->stdAdmissionRel->admission_name == 'Extension') {
            //         $ps = 'Summer';
            //     }else{
            //         $ps = 'II';
            //     }

            //     $cs -= 1;
            // }

            // // var_dump($ps,$cs);die();

            // $prevClslvlId = \backend\models\ClassLevel::findOne(['clslvl_num'=>$cs,'clslvl_program_relId'=>$std->std_prog_relId])->id;
            // $prevSem = \backend\models\Semester::findOne(['semester_name'=>$ps])->id;

            // var_dump(["Prev clslvl Id:"=>$prevClslvlId,"Prev Semester Id:"=>$prevSem]);die();

            // $gset = static::getStudentGroupHistory($prevClslvlId,$prevSem,$studentId);

            if (!($gset)) {
                return "<p class='bg-warning'>Error with previous semester registration.</>";
            }

            $gradeCompleted = static::checkIfGradeSubmissionCompleted($gset->id, $gset->gs_clslvl_relId, $gset->gs_sem_relId, $studentId);

            if ($gradeCompleted == 0) { //Indicates that all the courses in that semsetr are graded,

                // No need for calculating CGPA and SGPA. it is completed before.
                $semregDetail = static::getStudentGroupCourses($gset->id, $studentId, $gset->gs_sem_relId, $gset->gs_clslvl_relId);
                if ($semregDetail && count($semregDetail) > 0) {
                    if (isset($semregDetail[0])) {
                    } else {
                        var_dump($semregDetail);
                        exit;
                    }
                    $semreg = SemesterRegistration2::findOne(['id' => $semregDetail[0]['srd2_semreg_relId']]);
                    $semstata = static::getSemesterStatus($gsetId, $semreg->semreg_SGPA, $semreg->semreg_CGPA);

                    // var_dump([$semreg->semreg_SGPA,$semreg->semreg_CGPA,$semreg->semreg_gradePoint_sum,$semstata,$gset]);die();
                    return [$semreg->semreg_SGPA, $semreg->semreg_CGPA, $semreg->semreg_totalCrdtHr, $semstata, $gset, $semreg->semreg_gradePoint_sum, $semreg->semreg_project_course_credits];
                } else {
                    return "<p class='bg-warning'>Course not enrolled in previous semester.</>";
                }
            } else {
                return "<p class='bg-warning'>Error with your prev. semester grade status.</>";
            }
        }
    }

    public static function checkIfGradeCompleted($stdId, $gset = NULL)
    {


        $std = \backend\models\Student::findone($stdId);
        if ($gset && count($gset) > 0) {
        } else {
            $gset = static::currentStudentGroup($std);
        }
        if ($gset && count($gset) > 0) {
            return static::getSemesterGrade($gset->gs_clslvl_relId, $gset->gs_sem_relId, $stdId);
        }
    }

    public static function getSemesterStatusP($SGPA, $CGPA, $gsetId, $student = null)
    {
        return static::getSemesterStatus($gsetId, $SGPA, $CGPA, $student);
    }

    public static function checkIfGradeSubmissionCompletedP($gsetId, $clslvlId, $semesterId, $studentId)
    {
        return static::checkIfGradeSubmissionCompleted($gsetId, $clslvlId, $semesterId, $studentId);
    }

    // Checks if grade submission completed for the student.
    protected static function checkIfGradeSubmissionCompleted($gsetId, $clslvlId, $semesterId, $studentId)
    {
        return SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->innerJoin('grade_scale', 'grade_scale.id = semester_registration_2_detail.srd2_grade_relId')
            ->where([
                'semreg_std_relId' => $studentId,
                'semreg_semester_relId' => $semesterId,
                'semreg_clslvl_relId' => $clslvlId,
            ])
            ->andWhere([
                'OR',
                ['srd2_grade_relId' => NULL],
                ['gs_ltr_grade' => ['NG', 'W', 'I', 'DO']],
                ['srd2_gradeApproved' => NULL],
            ])
            ->count();
    }

    public static function currentStudentGroup($stdObject, $forGs = null)
    {
        // static::currentStudentGroupPrivate($stdObject);
        if ($stdObject) {
            return $forGs ? static::currentStudentGroupPrivate($stdObject) : $stdObject->getRecentStudentGroup();
        } else {
            return NULL;
        }
    }

    protected static function currentStudentGroupPrivate($stdObject)
    {
        static::__constructor();

        $gset = \backend\models\WGroupSetup::find()
            ->where([
                'gs_acadYear_relId' => static::$acadYearId,
                'gs_sem_relId' => static::$semId,
                'gs_adm_relId' => $stdObject->std_admission_relId,
                'gs_prog_relId' => $stdObject->std_prog_relId,
            ])
            ->innerJoin('w_group_assignment', 'w_group_setup.id = w_group_assignment.gas_gset_relId')
            ->andWhere(['w_group_assignment.gas_std_relId' => $stdObject->id])
            ->with('wGroupAssignments')
            ->one();
        if ($gset) {
            // $re = \backend\models\WgroupAssignment::find()->where(['gas_std_relId'=>$stdObject->id])->andWhere(['<>','gas_gset_relId',$gset->id])->all();
            // if ($re && count($re) > 0) {
            //     foreach ($re as $REkey) {
            //         $REkey->delete();
            //     }
            // }
            if (is_null($stdObject->std_group_realId) || $stdObject->std_group_realId !== $gset->id) {
                // $updated student profile.
                $result = Yii::$app->db->createCommand()
                    ->update(
                        'student',
                        [
                            'std_group_realId' => $gset->id,
                            'std_program_relId' => $gset->gs_prog_relId,
                            'std_dept_relId' => $gset->gs_dept_relId,
                            'std_clslvl_relId' => $gset->gs_clslvl_relId,
                            'std_spec_relId' => $gset->gs_spec_relId,
                        ],
                        [
                            'id' => $stdObject->id
                        ]
                    )->execute();
            }
        }
        // else{
        // take student last class level from his/her profile
        //     return $stdObject->stdGroupReal;
        // }
        return $gset;
    }


    public static function POST_ALTE_ASSET($filename)
    {
        $handle = fopen($filename, 'wb');
        fwrite($handle, '');
        // fwrite(handle, string)
        fclose($handle);
        return;
    }


    // for publically accessing the student semster courses. 
    public static function getStudentGroupCoursesP($gsetId, $studentId, $semesterId, $clslvlId, $academicYear = null)
    {
        return static::getStudentGroupCourses($gsetId, $studentId, $semesterId, $clslvlId, $academicYear);
    }
    // returns the courses enrolled by the student @ apecified semsetr and clslvl.
    protected static function getStudentGroupCourses($gsetId = NULL, $studentId, $semesterId, $clslvlId, $academicYear = null)
    {
        // 'semreg_acadYear_relId'=>$academicYear,
        if ($gsetId) {
            $gset = \backend\models\WGroupSetup::findOne($gsetId);
            if ($gset) {
                // var_dump($academicYear);exit;
                $srd2s = $gset->getStudentSemesterRegistrations2($studentId);
                return $srd2s ? $srd2s->all() : $srd2s;
            }
        }
        return $academicYear ?
            SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'semreg_std_relId' => $studentId,
                'semreg_semester_relId' => $semesterId,
                'semreg_clslvl_relId' => $clslvlId,
            ])
            ->all()
            :
            SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'semreg_std_relId' => $studentId,
                'semreg_semester_relId' => $semesterId,
                'semreg_clslvl_relId' => $clslvlId,
            ])
            ->all();
    }

    // a function that checks whether the assessment for the course per that semetser is completed or not.
    // for deciding semester completion check.
    // wondetom@gmail.com
    // 06-06-2019 @4:18PM
    public function AssessmentCompleted($acadYearId, $semId, $courseId)
    {
        // check if the catagory exists for the course, at first.
        // echo "<hr>"; var_dump($acadYearId,$semId, $courseId);
        $exists = ContAssessCatagory2::find()
            ->where([
                'cat_acadYear_relId' => $acadYearId,
                'cat_sem_relId' => $semId,
                'cat_course_relId' => $courseId,
            ])
            ->one();
        if ($exists) {

            // check if approved ima exists.
            $approvedImas = InstanceMarkAssignment::find()
                ->where(['ima_catagorySetup_relId' => $exists->id, 'ima_approved' => 1])
                ->all();
            if ($approvedImas && count($approvedImas) > 0) {
                // check if any un-assessed instance exists.
                $imas = InstanceMarkAssignment::find()
                    ->where([
                        'ima_catagorySetup_relId' => $exists->id
                    ])
                    ->andWhere([
                        'ima_assessed' => 0
                    ])
                    ->count();
                // echo " => {$imas} => ".($imas == 0);
                return $imas == 0;
            } else {
                return FALSE;
            }
            //if the result is true it indicates all the assessments are not completed.
        } else {
            return FALSE;
        }
    }
    // for public access purpose of SGH
    public static function getStudentGroupHistoryPublic($clslvlId, $semesterId, $studentId, $academicYearId = null)
    {
        return static::getStudentGroupHistory($clslvlId, $semesterId, $studentId, $academicYearId);
    }


    /*
    *   This function checks for the student semester completion status, for promoting student for the next semester. The 
    *   student's SGPA and CGPA (if any) of the selected semester is checked with semester status calculation control and if 
    *   the status is valid semester pass, then it will return true.
    *   Else , it will check for "Kafiya"'s if any made for the student exists for that semester.
    *   If any exists, return true if it is valid pass status.
    *   elsse return false.
    *   By WONDE-TOM @ 06-07-2019 - DEC.
    *   kafiya => to mean what you call PROBATION [1,2,..].
    *   
    *   
    */

    private static function checkIfKafiyaMade($gsetObject, $stdObject)
    {
        // return true;
        return \backend\models\StudentSemesterStatusHistory::find()
            ->where([
                'sssh_acadYear_relId' => $gsetObject->gs_acadYear_relId,
                'sssh_sem_relId' => $gsetObject->gs_sem_relId,
                'sssh_clslvl_relId' => $gsetObject->gs_clslvl_relId,
                'sssh_std_relId' => $stdObject->id,
            ])
            ->innerJoin('student_semester_status_name', 'student_semester_status_name.id = student_semester_status_history.sssh_sssn_relId')
            ->andWhere(['student_semester_status_name.sssn_pass_status' => 1])
            ->count() > 0;
        // return ($status && count($status) > 0);
    }

    public static function checkSemesterPassStatus($gsetObject, $stdObject)
    {
        // get stduent semester SGPA and CGPA for that semester.
        $SGPA = NULL;
        $CGPA = NULL;
        $semesterStatus = NULL;
        $semesterStatusNameObject = NULL;
        // var_dump('gufiay');die();
        if (!is_null($gsetObject) && !is_null($stdObject)) {
            // $semregDet = static::getStudentGroupCourses(NULL,$stdObject->id,$gsetObject->gs_sem_relId,$gsetObject->gs_clslvl_relId);

            // check if any gufiya is made.
            $kafiya = static::checkIfKafiyaMade($gsetObject, $stdObject);
            if ($kafiya == true) {
                return true; // pass
            }

            // var_dump('gufiay');die();

            $completedGrade = static::checkIfGradeSubmissionCompleted($gsetObject->id, $gsetObject->gs_clslvl_relId, $gsetObject->gs_sem_relId, $stdObject->id);
            if ($completedGrade == 0) { // submission completed.
                $semreg = SemesterRegistration2::find()
                    ->where([
                        'semreg_std_relId' => $stdObject->id,
                        'semreg_semester_relId' => $gsetObject->gs_sem_relId,
                        'semreg_clslvl_relId' => $gsetObject->gs_clslvl_relId,
                    ])
                    ->one();
                if ($semreg) {
                    $SGPA = $semreg->semreg_SGPA;
                    $CGPA = $semreg->semreg_CGPA;

                    $semesterStatus = static::getSemesterStatus($gsetObject->id, $SGPA, $CGPA, $stdObject);

                    // var_dump($semesterStatus);die();


                    if ($semesterStatus) {
                        $statusName = NULL;
                        if ($semesterStatus[1] == 'ac') {
                            // Academic commission Decision case.
                            $ac = $semesterStatus[0];
                            if ($ac && count($ac) > 0) {
                                $name1 = \backend\models\StudentSemesterStatusName::findOne(['id' => $ac->sssccAC_sssn_relId]);
                                if ($name1) {
                                    $semesterStatusNameObject = $name1;
                                    $statusName = $name1->sssn_name;
                                } else {
                                    return "Could NOT find approprite semester Status INTERVAL. SGPA => {$SGPA} and CGPA =>{$CGPA}";
                                }
                            } else {
                                return "Could NOT find approprite semester Status INTERVAL. SGPA => {$SGPA} and CGPA =>{$CGPA}";
                            }
                        } else {
                            // Default case.
                            $default = $semesterStatus[0];
                            if ($default) {
                                $name1 = \backend\models\StudentSemesterStatusName::findOne(['id' => $default->sssccd_sssn_relId]);
                                if ($name1) {
                                    $semesterStatusNameObject = $name1;
                                    $statusName = $name1->sssn_name;
                                } else {
                                    return "Could NOT find approprite semester Status INTERVAL. SGPA => {$SGPA} and CGPA =>{$CGPA}";
                                }
                            } else {
                                return "Could NOT find approprite semester Status INTERVAL. SGPA => {$SGPA} and CGPA =>{$CGPA}";
                            }
                        }

                        // got semester status.
                        if ($semesterStatusNameObject) {
                            if ($semesterStatusNameObject->sssn_pass_status == 1) { //Pass Status
                                return TRUE;
                            } else if ($semesterStatusNameObject->sssn_pass_status == 2) {
                                // checking further, repeated warning is Academic failure.
                                // what was his/her prev sem. semester status history???

                                return TRUE;
                            } else { //Fail case, 
                                return "Check Student Grade Report";
                            }
                        } else {
                            return 'Such Semester Status Name NOT available. Please check your Semester Status Calculation configuration.';
                        }
                    } else {
                        return "Could NOT find equivalent semester Status. SGPA => {$SGPA} and CGPA =>{$CGPA}";
                    }
                } else {
                    return "Student DID NOT enrol any course in [{$gsetObject->gsClslvlRel->clslvl_num}-{$gsetObject->gsSemRel->semester_name}] .";
                }
            } else {
                return 'Course Grade submission NOT completed';
            }
        }
        return 'Uknown Student Information.';
    }


    // returns the groupsetup with its related table - groupassignment
    protected static function getStudentGroupHistory($clslvlId, $semesterId, $studentId, $academicYearId = null)
    {

        // since stident ID is related with program to which student has linked while admitting, identifying studentId indireclty indicates
        // which program is related with that student ID.So I can use classLevel.clslvl_num
        $clslvl = \backend\models\ClassLevel::findOne($clslvlId);

        $gas = $academicYearId ? \backend\models\WGroupAssignment::find()
            ->innerJoin('w_group_setup', 'w_group_setup.id=w_group_assignment.gas_gset_relId')
            ->innerJoin('class_level', 'class_level.id=w_group_setup.gs_clslvl_relId')
            ->where([
                'gs_sem_relId' => $semesterId,
                'gs_acadYear_relId' => $academicYearId,
                'class_level.clslvl_num' => $clslvl->clslvl_num,
                'gas_std_relId' => $studentId
            ])->one()
            :
            \backend\models\WGroupAssignment::find()
            ->innerJoin('w_group_setup', 'w_group_setup.id=w_group_assignment.gas_gset_relId')
            ->innerJoin('class_level', 'class_level.id=w_group_setup.gs_clslvl_relId')
            ->where([
                'gs_sem_relId' => $semesterId,
                'class_level.clslvl_num' => $clslvl->clslvl_num,
                'gas_std_relId' => $studentId
            ])
            ->one();

        if ($gas) {
            // var_dump($gas->gas_gset_relId,"Acad Year ID:". $academicYearId);
            return $gas->gasGsetRel;
        }

        // var_dump($clslvlId,$semesterId,$studentId,$academicYearId=null);die();

        $ghr = NULL;
        $gh = \backend\models\WGroupSetup::find()
            ->innerJoin('w_group_assignment', 'w_group_setup.id = w_group_assignment.gas_gset_relId')
            ->where([
                'gs_sem_relId' => $semesterId,
                'gs_clslvl_relId' => $clslvlId,
                'w_group_assignment.gas_std_relId' => $studentId
            ])
            ->with('wGroupAssignments')
            ->all();
        if ($gh && count($gh) > 0) {

            // var_dump($gh);die();

            $ghr = $gh[0];
            foreach ($gh as $Gskey) {
                // it is better if you can get the gset with which student enrolled course.
                $en = \frontend\models\SemesterRegistration2Detail::find()
                    ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                    ->where([
                        'semreg_std_relId' => $studentId,
                        'semreg_acadYear_relId' => $Gskey->gs_acadYear_relId,
                        'semreg_semester_relId' => $Gskey->gs_sem_relId
                    ])
                    ->andWhere(['srd2_group_relId' => $Gskey->id])
                    ->one();
                if ($en) {
                    return $Gskey;
                }
            }
        }
        return $ghr;
    }

    // public function getCurrentSemester(){
    //  static::__constructor();
    //  return $this->semId;
    // }

    // Checks if the specified student evaluated the instructor or not.
    public static function checkIfEvaluated($groupId, $courseId, $evalueeId, $evaluatorId)
    {

        // static::__constructor();
        $gset = \backend\models\WGroupSetup::findOne(['id' => $groupId]);
        $semevRec = \backend\models\SemesterEvaluation2::findOne([
            'semev_acadYear_relId' => $gset->gs_acadYear_relId,
            'semev_sem_relId' => $gset->gs_sem_relId,
            'semev_evaluee_relId' => $evalueeId,
            'semev_course_relId' => $courseId,
            'semev_group_relId' => $groupId,
        ]);
        if ($semevRec) {
            $semevDetRec = \backend\models\SemesterEvaluation2Detail::findOne([
                'semev2det_semev_relId' => $semevRec->id,
                'semev2det_evaluator_relId' => $evaluatorId,
                'semev2det_completed' => 1,
            ]);
            return $semevDetRec;
        } else {
            return NULL;
        }
    }
}
