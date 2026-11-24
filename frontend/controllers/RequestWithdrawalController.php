<?php
// server side scripting language
// use yii;

namespace frontend\controllers;

use backend\models\WStudentWithdrawalHistory;
use common\components\CommonControllerFunctions as CCF;

class RequestWithdrawalController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model = new WStudentWithdrawalHistory();
        if (\Yii::$app->user->can('Student')) {
            return $this->render('index_student', ['model' => $model]);
        } else if (\Yii::$app->user->can('DepartmentHead')) {

            if (isset($_GET['approve'])) {
                return $this->redirect(['/request-withdrawal/request-approve']);
            }
            return $this->redirect(['/request-withdrawal/request-hod']);
        } else if (\Yii::$app->user->can('Registrar')) {
            return $this->redirect(['/request-withdrawal/request-approve']);
        }
        return $this->render('index');
    }

    public function actionGetApprovedRequests()
    {
        return $this->render('request_status');
    }

    public function actionGetEnrolledCourses()
    {
        $stdId = isset($_POST['stdId']) && !empty($_POST['stdId']) ? intval($_POST['stdId']) : die('Invalid request.');
        // $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])?intval($_POST['acadYearId']):die('Uknown Academic Year.');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('Uknown Semester request.');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Invalid Class Year.');


        $std = \backend\models\Student::findOne($stdId);

        $gset = CCF::getStudentGroupHistoryPublic($clslvlId, $semId, $stdId);
        if (!($gset)) {
            die("<p class='bg-danger'>Invalid Student Entry.</p>");
        }
        $courses = CCF::getStudentGroupCoursesP($gset->id, $std->id, $gset->gs_sem_relId, $gset->gs_clslvl_relId);

        if ($courses) {
            echo "<table class='table table-sm table-hover table-striped table-bordered table-condensed'>";
            echo "<caption><center>Enrolled Courses for {$gset->gsSemRel->semester_name} Semester of {$gset->gsAcadYearRel->sch_year_desc}, Class Level of {$gset->gsClslvlRel->clslvl_num}</center></caption>";
            echo "
                <thead style='background-color:#8D0;'>
                    <th>#</th>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Grade</th>
                    <th>Change To</th>
                </thead><tbody>";
            $count = 0;
            $srd2List = [];
            $gsetId = NUll;
            foreach ($courses as $key) {
                $course = \backend\models\Course::findOne($key->srd2_course_relId);
                $grade  = is_null($key->srd2_grade_relId) ? '-' : \backend\models\GradeScale::findone($key->srd2_grade_relId)->gs_ltr_grade;
                $checkbox = is_null($key->srd2_grade_relId) ? "F" : $grade;
                // {$course->course_title} => {$grade}<br>
                $srd2List[] = $key->id;
                if (is_null($key->srd2_coadd_flag)) {
                    $gsetId = $key->srd2_group_relId;
                }
                echo "<tr>
                        <td>" . ++$count . "</td>
                        <td>{$course->course_code}</td>
                        <td>{$course->course_title}</td>
                        <td style='text-align:center; font-weight:bold;'>{$grade}</td>
                        <td style='text-align:center; font-weight:bold;'>{$checkbox}</td>
                    </tr>";
            }
            $srd2List = json_encode($srd2List);
            echo "<tr style='text-align:center;'><td colspan = 5 data-semregEntry = '{$srd2List}'><input type='hidden' name='semregEntry' id='semregEntry' data-gsetid='{$gsetId}' data-semregentry='{$srd2List}' value='{$srd2List}'> <b>Total of <i>{$count} Courses Enrolled.</i></b></td></tr>";
            echo "</tbody></table>";
        } else {
            echo "Did Not enrolled any course for {$gset->gsSemRel->semester_name} Semester of {$gset->gsAcadYearRel->sch_year_desc}, Class Level of {$gset->gsClslvlRel->clslvl_num}.";
        }
    }


    public function actionGetRequestForm()
    {
        $userId = isset($_POST['userId']) && !empty($_POST['userId']) ? intval($_POST['userId']) : die('Invalid query, Please refine your input.');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('Uknown Semester request.');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Invalid Class Year.');


        $std = \backend\models\Student::findOne(['std_user_relId' => $userId]);

        $exists = WStudentWithdrawalHistory::find()
            ->where(['swh_std_relId' => $std->id,])
            ->andWhere(['OR', ['swh_approved' => NULL], ['swh_HOD_approved' => NULL]])
            ->one();
        if ($exists) {
            echo "<p class='bg-warning'>Student have approval pending withdrawal request.</p>";
            die();
        }
        $exists = WStudentWithdrawalHistory::find()
            ->where(['swh_std_relId' => $std->id,])
            ->andWhere(['AND', ['swh_approved' => 1], ['swh_HOD_approved' => 1]])
            ->one();
        if ($exists) {

            if ($exists->swh_returnedBack == 1) {
                // echo "<p class='bg-success'>Student have Successfully approved withdrawal request, and Readmitted</p>";
            } else {
                if ($exists->swh_lastApproved == 1) {
                    echo "<p class='bg-warning'>Student have Successfully approved withdrawal request, NOT readmitted Yet.</p>";
                    die();
                } else {
                    if ($exists->swh_stdSigned == 1) {
                        echo "<p class='bg-warning'>Waiting Last approval from Registrar...</p>";
                        die();
                    } else {
                        echo "<p class='bg-warning'>Student withdrawal processing NOT completed.</p>";
                        die();
                    }
                }
            }
        }

        $gset = CCF::getStudentGroupHistoryPublic($clslvlId, $semId, $std->id);
        if (!($gset)) {
            die("<p class='bg-danger'>Student NOT active in this semester.</p>");
        }

        if ($std) {

            if (!\Yii::$app->user->can('DepartmentHead')) {
                $gset = CCF::currentStudentGroup($std);
            }

            if ($gset) {
                $user = \common\models\User1::findOne($userId);
                $adm = \backend\models\Admission::findOne($gset->gs_adm_relId);
                $prog = \backend\models\Program::findOne($gset->gs_prog_relId);
                $dept = \backend\models\Department::findOne($gset->gs_dept_relId);
                $spec = is_null($gset->gs_spec_relId) ? ' - ' : \backend\models\Specialization::findOne($gset->gs_spec_relId)->spec_name;
                $clslvl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId);

                $repo = "";

                $repo .= "
                    <div class='panel panel-default'>
                        <div class='panel-heading'>
                            Student Information
                        <div>
                        <div class='panel-body'>
                            <br><b>REG. ID.</b>: <u><i>{$std->std_regId}</i></u>
                            <br><b>Full Name</b>: <u><i>{$user->first_name} {$user->middle_name} {$user->last_name}</i></u>
                            <br><b>Admission</b>: <u><i>{$adm->admission_name}</i></u>
                            <br><b>Program</b>: <u><i>{$prog->program_name}</i></u>
                            <br><b>Department</b>: <u><i>{$dept->dept_name}</i></u>
                            <br><b>Class Level</b>: <u><i>{$clslvl->clslvl_num}</i></u>
                            <br><b>Specialization</b>: <u><i>{$spec}</i></u>
                        </div>

                    </div>
                     ";
                // check if student can readmit

                echo json_encode([1, $repo, $std->id]);
                die();
            } else {
                echo "<p class='bg-danger'>Selected Student is NOT assigned to any group in this semester. </p>";
                die();
            }
        } else {
            echo "<p class='bg-danger'>Invalid Student information.</p>";
            die();
        }
    }

    public function actionGetApplicationForm()
    {
        if (\Yii::$app->user->can('Student')) {
            $std = \backend\models\Student::find()->where(['std_user_relId' => \Yii::$app->user->identity->id])->one();

            $semregObj = new \frontend\models\SemesterRegistration2Detail();
            $reObj = new \backend\models\ReadmissionCalculationControl();

            if (strtoupper($std->std_semester_status) == 'WITHDRAWN') {
                echo "<p class='bg-warning'>You can NOT apply for withdrawal, try <a href='/request-readmission/index'> Re-admission </a> before this.</p>";
                die();
            }

            $exists  = NULL;
            $gset = $std->stdGroupReal;
            // 'swh_acadYear_relId'=>$gset->gs_acadYear_relId,
            // 'swh_sem_relId'=>$gset->gs_sem_relId
            if ($gset) {
                $exists = WStudentWithdrawalHistory::find()
                    ->where([
                        'swh_std_relId' => $std->id,
                    ])
                    ->andWhere(['swh_returnedBack' => NULL])
                    ->one();
            } else {
                $exists = WStudentWithdrawalHistory::find()
                    ->where(['swh_std_relId' => $std->id,])
                    ->andWhere(['swh_returnedBack' => NULL])
                    ->one();
            }

            if ($exists) {
                // 
                if ($exists->swh_lastApproved == 1 && !is_null($exists->swh_HOD_approved) && !is_null($exists->swh_approved)) {
                    if ($exists->swh_stdSigned != 1) {
                        return $this->renderPartial('withdrawal-application-form', ['model' => $exists]);
                    }
                    echo "<p class='bg-success'>You have approved pending withdrawal request.</p>";
                    die();
                } else {
                    // check if there is student process waiting request...
                    if ($exists->swh_stdSigned != 1) {
                        return $this->renderPartial('withdrawal-application-form', ['model' => $exists]);
                    } else {
                        if (((is_null($exists->swh_HOD_approved) || $exists->swh_HOD_approved != 0) &&
                            (is_null($exists->swh_approved) || $exists->swh_approved != 0) &&
                            (is_null($exists->swh_lastApproved) || $exists->swh_lastApproved != 0))) {

                            echo "<p class='bg-warning'>You have approval pending withdrawal request.</p>";
                            die();
                        }
                    }
                }
            }
            // $exists = WStudentWithdrawalHistory::find()
            //         ->where(['swh_std_relId'=>$std->id,])
            //         ->andWhere(['AND',['swh_approved'=>1],['swh_HOD_approved'=>1]])
            //         ->andWhere(['swh_returnedBack'=>NULL])
            //         ->one();
            // if ($exists && count($exists) > 0) {
            //     if ($exists->swh_lastApproved == 1) {
            //         echo "<p class='bg-warning'>You have Successfully approved withdrawal request, NOT readmitted Yet.</p>";die();
            //     }else{
            //        echo "<p class='bg-success'>No pending request found.</p>";die();
            //     }

            // }

            $gset = CCF::currentStudentGroup($std);

            // check if enrolled any course in the semester.

            if ($gset) {

                $validGradesArray = [];
                $validGrades = \backend\models\GradeScale::find()->where(['OR', ['gs_valid' => 1], ['gs_ltr_grade' => 'F']])->all();
                if ($validGrades) {
                    foreach ($validGrades as $VGkey) {
                        $validGradesArray[] = $VGkey->id;
                    }
                }

                // var_dump($validGradesArray);die();

                // check readmission entry control
                // if one course of the semester has given grade::
                $enrolledCourses = \frontend\models\SemesterRegistration2Detail::getEnrolledCourses($std, $gset);
                if ($enrolledCourses) {
                    $graded = false;
                    $semregObj = $enrolledCourses[0]->srd2SemregRel;
                    foreach ($enrolledCourses as $ECkey) {
                        if (!is_null($ECkey->srd2_grade_relId) &&  in_array($ECkey->srd2_grade_relId, $validGradesArray)) {
                            $graded = true;
                            break;
                        }
                    }
                    if ($graded) {
                        if ($semregObj->semreg_gradeCompleted == 1) {
                            $canApply = $reObj->checkREControl($std, $gset, 1);
                            // var_dump($canApply);die();
                            if (is_bool($canApply) && $canApply == true) {
                                echo 1;
                                die();
                            } else {
                                if (is_object($canApply)) {
                                    return $this->renderPartial('withdrawal-application-form', ['model' => $canApply]);
                                }
                                echo $canApply;
                                die();
                            }
                        }
                        return "<p class='bg-warning'> You have graded Course in this semester.</p>";
                    }
                    echo 1;
                    die();
                } else {
                    $canApply = $reObj->checkREControl($std, $gset);
                    if (is_bool($canApply) && $canApply == true) {
                        echo 1;
                        die();
                    } else {
                        echo $canApply;
                        die();
                    }
                }
            } else {
                echo "<p class='bg-warning'> You are NOT assigned to any group in this semestr.</p>";
                die();
            }
        } else if (\Yii::$app->user->can('DepartmentHead')) {
            // return $this->redirect('request-hod');
            echo "Welcome";
            die();
        }
    }

    public function actionGetApplicationReport2()
    {
        if (\Yii::$app->user->can('Registrar') || \Yii::$app->user->can('Admin')) {
            $id = isset($_GET['id']) && !empty($_GET['id']) ? intval($_GET['id']) : die('Invalid query, Please refine your input.');

            $request = WStudentWithdrawalHistory::find()
                ->where([
                    'id' => $id,
                    'swh_stdSigned' => 1,
                ])
                ->one();
            if ($request) {
                $this->layout = 'printLayout';
                return $this->render('withdrawal-application-form', ['model' => $request]);
            } else {
                die("Invalid Request status, NOT_READY_FOR GENERATING REPORT, NOT COMPLETE YET.");
            }
        } else if (\Yii::$app->user->can('Student')) {
            $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('Invalid query, Please refine your input.');
            $request = WStudentWithdrawalHistory::find()
                ->where([
                    'id' => $id,
                    'swh_stdSigned' => 1,
                ])
                ->one();
            if ($request) {
                return $this->renderPartial('withdrawal-application-form', ['model' => $request]);
            } else {
                die("Invalid Request status, NOT_READY_FOR GENERATING REPORT, NOT COMPLETE YET.");
            }
        } else {
            die("Invalid access!");
        }
    }

    public function actionGetApplicationReport()
    {
        if (\Yii::$app->user->can('Registrar')) {
            $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('Invalid query, Please refine your input.');

            $request = \backend\models\WStudentWithdrawalHistory::find()
                ->where([
                    'id' => $id,
                    'swh_stdSigned' => 1,
                ])
                ->one();
            if ($request) {
                return $this->renderPartial('withdrawal-application-form', ['model' => $request]);
            } else {
                die("Invalid Request status, NOT_READY_FOR GENERATING REPORT, NOT COMPLETE YET.");
            }
        } else if (\Yii::$app->user->can('Student')) {
            $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('Invalid query, Please refine your input.');
            $request = \backend\models\WStudentWithdrawalHistory::find()
                ->where([
                    'id' => $id,
                    'swh_stdSigned' => 1,
                ])
                ->one();
            if ($request) {
                return $this->renderPartial('withdrawal-application-form', ['model' => $request]);
            } else {
                die("Invalid Request status, NOT_READY_FOR GENERATING REPORT, NOT COMPLETE YET.");
            }
        } else {
            die("Invalid access!");
        }
    }

    public function actionApproveRequestStudent()
    {
        if (\Yii::$app->user->can('Student')) {
            $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('Invalid query, Please refine your input.');
            $date = isset($_POST['date']) && !empty($_POST['date']) ? $_POST['date'] : die('Date of Last class Attended is required.');

            $dateAttended = date_format(date_create($date), 'Y-m-d h:i:s');

            // var_dump($dateAttended );die();

            $request = \backend\models\WStudentWithdrawalHistory::find()
                ->where([
                    'id' => $id,
                ])
                ->one();
            if ($request) {
                $result = \Yii::$app->db->createCommand()
                    ->update(
                        'w_student_withdrawal_history',
                        [
                            'swh_stdSigned' => 1,
                            'swh_last_dateAttended' => $dateAttended,
                            'swh_stdSigned_at' => date("Y-m-d h:i:s"),
                            'swh_stdSigned_by' => \Yii::$app->user->identity->id,
                        ],
                        [
                            'id' => $id,
                        ]
                    )
                    ->execute();
                if ($result) {
                    return $this->redirect('/request-withdrawal/index');
                } else {
                    die("<p class='bg-danger'>Could NOT complete the request. ERROR WHILE CHANGING PROFILE.</p>");
                }
            } else {
                die("<p class='bg-danger'>Invalid request Entry, Expected request status NOT satisfied.</p>");
            }
        } else {
            die("Invalid Operation, STUDENT_PRIVILLEGED_ACTION !");
        }
    }


    public function actionRequestHod()
    {
        return $this->render('request_HOD', [
            'model' => new \backend\models\StudentSemesterStatusHistory(),
            'modelW' => new  WStudentWithdrawalHistory(),
        ]);
    }

    // Approval for HOD / Registrar
    public function actionRequestApprove()
    {
        return $this->render('approve_request', ['model' => new WStudentWithdrawalHistory()]);
    }
}
