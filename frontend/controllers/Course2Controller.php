<?php

namespace frontend\controllers;

use yii;
use backend\models\User;
use backend\models\Student;
use backend\models\CourseOffering2;
use backend\models\CourseOffering2Detail;
use backend\models\Instructor;
use backend\models\GradeScale;
use frontend\models\SemesterRegistration2;
use frontend\models\SemesterRegistration2Detail;
use \common\components\CommonControllerFunctions as CCF;

class Course2Controller extends \yii\web\Controller {

    public $acadYearId;
    public $semId;

    protected function __constructor() {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionPrintEnrStudList(){
        $params = isset($_GET['params']) && !empty($_GET['params'])?(string)$_GET['params'] : die('Invalid request');

        $params = json_decode($params);
        $courseId = isset($params->courseId) && !empty($params->courseId)?intval($params->courseId):die('Uknown Course.');
        $coffdetId = isset($params->coffdetId) && !empty($params->coffdetId)?intval($params->coffdetId):die('Uknown course registration entry...');

        $this->__constructor(); //Initialize global vars.

        if (Yii::$app->user->can('Instructor')  || Yii::$app->user->can('DepartmentHead')) {
                $instId = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->id]);
            }

            $coff2DetId = CourseOffering2Detail::find()->where(['id' => $coffdetId,'coff2det_inst_relId'=>$instId->id])->one();
        if ($coff2DetId) {
            $gset = CourseOffering2::findOne(['id' => $coff2DetId->coff2det_coff2_relId])->coff2GroupRel;
        } else {
            echo "<p class='bg-warning'>Invalid Orrefing record.</p>";
            die();
        }

        $gsetId = $gset->id;
        $course = \backend\models\Course::findOne($courseId);

