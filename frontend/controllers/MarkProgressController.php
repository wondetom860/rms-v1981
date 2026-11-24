<?php

namespace frontend\controllers;

use yii;
use yii\data\ActiveDataProvider;
use \backend\models\ContAssessCatagory2;
use \backend\models\ContinuousAssessment2;
use \backend\models\InstanceMarkAssignment;
use \common\components\CommonControllerFunctions as CCF;

class MarkProgressController extends \yii\web\Controller
{
    public $acadYearId;
    public $semId;
    public $pieReportArray = [];
    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                // We will override the default rule config with the new AccessRule class
                'ruleConfig' => [
                    'class' => \yii\filters\AccessRule::class,
                ],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'get-mark-progress-report2'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['@',],
                    ],
                ]
            ]
        ];
    }
    public function actionIndex()
    {
        return $this->render('index');
    }


    private function markProgressReport($courseId)
    {
        $table = '';

        // $courseId = isset($_POST['courseId']) && !empty($_POST['courseId']) ? $_POST['courseId'] : die('Uknown Course');
        // $instId = isset($_POST['instId']) && !empty($_POST['instId']) ? $_POST['instId'] : die('Uknown Course course Error.');
        $this->__constructor(); //Initialize global vars.

        // $coffdetId = $_POST['instId'];
        // $courseId = $_POST['courseId'];
        $catObj = new ContAssessCatagory2();
        $contAssObj = new ContinuousAssessment2();
        $ima = new InstanceMarkAssignment();

        // $coff2DetId = \backend\models\CourseOffering2Detail::findOne(['id' => $coffdetId]);
        // if ($coff2DetId && count($coff2DetId) > 0) {
        //     $gsetId = \backend\models\CourseOffering2::findOne(['id' => $coff2DetId->coff2det_coff2_relId])->coff2_group_relId;
        // } else {
        //     echo "<p class='bg-warning'>Invalid Orrefing record.</p>";
        //     die();
        // }


        // Get student current group detail
        $std = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
        $gset = CCF::currentStudentGroup($std);

        // 'srd2_group_relId' => $gset->id,
        $srd = \frontend\models\SemesterRegistration2Detail::find()
            ->where(['srd2_course_relId' => $courseId])
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->andWhere([
                'semreg_acadYear_relId' => $gset->gs_acadYear_relId,
                'semreg_semester_relId' => $gset->gs_sem_relId,
            ])
            ->andWhere(['semreg_std_relId' => $std->id])
            ->with('srd2SemregRel')
            ->one();

        // echo "<pre>";
        // var_dump($srd[0]['srd2_coff_relId']);die();

        // $gset = \backend\models\WGroupSetup::findOne(['id' => $gsetId]);
        // Get inst groups from course offering detail.

        $dept = \backend\models\Department::findOne(['id' => $gset->gs_dept_relId])->dept_name;
        $clslvl = \backend\models\ClassLevel::findOne(['id' => $gset->gs_clslvl_relId])->clslvl_num;
        $gname = \backend\models\WGroup::findOne(['id' => $gset->gs_group_relId])->group_name;
        $course = \backend\models\Course::findOne(['id' => $courseId]);

        if ($srd ) {

            $table .= "<table class='table table-sm table-bordered table-condensed' style='font-size:9pt; background-color:#333; color:white;'>";
            // "<h4 style='text-decoration:underline; text-align:center;'>Mark Sheet.</h4>
            //                 Department:<u><i>{$dept}</i></u>&nbsp;&nbsp;&nbsp;
            //                 Class Level:<u><i>{$clslvl}</i></u><br>
            //                 Group:<u><i>{$gname}</i></u>&nbsp;&nbsp;&nbsp;"
            $gss = NULL;
            if (!is_null($srd->srd2_coadd_flag)) {
                $addHistory = \backend\models\AddHistory::findOne($srd->srd2_coadd_flag);

                $gss = $addHistory->ah_addWithGroup_relId;
            } else {
                $gss = $gset->id;
            }
            $instNmae = $this->getInstName($srd['srd2_coff_relId'], $courseId, $gss);

            $table .= "<caption style='text-align:center; font-size:12pt; color:blue;'>
                Course Name:<u><b><i>{$course->course_title}</i><b></u>&nbsp;&nbsp;&nbsp;<br>
                Instructor Name:<u><b><i>{$instNmae}</i><b></u>&nbsp;&nbsp;&nbsp;
                </caption>";
            // $mdCatagory = $catObj->getCourseCatagor($course->id,);



            // fetch instances first, and continuous assessment.
            $instances = $ima->getAssessmentInstances2($course->id);


            $table .= "<thead style=''>
                <th>#</th>
                <th colspan=2 style='text-align:center;'>Student Detail</th>
                <th colspan=" . (count($instances) + 2) . " style='text-align:center;'>Assessment detail</th>
                
                
                </thead>";
            $table .= "<thead style=''>
                    <th>#</th>
                    <th>Reg. ID.</th>
                    <th>Student Name</th>
                    ";
            // get assessment catagries (sub_catagories if any) made for the course and append them as the headr.

            if ($instances) {
                $namesArray = [];
                foreach ($instances as $key) {
                    // display actual instances of assessment under the category.
                    // $catagoryName = \backend\models\ContAssessCatagoryName::findOne(['id'=>$key->cat_name_relId])->cont_cat_name;
                    $instanceName = InstanceMarkAssignment::findOne($key->id);
                    if (!in_array($instanceName->ima_instanceName, $namesArray)) {
                        $namesArray[] = $instanceName->ima_instanceName;
                        $table .= "<th style='text-align:center;'>" . $instanceName->ima_instanceName . "(" . $instanceName->ima_possiblePoint . "%)</th>";
                    }
                }
            } else {

                $table .= "<th colspan=" . (count($instances)) . "><p style='color:red;'>Course assessment not set yet.</p></th>";
            }

            $table .= "<th style='text-align:center;'>Total.</th>
                    <th>Grade</th>";

            // get assessments made under this category.
            // echo "<thead style='background-color:#bbe;'>
            //             <th></th>
            //             <th></th>
            //             <th></th>
            //             <th></th>
            //             ";
            $table .= "</thead><tbody>";
            $count = 0;

            // $s = \backend\models\Student::findOne(['id' => $std['srd2SemregRel']['semreg_std_relId']]);
            $u = \backend\models\User::findOne(['id' => $std->std_user_relId]);

            $gid = $srd->srd2_grade_relId;
            //$grade = is_null($gid)?'-':GradeScale::findOne(['id'=>$gid])->gs_ltr_grade;
            $caddda = is_null($srd->srd2_coadd_flag) ? 'NO' : 'YES';
            $table .= "<tr>
                  <td>" . ++$count . "</td>
                  <td>" . $std->std_regId . "</td>
                  <td>{$std->getFullName()}</td>
                 ";
            $totalAssigned = 0;
            $totalgained = 0;
            $grade = 'N/C';
            $ttt = 'Mark Submission NOT COMPLETED yet.';
            foreach ($instances as $key2) {
                // For each assessment catagory, get the mark obtained by the student.
                $mark = '-';
                $instanceName = InstanceMarkAssignment::findOne([$key2->id]);

                // check if the instance is Final Exam
                if ($this->checkIfFinalExam($key2->imaCatagorySetupRel, $srd)) {
                    // check if the student is evauated the instructor.
                    $table .= "<td style='text-align:center; background-color:#f34;' title='Not Evaluated Instructor/TA.'>-</td>";
                    continue;
                }
                // 


                $mark = $contAssObj->getStudentMark($course->id, $std->id, $key2->ima_catagorySetup_relId, $instanceName->id);
                if ($mark) {
                    $totalAssigned += $instanceName->ima_possiblePoint;
                    $markDtae = $mark->ca_created_at;
                    $mark = $mark->ca_markObtained;
                    $totalgained += $mark;
                    $asessDate = date_format(date_create($markDtae), 'D d M Y');
                    $table .= "<td style='text-align:center;' title='{$asessDate}'>" . $mark . "</td>";
                } else {
                    $table .= "<td style='text-align:center;' title='Not assessed yet.'>-</td>";
                }
            }
            $table .= "<td style='text-align:center; font-weight:bold;'>" . $totalgained . "/" . $totalAssigned . "</td>";
            if ($totalAssigned == 0) {
                $table .= "<td style='text-align:center; font-weight:bold;'></td>";
            }
            if ($totalAssigned >= 100 && $srd->srd2_total_mark != $totalgained) {
                // Indicates the mark submission is completed. Ready to calculate LetterGrade depending on the grade scale.
                $ttt = 'Letter Grade equivalent.';

                // check if already completed and student requested for re-assessment.
                // $gradeObj = $contAssObj->setLetterGrade($totalAssigned, $totalgained, $std->id, $srd['srd2_coff_relId'], $srd->id, false);
                $gradeObj = $contAssObj->setLetterGrade2($totalAssigned, $totalgained, $std->id, NULL, $srd->id, false);
                if (is_object($gradeObj)) {
                    $grade = $gradeObj->gs_ltr_grade;
                }else{
                    $grade = $gradeObj;
                }
            }else{
                $grade = $srd->srd2GradeRel ? $srd->srd2GradeRel->gs_ltr_grade : '-';
            }
            $table .= "<td style='text-align:center;' title='{$ttt}'>{$grade}</td>";
            $table .= "</tr>";
            $table .= "</tbody></table>";
            // $count = $count == 0 ? 'NO Student ' : ($count == 1 ? $count . ' Student ' : $count . ' Students ');
            // $table .= "<p class='bg-success'><i><b>" . $count . "</b></i> enrolled this course in this semester.</p>";
            echo $table;
            // die();
        } else {
            echo "<p class='bg-warning'>Course Enrollment Error..</p>";
        }
    }

    private function checkIfFinalExam($catgeoryModel, $std)
    {
        // handle by the one with highest mark
        $catagoryName = $catgeoryModel->catCatagoryRel;
        if ($catagoryName && (strtoupper($catagoryName->catagory_name) == 'FINAL EXAM')) {

            // var_dump($catagoryName);die();
            // $coff = \backend\models\CourseOffering2Detail::findOne($std->srd2_coff_relId);
            $std2 = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
            $gset = CCF::currentStudentGroup($std2);
            $coff3 = NULL;
            if (!is_null($std->srd2_coadd_flag)) { //indicates the course is added.
                $addHistory = \backend\models\AddHistory::findOne(['id' => $std->srd2_coadd_flag]);
                $addWithGroup = $addHistory->ah_addWithGroup_relId;
                // $evtd = CCF::checkIfEvaluated($addWithGroup,$course->id,$evalueeId,Yii::$app->user->identity->id);
                $coff3 = CCF::getCourseInstructorForGroup($addWithGroup, $std->srd2_course_relId);
                $grrrp = $addWithGroup;
            } else {
                $coff3 = CCF::getCourseInstructorForGroup($gset->id, $std->srd2_course_relId);
                $grrrp = $gset->id;
            }

            // var_dump($std->evaluatedCourseInstructors());die();

            if ($std->evaluatedCourseInstructors()) {
                return false;
            }else{
                return true;
            }

            // $evalueeId = \backend\models\Instructor::findOne(['id' => $coff3['coff2det_inst_relId']])->inst_user_relId;
            // $evalueeId2 = is_null($coff3['coff2det_TA_relId']) ? NULL : \backend\models\Instructor::findOne(['id' => $coff3['coff2det_TA_relId']])->inst_user_relId;
            // //$evtd2 = is_null($evalueeId2)?TRUE:CCF::checkIfEvaluated($grrrp,$std->srd2_course_relId,$evalueeId2,Yii::$app->user->identity->id);
            // $evtd2 = is_null($coff3['coff2det_TA_relId']) || strtoupper($std->srd2SemregRel->semregStdRel->stdProgRel->program_name) == 'MASTERS' ? TRUE : \backend\models\Instructor::findOne(['id' => $coff3['coff2det_TA_relId']])->inst_user_relId;
            // $evtd = CCF::checkIfEvaluated($grrrp, $std->srd2_course_relId, $evalueeId, Yii::$app->user->identity->id);

            // // var_dump(is_null($evtd) || is_null($evtd2));die();

            // if (is_null($evtd) || is_null($evtd2)) {
            //     return true;
            // }
            // return false;
        } else {
            return false;
        }
    }

    private function getInstName($coffId, $courseId, $gsetId)
    {
        // $this->__constructor();

        // $gset = \backend\models\WGroupSetup::findOne($gsetId);
        // $coff = \backend\models\CourseOffering2Detail::find()
        //     ->innerJoin('course_offering2','course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
        //     ->where([
        //         'course_offering2.id'=>$coffId,
        //     ])
        //     ->andWhere([
        //         'coff2det_course_relId'=>$courseId,
        //         'course_offering2.coff2_group_relId'=>$gsetId
        //     ])
        //     ->one();
        $coff = CCF::getCourseInstructorForGroup($gsetId, $courseId);
        if (!($coff)) {
            return 'TBA';
        }
        $inst = \backend\models\Instructor::findOne($coff->coff2det_inst_relId);
        if ($inst) {
            $user = \backend\models\User::findOne($inst->inst_user_relId);

            return "{$user->first_name} " . $user->middle_name[0];
        }
        return 'TBA';
    }
    public function actionGetMarkProgressReport($d = NULL)
    { //d indicates graphical report.

        $contAssess = $this->getEnrlledCourses();
        $std = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
        $courseArray = [];
        $table = '';

        if ($contAssess) {
            foreach ($contAssess as $key) {
                // $coffDet = \backend\models\CourseOffering2Detail::findOne($key->srd2_coff_relId);

                if (!in_array($key->srd2_course_relId, $courseArray)) {
                    $courseArray[] = $key->srd2_course_relId;
                    if (is_null($d)) { //table

                        echo $this->markProgressReport($key->coff2det_course_relId);
                    } else { //piechart
                        $this->courseAssessmentProgressDetail($key, $key->srd2_course_relId, $d, $std);
                    }
                }
            }
            if (!is_null($d)) { //design or pichart
                return $this->pieReportArray;
            }
            die();
        } else {
            echo "<p class='bg-danger'>No record found.<p>";
            die();
        }

    }

    public function actionGetMarkProgressReport2($d = NULL)
    { //d indicates graphical report.

        $contAssess = $this->getEnrlledCourses();
        // echo "<pre>";
        // var_dump($contAssess);die();
        $courseArray = [];
        $table = '';

        if ($contAssess) {
            foreach ($contAssess as $key) {
                // $coffDet = \backend\models\CourseOffering2Detail::findOne($key->ca_coff_relId);

                if (!in_array($key->srd2_course_relId, $courseArray)) {
                    $courseArray[] = $key->srd2_course_relId;
                    // if(is_null($d)){ //table

                    echo $this->markProgressReport($key->srd2_course_relId);
                    // }else{
                    //     $this->courseAssessmentProgressDetail($key,$key->coff2det_course_relId,$d);
                    // }
                }
            }
            if (!is_null($d)) { //design or pichart
                return $this->pieReportArray;
            }
            die();
        } else {
            echo "<p class='bg-danger'>No record found.<p>";
            die();
        }

    }

    private function getEnrlledCourses()
    {
        if (Yii::$app->user->can('Student')) {

            $std = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
            $gset = CCF::currentStudentGroup($std);
            if ($gset) {
                $srd = \frontend\models\SemesterRegistration2Detail::find()
                    ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                    ->andWhere(['semreg_acadYear_relId' => $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id, 'semreg_semester_relId' => $gset->gsActiveProgRel->semester_relId])
                    ->andWhere(['semester_registration_2.semreg_std_relId' => $std->id])
                    ->with('srd2SemregRel')
                    ->all();

                return $srd;
            }
        }
    }

    // private function continousAssessmentReport()
    // {
    //     if (Yii::$app->user->can('Student')) {
    //         $std = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
    //         $this->__constructor();
    //         $contAssess = \backend\models\ContinuousAssessment2::find()
    //             ->innerJoin('course_offering2_detail', 'continuous_assessment2.ca_coff_relId = course_offering2_detail.id')
    //             ->innerJoin('semester_registration_2', 'semester_registration_2.id = continuous_assessment2.ca_semreg_relId')
    //             ->andWhere(['semreg_acadYear_relId' => $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id, 'semreg_semester_relId' => $gset->gsActiveProgRel->semester_relId])
    //             ->where(['ca_std_relId' => $std->id])
    //             ->indexBy('ca_coff_relId')
    //             ->all();

    //         return $contAssess;
    //     }

    //     return NULL;
    // }

    public function actionPie()
    {
        $qq = \backend\models\ContinuousAssessment::find()->groupBy('ca_semreg_relId');
        //$qq = array_merge($this->pieReportArray);
        $dataProvider = new ActiveDataProvider([
            'query' => $qq,
            'pagination' => false
        ]);

        $qq = $qq->all();

        return $this->render('pie', [
            'dataProvider' => $dataProvider,
            'model' => $qq,
        ]);
    }

    public function actionChart()
    {
        if (empty($this->pieReportArray)) {
            $this->actionGetMarkProgressReport(1);
        }
        return $this->renderAjax('chart', ['courses' => $this->pieReportArray]);
    }

    private function courseAssessmentProgressDetail($contAssessObject, $courseId, $d = NULL, $std)
    {
        $this->__constructor();
        if (!(is_null($contAssessObject))) {
            $course = \backend\models\Course::findOne($courseId);
            $contAssess = ContinuousAssessment2::find()
                ->innerJoin('cont_assess_catagory2', 'continuous_assessment2.ca_catagorySetup_relId = cont_assess_catagory2.id')
                ->where([
                    'cat_acadYear_relId' => $this->acadYearId,
                    'cat_sem_relId' => $this->semId,
                    'cat_course_relId' => $courseId,
                ])
                ->andWhere(['ca_std_relId' => $std->id, 'ca_semreg_relId' => $contAssessObject->srd2_semreg_relId])
                ->all();
            $totalAssigned = 0;
            $totalObtained = 0;
            if ($contAssess) {
                foreach ($contAssess as $key) {

                    // $catagory = \backend\models\ContAssessCatagory::findOne($key->)
                    $totalObtained += $key->ca_markObtained;
                    $assignment = \backend\models\InstanceMarkAssignment::findOne($key->ca_markAssignment_relId);
                    $totalAssigned += $assignment->ima_possiblePoint;
                }
            }

            $xx = $totalAssigned == 0 ? '-' : ($totalObtained / $totalAssigned);
            $this->pieReportArray[] = [
                "Course_Title" => $course->course_title,
                'Mark' => round($xx, 2, PHP_ROUND_HALF_UP) * 100,
                'title' => "{$totalObtained}/{$totalAssigned}",
                'course_code' => $course->course_code,
            ];
            if (is_null($d)) {
                return "<p>{$course->course_title} => {$totalObtained}/{$totalAssigned}</p>";
            } else {
                return $this->pieReportArray;
            }
        } else {
            return '-';
        }
    }
}