        return $this->renderPartial('std_list',
            [
                'gsetObj'=>$gset,
                'coffdetObj'=>$coff2DetId,
            ]) ;

    }

    // return the list of students who enrolled the course offered for an instructor.
    public function actionEnrstudlists() {
        if (1 == 1) {

            $courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])?$_POST['courseId']:die('Uknown Course');
            $instId = isset($_POST['instId']) && !empty($_POST['instId'])?$_POST['instId']:die('Uknown Course course Error.');
            $this->__constructor(); //Initialize global vars.

            $coffdetId = $_POST['instId'];
            $courseId = $_POST['courseId'];

            if (Yii::$app->user->can('Instructor')  || Yii::$app->user->can('DepartmentHead')) {
                $instId = Instructor::findOne(['inst_user_relId'=>Yii::$app->user->id]);
            }

            $coff2DetId = CourseOffering2Detail::find()->where(['id' => $coffdetId,'coff2det_inst_relId'=>$instId->id])->one();
            if ($coff2DetId) {
                $gset = CourseOffering2::findOne(['id' => $coff2DetId->coff2det_coff2_relId])->coff2GroupRel;
            } else {
                echo "<p class='bg-warning'>Invalid Orrefing record.</p>";
                die();
            }

            $stds2 = SemesterRegistration2::find()
                ->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')
                ->innerJoin('user','user.id = student.std_user_relId')
                ->where([
                    'semreg_acadYear_relId' => $gset->gs_acadYear_relId,
                    'semreg_semester_relId' => $gset->gs_sem_relId,
                ])
                ->orderBy('user.first_name');

            $gsetId = $gset->id;
            $course = \backend\models\Course::findOne($courseId);
            // Get inst groups from course offering detail.

            if (!empty($stds2)) {
                $wt_rep = "";
                $stds2 = $stds2->all();

                $stdArray = [];
                foreach ($stds2 as $STDkey) {
                    $stdArray[] = $STDkey->semreg_std_relId;
                }

                $wt_rep .= "<table class='table table-striped table-hover table-sm table-bordered table-condensed'>";
                $wt_rep .= "

                    <caption>
                    <h4 style='text-decoration:underline; text-align:center;'>Student List.</h4>
                    Department:<u><i>{$gset->gsDeptRel->dept_name}</i></u>&nbsp;&nbsp;&nbsp;<br>
                    Class Level:<u><i>{$gset->gsClslvlRel->clslvl_num}</i></u><br>
                    Group:<u><i>{$gset->gsGroupRel->group_name}</i></u>&nbsp;&nbsp;&nbsp;<br>
                    Course:<u><i>{$course->course_title}</i></u>&nbsp;&nbsp;&nbsp;<br>
                    </caption>

                    <thead style='background-color:#8D0;'>
                        <th>#</th>
                        <th>Reg. ID.</th>
                        <th>Student Name</th>
                        <th>Gender</th>
                        <th>Nationality</th>
                        <th>Crs. Added</th>
                    </thead><tbody>";
                $students = \backend\models\Student::find()
                    ->innerJoin('user','user.id = student.std_user_relId')
                    ->where(['student.id'=>$stdArray])
                    ->orderBy('user.first_name')
                    ->all();
                $count = 0;
                foreach ($stds2 as $std) {

                    $s = Student::findOne(['id' => $std['semreg_std_relId']]);
                    $u = User::findOne(['id' => $s->std_user_relId]);

                    $srd = SemesterRegistration2Detail::findOne([
                                'srd2_semreg_relId' => $std['id'],
                                'srd2_course_relId' => $courseId,
                                'srd2_group_relId' => $gsetId,
                    ]);

                    if (empty($srd)) {
                        continue;
                    }
                    $gid = $srd->srd2_grade_relId;
                    $nat = ($u->nationality && $u->nationality != 0)?\backend\models\Nationality::findOne($u->nationality)->name:'-';
                    //$grade = is_null($gid)?'-':GradeScale::findOne(['id'=>$gid])->gs_ltr_grade;
                    $caddda = is_null($srd->srd2_coadd_flag) ? 'NO' : 'YES';
                    $wt_rep .= "<tr>
                          <td>" . ++$count . "</td>
                          <td>" . $s->std_regId . "</td>
                          <td>{$s->getFullName()}</td>
                          <td>{$u->gender}</td>
                          <td>{$nat}</td>
                          <td>" . $caddda . "</td>
                    </tr>";

                    //echo "<hr>";
                }
                $wt_rep .= "</tbody></table>";
                $count = $count == 0 ? 'NO Student ' : ($count == 1 ? $count . ' Student ' : $count . ' Students ');
                $wt_rep .= "<p class='bg-success'><i><b>" . $count . "</b></i> enrolled this course in this semester.</p>";
                return $wt_rep;
            } else {
                echo "<p class='bg-warning'>No student enrolled this course in this semester.</p>";
            }
        } else {
            echo "<p>No data found.</p>";
            die();
        }
    }

    protected function checkGroupAssigned($std) {
        
        return CCF::currentStudentGroup($std);
    }

    // returns the list of courses assigned for the instructor in this semester.
    public function actionCourselists() {
        $this->__constructor();

        if (Yii::$app->user->can('Instructor') || Yii::$app->user->can('DepartmentHead')) {

            // $usrId = ;

            $instId = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id])->id;

            $coff2det = NULL;
            if (isset($_POST['t'])) {
                if (Yii::$app->user->can('DepartmentHead')) { 
                    $instDept = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;
                    // ->groupBy('coff2det_course_relId')
                    $coff2det = CourseOffering2Detail::find()
                        ->innerJoin('course_offering2','course_offering2.id=course_offering2_detail.coff2det_coff2_relId')
                        ->innerJoin('course_catalog','course_catalog.id = course_offering2_detail.coff2det_cocat_relId')
                        ->innerJoin('course','course.id = course_offering2_detail.coff2det_course_relId')
                        ->andWhere(['course.course_dept_relId'=>$instDept])
                        ->andWhere(['<>','cocat_courseProject',1])
                        ->orderBy([
                            'coff2_acadYear_relId' => SORT_DESC,
                            'coff2_sem_relId' => SORT_DESC,
                        ])
                        ->all();
                }else{
                    $coff2det = CourseOffering2Detail::find()
                        ->innerJoin('course_offering2','course_offering2.id=course_offering2_detail.coff2det_coff2_relId')
                        ->andWhere(['coff2det_inst_relId' => $instId])
                        ->orderBy([
                            'coff2_acadYear_relId' => SORT_DESC,
                            'coff2_sem_relId' => SORT_DESC,
                        ])
                        ->all();
                }
            }else{
                $coff2det = CourseOffering2Detail::find()
                    ->innerJoin('course_offering2','course_offering2.id=course_offering2_detail.coff2det_coff2_relId')
                    ->andWhere(['coff2det_inst_relId' => $instId])
                    ->orderBy([
                            'coff2_acadYear_relId' => SORT_DESC,
                            'coff2_sem_relId' => SORT_DESC,
                        ])
                    ->all();
            }


            $instCourses = $coff2det;
            $wondereport  = "";

            if (empty($instCourses) && count($instCourses) < 1) {
                if (isset($_POST['t']) && Yii::$app->user->can('DepartmentHead')) {
                    echo "<p class='bg-danger'>No active course for this semester</p>";
                }else{
                    echo "<p class='bg-danger'>No course assigned for you in this semester.</p>";
                }
                return;
            } else {

                // var_dump($instCourses->all());
                // die();
                //$instCourses = $instCourses->all();
                $wondereport .= "<table class='table table-hover table-sm table-bordered table-condensed' style='font-size:9pt;'>";
                $wondereport .= "<thead style='background-color:#ace;'>
                        <th>#</th>
                        <th>Academic Year</th>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Yr.</th>
                        <th>Grp</th>
                        <th>CrdtHr</th>
                        <th>Show</th>
                    </thead>
                    <tbody>";
                $count = 0;
                $displayedCourseList = [];
                foreach ($instCourses as $instCourse) {
                    $course = \backend\models\Course::findOne(['id' => $instCourse['coff2det_course_relId']]);

                    $gsetId = CourseOffering2::findOne($instCourse['coff2det_coff2_relId'])->coff2_group_relId;

                    $groupId = \backend\models\WGroupSetup::findOne(['id' => $gsetId]);
                    // if (!($groupId && count($groupId) > 0)) {
                    //     continue;
                    // }
                    if (!($groupId)) {$instCourse->delete();continue;}

                    if (!in_array($instCourse->id, $displayedCourseList)) {
                        $displayedCourseList[] = $instCourse->id;
                    }else{continue;}
                    $yr = \backend\models\ClassLevel::findOne($groupId->gs_clslvl_relId)->clslvl_num;
                    $group = \backend\models\WGroup::findOne(['id' => $groupId->gs_group_relId])->group_name;

                    $tttt = \backend\models\Department::findOne(['id' => $groupId->gs_dept_relId])->dept_name;
                    $prog = \backend\models\Program::findOne(['id' => $groupId->gs_prog_relId])->program_name;
                    $adm = \backend\models\Admission::findOne(['id' => $groupId->gs_adm_relId])->admission_name;

                    if (!is_null($groupId->gs_spec_relId)) {
                        $tttt .= ' : ' . \backend\models\Specialization::findOne(['id' => $groupId->gs_spec_relId])->spec_name;
                    }

                    $ta = $instCourse['coff2det_TA_relId'] == $instId ? 'danger' : 'default';
                    $taaa = '';

                    if ($instCourse['coff2det_TA_relId'] != $instId &&
                            !is_null($instCourse['coff2det_TA_relId'])) {

                        $taInst = \backend\models\Instructor::findOne(['id' => $instCourse['coff2det_TA_relId']]);
                        $taUser = \backend\models\User::findOne(['id' => $taInst->inst_user_relId]);

                        $taaa .= 'Tech. Assistant : ' . $taUser->first_name . ' ' . $taUser->middle_name;
                    }
                    $btn = '';
                    if (isset($_POST['t'])) {
                        $btn = "<button class='btn btn-xs btn-success' onclick='markDistribution(" . $instCourse['id'] . "," . $course->id . ")'>Mark Dist.</button>";
                    }else{
                        $btn = "<button class='btn btn-xs btn-success' onclick='loadStudent(" . $instCourse['id'] . "," . $course->id . ")'>Students</button>";
                    }
                    $wondereport .= "<tr class='$ta' title='" . $taaa . "'>
                        <td>" . ++$count . "</td>
                        <td>{$instCourse->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc}-{$instCourse->coff2detCoff2Rel->coff2SemRel->semester_name}</td>
                        <td>" . $course->course_code . '</td>
                        <td>' . $course->course_title . "</td>
                        <td style='text-align:center;'>" . $yr . "</td>
                        <td style='text-align:center;' title='" . $tttt . "'>" . $group . "({$adm[0]}{$prog[0]})</td>
                        <td style='text-align:center;'>" . $course->course_crdthr . "</td>
                        <td>{$btn}</td>
                    </tr>";
                }
                $wondereport .= "</tbody></table>";

                if (isset($_POST['t']) && Yii::$app->user->can('DepartmentHead')) {
                    $wondereport .= "<p class='bg-success'><b>".$count."</b> courses.</p>";
                }else{
                    $wondereport .= "<p class='bg-success'>" . count($instCourses) . " course(s) found.</p>";
                }

            return $wondereport;
            }
        } else if (Yii::$app->user->can('Student')) { //return the list of course the student enrolled in this semsetr.
            $user = \backend\models\User::findOne(['id' => Yii::$app->user->identity->id]);
            $student = \backend\models\Student::findOne(['std_user_relId' => $user->id]);

            //$this->__constructor();
            //$gset2 = CCF::getStudentGroupHistory($clslvlId,$semesterId,$studentId);
            $gset = $this->currentStudentGroup($student);

            if ($gset) {
                
            } else {
                die('<p class="bg-danger">Not assigned to any group in this semester.</p>');
            }

            $semregEntry = SemesterRegistration2::findOne([
                        'semreg_acadYear_relId' => $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id,
                        'semreg_semester_relId' => $gset->gsActiveProgRel->semester_relId,
                        'semreg_std_relId' => $student->id,
                        'semreg_clslvl_relId' => $gset->gs_clslvl_relId,
            ]);

            if ($semregEntry) {
                $semregDet = SemesterRegistration2Detail::find()
                                ->where([
                                    'srd2_semreg_relId' => $semregEntry->id,
                                ])->all();
                if ($semregDet && count($semregDet) > 0) {
                    $Option = '';
                    $wt_response = "";
                    $dd = FALSE;
                    if (isset($_POST['dropDown']) && !empty($_POST['dropDown']) && $_POST['dropDown'] == TRUE) {
                        $Option .= "<option value=''> - </option>";
                        $dd = TRUE;
                    } else {
                        $wt_response .= "<table class='table table-hover table-sm table-bordered table-condensed'>";
                        $wt_response .= "<thead style='background-color:#ace;'>
                                    <th>#</th>
                                    <th>Course Code</th>
                                    <th>CrdtHr</th>
                                    <th>Inst</th>
                                    <th>TA</th>";
                        if (isset($_POST['t'])) { //for the case of coure outline.
                           $wt_response .= "<th title='Show continouos assessment mark distribution.'>Show</th>";
                        }
                     $wt_response .= "</thead>
                                <tbody>";
                    }
                    $count = 0;

                    foreach ($semregDet as $srd2) {

                        $course = \backend\models\Course::findOne(['id' => $srd2['srd2_course_relId']]);
                        if ($dd) {
                            $Option .= "<option value='{$course->id}'>" . $course->course_code . ' => ' . $course->course_title . "</option>";
                        } else {
                            $insts = $this->getInstTA($srd2, $student);
                            // var_dump($insts);die();
                            $wt_response .= "<tr>
                                    <td>" . ++$count . "</td>
                                    <td title='{$course->course_title}'>" . $course->course_code. "</td>
                                    <td style='text-align:center;'>" . $course->course_crdthr . "</td>
                                    <td>" . $insts[0] . "</td>
                                    <td>" . $insts[1] . "</td>
                                ";
                            if (isset($_POST['t'])) {
                               $wt_response .= "<td title='Show continouos assessment mark distribution for this course.'><button class='btn btn-success btn-sm' onclick='markDistribution({$srd2['srd2_coff_relId']},{$course->id})'>Mark Dist.</button></td>";
                            }
                            $wt_response .= "</tr>";
                        }
                    }
                    if (!$dd) {
                        $wt_response .= "</tbody></table>";
                        if (!isset($_POST['t'])) {
                           $wt_response .= "<p class='bg-success'>Enrolled  " . count($semregDet) . " course(s)in this semester.</p>";
                        }
                    } else {
                        $wt_response .= $Option;
                    }
                    return $wt_response;
                } else {
                    echo "<p class='bg-danger'>No semester registrtion record found. You may not enrolled any course in this semester.</p>";
                    die();
                }
            } else {
                echo "<p class='bg-danger'>You have't enrolled any course for this semester.</p>";
                die();
            }
        } else {
            echo "<p class='bg-danger'>Uknown User type (ERROR: UKNOWN_ROLE).</p>";
            die();
        }
    }

    // PHP = HypherText pre-processor
    // PDO = PHP Data Object
    // CSS = Cascaded Style Sheet
    // YII = Yes It Is
    // AJAX = Assyncronous JavaScript and XML
    // WT = WONDE-TOM
    // HTML = Hypher Text manipulation Language

    protected function currentStudentGroup($stdObject) {
        return CCF::currentStudentGroup($stdObject);
    }

    protected function getInstTA($srd2Obj, $studentObj) {

        //$gsetId =$studentObj->std_group_realId;
        //'coff2det_group_relId'=>$studentObj->std_group_realId,

        $coff2Det = CourseOffering2Detail::findOne([
                    'coff2det_coff2_relId' => $srd2Obj->srd2_coff_relId,
                    'coff2det_course_relId' => $srd2Obj->srd2_course_relId,
        ]);

        $TaName = 'N/A';
        $instName = 'TBA';
        $course = \backend\models\Course::findOne($srd2Obj->srd2_course_relId);

        if ($coff2Det) {
            // check for INST
            if (!is_null($coff2Det->coff2det_inst_relId)) {
                $inst = \backend\models\Instructor::findOne(['id' => $coff2Det->coff2det_inst_relId]);
                $user = \backend\models\User::findOne(['id' => $inst->inst_user_relId]);
                $instName = $user->first_name . ' ' . $user->middle_name;
            }

            // check for TA
            if (!is_null($coff2Det->coff2det_TA_relId)) {
                $ta = \backend\models\Instructor::findOne(['id' => $coff2Det->coff2det_TA_relId]);
                $taU = \backend\models\User::findOne(['id' => $ta->inst_user_relId]);
                $TaName = $taU->first_name . ' ' . $taU->middle_name;
            }else{
                if ($course->course_lab_hour > 0) {
                    $TaName = 'TBA';
                }else{
                     $TaName = 'N/A';
                }
            }
        }
        return [
            0=>$instName,
            1=>$TaName,
        ];

        // if (!($coff2Det && is_numeric($coff2Det->coff2det_inst_relId))) {
        //     return [
        //         0 => 'TBA', 
        //         1 => function(){
        //             return 'INST';
        //         }];
        // } else {
        //     $inst = \backend\models\Instructor::findOne(['id' => $coff2Det->coff2det_inst_relId]);

        //     if (!is_null($coff2Det->coff2det_TA_relId)) {
        //         $ta = \backend\models\Instructor::findOne(['id' => $coff2Det->coff2det_TA_relId]);
        //         $taU = \backend\models\User::findOne(['id' => $ta->inst_user_relId]);
        //         $TaName = $taU->first_name . ' ' . $taU->middle_name;
        //     }else{
        //         $TaName = 'TBA';
        //     }
        //     $user = \backend\models\User::findOne(['id' => $inst->inst_user_relId]);

        //     return [0 => $user->first_name . ' ' . $user->middle_name, 1 => $TaName];
        // }
    }

}
