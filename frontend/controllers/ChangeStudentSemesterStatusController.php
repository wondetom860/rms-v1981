<?php

namespace frontend\controllers;

use backend\models\SemesterRegistration2;
use backend\models\StudentSemesterStatusName;
use \common\components\CommonControllerFunctions as CCF;
use yii\base\ErrorException;
use \backend\models\WGroupSetup;
use yii;
use \frontend\models\SemesterRegistration2Detail;
use yii\filters\VerbFilter;

class ChangeStudentSemesterStatusController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
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
                       'actions' => [],
                       'allow' => true,
                       'roles' => ['@'],
                   ],
               ]
           ]
        ];
    }

    public $acadYearId;
    public $semId;

    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    public function actionIndex()
    {
        $model = new \backend\models\StudentSemesterStatusHistory();
        if (Yii::$app->user->can('Registrar')) {
            return $this->render('index_Registrar', ['model' => $model]);
        }
        return $this->render('index', ['model' => $model]);
        // return $this->render('index');
    }

    private function getSemGrade($stdId, $clslvl, $sem, $cGset)
    {
        $semObject = \backend\models\Semester::findOne(['semester_name' => $sem]);
        $clslvlObj = \backend\models\ClassLevel::findOne(['clslvl_num' => $clslvl, 'clslvl_program_relId' => $cGset->gs_prog_relId]);
        $stdObject = \backend\models\Student::findOne($stdId);

        $semregObj = new SemesterRegistration2Detail();
        // 1=>$forChange - want to get default semestr status of the students too.
        $semReport = $semregObj->getSemregRecord($stdId, $clslvlObj->id, $semObject->id, $cGset, 1);

        // var_dump($semReport);
        // die();

        if (is_array($semReport)) {
            $semReport[0] = is_numeric($semReport[0]) ? sprintf("%0.2f", $semReport[0]) : $semReport[0];
            $semReport[1] = is_numeric($semReport[1]) ? sprintf("%0.2f", $semReport[1]) : $semReport[1];
            return $semReport;
        }
        return [NULL, NULL, NULL];
    }

    public function actionGetAdStudentList(){
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('acadYearId Error..');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('semId Error..');
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Uknown Admission');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Uknown Program');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Uknown Class Year.');
        $failStata = StudentSemesterStatusName::find()->where(['sssn_pass_status' => [3,2]])->select('id')->column();
        $semregs = SemesterRegistration2::find()
            ->where([
                'semreg_acadYear_relId' => $acadYearId,
                'semreg_semester_relId' => $semId,
                'semreg_clslvl_relId' => $clslvlId,
                'semreg_gradeCompleted' => 1,
            ])->andWhere([
                'OR',
                ['semreg_last_ssn_relId' => NULL],
                ['semreg_last_ssn_relId' => $failStata]
            ])
            ->all();
        return $this->renderPartial('__reports/ad-semregs',['semregs' => $semregs]);
    }

    // get those students with AD status in the selected semester.
    public function actionGetAdStudentList2()
    {
        // completed seemster courses
        // all course grades submitted
        // known CGPA and SGPA
        // Status not mentioned or filed
        
        // 
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('acadYearId Error..');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('semId Error..');
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Uknown Admission');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Uknown Program');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Uknown Class Year.');
        $wondereport = "";
        $gs = new WGroupSetup();
        $gsets = $gs->getGsets($admId, $progId, NULL, $clslvlId, $acadYearId, $semId);

        if ($gsets && count($gsets) > 0) {
            $clslvlObj = \backend\models\ClassLevel::findOne(['id' => $clslvlId, 'clslvl_program_relId' => $progId]);
            $semObj = \backend\models\Semester::findOne($semId);
            $ayObj = \backend\models\SchoolYear::findOne($acadYearId);
            $progObj = \backend\models\Program::findOne($progId);
            $admObj = \backend\models\Admission::findOne($admId);

            $wondereport .= "<table class='table table-striped table-hover table-condensed table-sm' style='font-size:10pt;'>";
            $wondereport .= "<caption style='font-weight: normal; color:black;'>
                <center>
                    <b><u>List of Students with Academic Defficiency</u></b><br>

                    {$semObj->semester_name} Semester of Academic Year {$ayObj->sch_year_desc}<br>

                    Program : <u>{$admObj->admission_name} {$progObj->program_name} {$clslvlObj->clslvl_num} Year</u>
                </center>
            </caption>";
            $wondereport .= "<thead style='background-color: #8D0;'>";
            $wondereport .= "<th rowspan = 2 style='border:1px solid black; vertical-align:middle;'>#</th>
                <th rowspan = 2 style='border:1px solid black; vertical-align:middle; text-align:left;'>Reg. NO.</th>
                <th rowspan = 2 style='border:1px solid black; vertical-align:middle; text-align:left;'>Full Name</th>
                <th colspan=3 style='border:1px solid black; text-align:center;'>Semester Grade History</th>
                <th rowspan=2 colspan=2 style='border:1px solid black; text-align:center;'>Option</th>
                </thead>";

            $wondereport .= "</thead>";
            // GPA row
            $wondereport .= "<thead style='background-color: #8D0;'>
                    <th style='border:1px solid black;'></th>
                    <th style='border:1px solid black;'></th>
                    <th style='border:1px solid black;'></th>";
            $cl = $clslvlObj->clslvl_num;
            while ($cl <= $clslvlObj->clslvl_num) {

                // Sem - I
                $wondereport .= "<th style='border:1px solid black; text-align:center;'>SGPA</th>
                      <th style='border:1px solid black; text-align:center;'>CGPA</th>
                      <th style='border:1px solid black; text-align:left;'>Status</th>";

                $cl++;

            }

            $wondereport .= "<th style='border:1px solid black; text-align:left;'></th>
                    <th style='border:1px solid black; text-align:left;'></th>
                </thead><tbody>";
            $count = 1;
            foreach ($gsets as $gset) {

                $stds = \backend\models\WGroupAssignment::find()
                    ->innerJoin('student', 'student.id = w_group_assignment.gas_std_relId')
                    ->innerJoin('user', 'user.id = student.std_user_relId')
                    ->where(['gas_gset_relId' => $gset->id])
                    ->orderBy('user.first_name')
                    ->all();
                $cGset = NULL;

                if ($stds && count($stds) > 0) {
                    foreach ($stds as $std) {
                        $student = $std->gasStdRel;
                        $stdRow = "";
                        $statusId = 0;
                        $showRow = true;
                        if ($student) {
                            // $cGset = CCF::currentStudentGroup($student);
                            // check if requested before,
                            $requested = $this->checkIfRequested($student, $acadYearId, $semId);
                            if ($requested) {
                                continue;
                            }
                        } else {
                            continue;
                        }
                        $stdRow .= "<tr onclick='adClas({$count})' id='row_{$count}'>";
                        $stdRow .= "<td style='border:1px solid black;'>" . $count . "</td>";
                        $stdRow .= "<td style='border:1px solid black; text-align:center;'>{$student->std_regId}</td>
                            <td style='border:1px solid black; text-align:left;'>{$student->stdUserRel->first_name} {$student->stdUserRel->middle_name} {$student->stdUserRel->last_name} <b>({$student->stdDeptRel->dept_code})</b></td>";
                        $c = $clslvlObj->clslvl_num;
                        while ($c <= $clslvlObj->clslvl_num) {

                            $semGrade1 = $this->getSemGrade($student->id, $c, $semObj->semester_name, $gset);
                            // Y-I
                            $set1 = isset($semGrade1[3]) ? 'bold' : 'normal';
                            $stdRow .= "<td style='border:1px solid black; text-align:center; font-weight:{$set1};'>$semGrade1[0]</td>";
                            $stdRow .= "<td style='border:1px solid black; text-align:center; font-weight:{$set1};'>$semGrade1[1]</td>";
                            if (isset($semGrade1[2])) {
                                $statusId = is_int($semGrade1[3]) ? $semGrade1[3] : $statusId;
                                if (is_int($statusId) && $statusId != 0) {
                                    $st = \backend\models\StudentSemesterStatusName::findOne($statusId);
                                    $stdRow .= "<td style='border:1px solid black; text-align:left; font-weight:{$set1};'>{$st->sssn_name}</td>";
                                } else {
                                    $stdRow .= "<td style='border:1px solid black; text-align:left; font-weight:{$set1};'>$semGrade1[2]</td>";
                                }
                            } else {
                                $stdRow .= "<td style='border:1px solid black; text-align:left; font-weight:{$set1};'>$semGrade1[2]</td>";
                            }

                            $showRow = isset($semGrade3[4]) ? (($semGrade3[5] == 3 || $semGrade3[5] == 0) ? true : false) :
                                (isset($semGrade2[4]) ? (($semGrade2[5] == 3 || $semGrade2[5] == 0) ? true : false) :
                                    (isset($semGrade1[4]) ? (($semGrade1[5] == 3 || $semGrade1[5] == 0) ? true : false) :
                                        false));
                            $c++;
                        }
                        if ($showRow) {
                            $ops = $this->getOptions2($student->id);
                            $option = "<select data-s='WONDE-TOM' class='form-control' title='select action' 
                                onChange='changeStatus({$student->id},$(this).val(),{$statusId})'>{$ops}</select>";
                            $btn = "<p id='change_stat_{$student->id}'>change</p>";
                            $stdRow .= "<td style='border:1px solid black;'>{$option}</td>
                                        <td style='border:1px solid black;'>{$btn}</td>";
                            $stdRow .= "</tr>";
                            $wondereport .= $stdRow;
                            $count++;
                        }
                    }
                }
            }
            $wondereport .= "</tbody></table>";
            return $wondereport;
        }
    }

    protected function removeEnrolledCourses($studentId)
    {
        // throw new ErrorException("removing enrolled courses erro", 1);   
        $this->__constructor();
        $remove1 = \frontend\models\SemesterRegistration2Detail::find()
            ->where([
                'semreg_std_relId' => $studentId,
                'semreg_acadYear_relId' => $this->acadYearId,
                'semreg_semester_relId' => $this->semId
            ])
            ->innerJoin('semester_registration_2', 'semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id');
        $remove = $remove1->all();

        if ($remove && count($remove) > 0) {

        } else {
            throw new ErrorException("Not Enrolled any course for this semester.", 1);
        }
        $semregEntry = $remove[0]['srd2_semreg_relId'];
        $deletedCoursesTitle = [];
        $result22 = NULL;
        if ($remove && count($remove) > 0) {
            foreach ($remove as $key) {
                $deletedCoursesTitle[] = \backend\models\Course::findOne(['id' => $key['srd2_course_relId']])->course_title;
                $result22 = Yii::$app->db->createCommand()
                    ->delete('semester_registration_2_detail', ['id' => $key['id']])
                    ->execute();
                if (!$result22) {
                    throw new ErrorException("Removing enrolled course failed. Please try again.", 1);
                }
            }

            if ($result22) {
                $result2 = \frontend\models\SemesterRegistration2::findOne(['id' => $semregEntry]);
                if ($result2) {
                    $result3 = Yii::$app->db->createCommand()->delete('semester_registration_2', ['id' => $result2->id])->execute();
                    return [$result3, $deletedCoursesTitle];
                } else {
                    throw new ErrorException("Seemster registration Entry Not found, Please re-try.", 1);
                }
            } else {
                throw new ErrorException("Semester registered course removsl failed. Please re-try.", 1);
            }
        } else {
            throw new ErrorException("Not enrolled any course in this semester.", 1);
        }

        // throw new ErrorException("removing enrolled courses error  REMOVING   ----. COUNT: ".count($remove), 1);
    }

    public function actionApproveStudentSemesterStatusChange()
    {
        if (Yii::$app->user->can('Registrar')) {
            $recordId = isset($_POST['recordId']) && !empty($_POST['recordId']) ? intval($_POST['recordId']) : die('Uknown status record.');
            $actionId = isset($_POST['actionId']) && !empty($_POST['actionId']) ? intval($_POST['actionId']) : die('Uknown Action Selected.');

            // Change back the actionType
            $actionId = $actionId == 2 ? 0 : $actionId;

            // var_dump($actionId);die();


            $this->__constructor();

            $semStatus = \backend\models\StudentSemesterStatusHistory::findOne(['id' => $recordId]);
            $semStatusName = \backend\models\StudentSemesterStatusName::findOne(['id' => $semStatus->sssh_sssn_relId]);

            if ($semStatus) {
                // update the record to approve.

                $transaction = Yii::$app->db->beginTransaction();
                // 1=approve   0=reject    NULL= pending
                try {
                    if (!is_null($actionId) && $actionId == 0) { // the action is to reject the status change.
                        // update the approval status to 0.
                        $result = Yii::$app->db->createCommand()
                            ->update('student_semester_status_history', [
                                'sssh_approved' => $actionId,
                                'sssh_approved_at' => date('Y-m-d h:i:s'),
                                'sssh_approved_by' => Yii::$app->user->identity->id,
                            ], [
                                'id' => $recordId
                            ])
                            ->execute();
                        if ($result) {
                            $transaction->commit();
                            echo "Student semester status change history Rejected.";
                            die();
                        } else {
                            throw new ErrorException('Error while updating your request. pleae try again', 1);
                        }
                    } else if (!is_null($actionId) && $actionId == 1) { //it is approval
                        $result = Yii::$app->db->createCommand()
                            ->update('student_semester_status_history', [
                                'sssh_approved' => $actionId,
                                'sssh_approved_at' => date('Y-m-d h:i:s'),
                                'sssh_approved_by' => Yii::$app->user->identity->id,
                            ], ['id' => $recordId])
                            ->execute();

                        if ($result) {
                            $student = \backend\models\Student::findOne($semStatus->sssh_std_relId);
                            $result = Yii::$app->db->createCommand()
                                ->update('semester_registration_2', [
                                    'semreg_last_ssn_relId' => $semStatusName->id,
                                ], [
                                    'semreg_std_relId' => $semStatus->sssh_std_relId,
                                    'semreg_acadYear_relId' => $semStatus->sssh_acadYear_relId,
                                    'semreg_clslvl_relId' => $semStatus->sssh_clslvl_relId,
                                    'semreg_semester_relId' => $semStatus->sssh_sem_relId,
                                ])
                                ->execute();
                            $this->changeStudentProfile($semStatus->sssh_std_relId, $semStatusName->sssn_name);
                            $transaction->commit();
                            return 1;
                        } else {
                            throw new ErrorException('Error while updating your request. pleae try again', 1);
                        }
                    }
                } catch (ErrorException $e) {
                    $transaction->rollBack();
                    var_dump($e);
                    die();
                }
                // if it is approve, remove courses from the enroll status.
                // else if it is reject, cahnge only the status.
                // Done.
            } else {
                echo "<p class='bg-warning'>Record Error, Uknown Status cahnge record. please reload.</p>";
            }
        } else {
            die('You can\'t do such operation. Lash....');
        }
    }

    // change student status on his/her profile tooo
    protected function changeStudentProfile($studentId, $status)
    {
        return Yii::$app->db->createCommand()
            ->update('student', ['std_semester_status' => $status], ['id' => $studentId])
            ->execute();
    }

    // Roll back the opertaion on change
    public function actionChangeStudentSemesterStatusBack()
    {
        if (Yii::$app->user->can('Admin')) {
            $recordId = isset($_POST['recordId']) && !empty($_POST['recordId']) ? intval($_POST['recordId']) : die('Uknown status record.');

            $this->__constructor();

            $semStatus = \backend\models\StudentSemesterStatusHistory::findOne(['sssh_acadYear_relId' => $this->acadYearId, 'id' => $recordId]);

            if ($semStatus) {
                $delete = Yii::$app->db->createCommand()
                    ->delete('student_semester_status_history', [
                        'id' => $recordId,
                    ])
                    ->execute();
                if ($delete) {
                    echo 1;
                } else {
                    echo "<p class='bg-danger'>Errro while updating status change request.</p>";
                    die();
                }
            } else {
                echo "<p class='bg-danger'>ERROR: No such record found. (4:04PM)</p>";
            }
        } else {
            echo "<p class='bg-danger'>Who told you to do so? IN-VALID OPERTAION>>>></p>";
        }
    }

    // Change student semester status
    public function actionChangeStudentSemesterStatus()
    {
        $this->__constructor();
        $studentId = isset($_POST['studentId']) && !empty($_POST['studentId']) ? intval($_POST['studentId']) : die('Not selected Student');
        $statusId = isset($_POST['statusId']) && !empty($_POST['statusId']) ? intval($_POST['statusId']) : die('Not selected Status');
        $defStatId = isset($_POST['defStatId']) && !empty($_POST['defStatId']) ? intval($_POST['defStatId']) : NULL;

        // $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])?intval($_POST['acadYearId']):die('acadYearId Error..');
        // $semId = isset($_POST['semId']) && !empty($_POST['semId'])?intval($_POST['semId']):die('semId Error..');
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Uknown Admission for change');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Uknown Program');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Uknown Class Year.');

        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('<p class="bg-danger">Uknown Academic Year</p>');
        $semId = isset($_POST['SemId']) && !empty($_POST['SemId']) ? intval($_POST['SemId']) : die('<p class="bg-danger">Uknown semester.</p>');

        $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : NULL;

        $gset = NULL;
        if (is_null($gsetId)) {
            $gset = CCF::getStudentGroupHistoryPublic($clslvlId, $semId, $studentId);
        } else {
            $gset = WGroupSetup::findOne($gsetId);
        }
        $student = \backend\models\Student::findOne(['id' => $studentId]);
        // var_dump($student);die();        
        $statuss = ['Academic Dismissal', 'Defector', 'Discipline dismissal', 'Personal dropout'];
        $probations = ['Probation 1', 'Probation 2'];
        $sssns = NULL;
        // identitfy status 


        // $gset = CCF::currentStudentGroup($student);
        if (is_null($gset)) {
            echo "<p class='bg-danger'>Student group arrangement for Selected semester is invalid.</p>";
            die();
        }


        $sssns = \backend\models\StudentSemesterStatusName::findOne($statusId);

        $requested = $this->checkIfRequested($student);
        if ($requested && count($requested) > 0) {
            echo "<p class='bg-danger'>Already Requested for status change, Waiting for registrar approval.</p>";
            die();
        }

        if ($sssns) {
            // It is promaotion , no need of removing courses from the student enrollment status.
            $save = $this->storeData($studentId, $statusId, $gset, $defStatId, $acadYearId, $semId);
            if ($save) {
                echo 1;
            } else {
                echo "<p class='bg-danger'>Error while saving student status.{[$save]}</p>";
            }
            die();
        }
    }

    // Store last operstion to the database
    private function storeData($stdId, $statusId, $gset, $defStatId = NULL, $acadYear = NULL, $sem = NULL)
    {
        $model = new \backend\models\StudentSemesterStatusHistory();

        $year = is_null($acadYear) ? $this->acadYearId : $acadYear;
        $sem = is_null($sem) ? $this->semId : $sem;

        $model->sssh_std_relId = $stdId;
        $model->sssh_detault_sssn_relId = $defStatId;
        $model->sssh_sssn_relId = $statusId;
        $model->sssh_acadYear_relId = $year;
        $model->sssh_sem_relId = $sem;
        $model->sssh_clslvl_relId = $gset->gs_clslvl_relId;

        $model->sssh_created_at = $model->sssh_updated_at = date('Y-m-d h:i:s');
        $model->sssh_created_by = $model->sssh_updated_by = Yii::$app->user->identity->id;

        return $model->save();
    }

    // Get list of semseter options
    private function getOptions2($stdId)
    {
        // ->where(['sssn_name' => ['Probation 1', 'Probation 2']])
        $kafiya = \backend\models\StudentSemesterStatusHistory::find()
            ->where(['sssh_std_relId' => $stdId, 'sssh_approved' => 1])
            ->all();
        $kafiyaList = [];
        if ($kafiya && count($kafiya) > 0) {
            foreach ($kafiya as $keyKafiya) {
                $kafiyaList[] = $keyKafiya->sssh_sssn_relId;
            }

            $kafiyaList = array_unique($kafiyaList);

            // var_dump($kafiyaList);die();
        }
        $sssns = \backend\models\StudentSemesterStatusName::find()
            //->where(['OR',['sssn_pass_status' => 3],['sssn_pass_status' => 2]])
            ->andWhere(['NOT IN', 'id', $kafiyaList])
            ->orderBy('sssn_name ASC')
            ->all();
        //echo "WONDETOM";
        $sssnOptions = "<option value=''> - </option>";
        if ($sssns && count($sssns) > 0) {
            foreach ($sssns as $key) {
                $sssnOptions .= "<option title='{$key->id}' value={$key->id}>{$key->sssn_name}</option>";
            }
        }
        return $sssnOptions;
    }


    // Get list of semseter options
    private function getOptions()
    {
        $sssns = \backend\models\StudentSemesterStatusName::find()
            ->where(['sssn_name' => ['Academic Dismissal', 'Defector', 'Discipline dismissal', 'Personal dropout', 'Probation 1', 'Probation 2']])
            ->orderBy('sssn_name ASC')
            ->all();
        //echo "WONDETOM";
        $sssnOptions = "<option value=''> - </option>";
        if ($sssns && count($sssns) > 0) {
            foreach ($sssns as $key) {
                $sssnOptions .= "<option value={$key->id}>{$key->sssn_name}</option>";
            }
        }
        return $sssnOptions;
    }

    // Get list of student under search key
    public function actionGetStudent()
    {
        $searchKey = isset($_POST['searchKey']) && !empty($_POST['searchKey']) ? $_POST['searchKey'] : die('Search Key Empity');
        $students = \backend\models\Student::find()->where(['like', 'std_regId', $searchKey])->all();
        $report = '';
        if ($students && count($students) > 0) {

            $cng = '';
            if (isset($_POST['wt'])) {
                $cng = "Change student academic profile";
            } else {
                $cng = "Change Student semester status";
            }
            $report .= "<table class='table table-striped table-condensed table-sm table-bordered'>";
            $report .= "<thead style='background-color:#00ce0f'>
                <th>#</th>
                <th>RegId</th>
                <th>Full Name</th>
                <th>Group</th>
                <th colspan=3 style='text-align:center;'>{$cng}</th>
            </thead><tbody>";
            $count = 0;
            $fcount = 0;
            foreach ($students as $key) {
                $user = \backend\models\User::findOne(['id' => $key['std_user_relId']]);

                // Student should have to be active semester student.
                $gset = CCF::currentStudentGroup($key);
                if (is_null($gset)) {
                    $fcount++;
                    continue;
                }

                // If student status already changed in this acadYear, pass the record.
                $requested = $this->checkIfRequested($key);
                if ($requested && count($requested) > 0) {
                    # code...
                    continue;
                }

                $option = "";
                if (isset($_POST['wt'])) {
                    $option = "
                    <button title='Change Student&apos;s Department' class='btn btn-warning btn-sm' onClick='change({$key->id},1)'>Department</button></td>
                    <td><button title='Change Student&apos;s Specilaization' class='btn btn-warning btn-sm' onClick='change({$key->id},2)'>Specialization</button></td>
                    <td><button title='Change Student&apos;s Sponsorship type' class='btn btn-warning btn-sm' onClick='change({$key->id},3)'>Sponsorship</button>";
                } else {
                    $option = "<select class='form-control' title='select action' onchange='changeStatus(" . $key['id'] . ",$(this).val())'>
                                        {$this->getOptions()}
                                    </select>";
                }
                $report .= "<tr>
                <td>" . ++$count . "</td>
                <td>" . $key['std_regId'] . "</td>
                <td>" . $user->getFullName() . "</td>
                <td>{$gset->gsGroupRel->group_name}</td>
                <td style='text-align:center;'>{$option}</td>
                </tr>";
                // $count++;
            }
            if ($count == 0 && $fcount != 0) {

                $report .= "<tr class='danger'>Student(s) for your seach are (is) not active students in this academic Year.</tr>";
            }
            $report .= "</tbody></table>";
            return $report;
        } else {
            echo "<p class='bg-danger'>No such student record found.</p>";
        }
        // echo "SEARCH__". $searchKey ;
    }

    private function checkIfRequested($key, $acadYearId = NULL, $semId = NULL)
    {
        if (!is_null($acadYearId) && !is_null($semId)) {
            return \backend\models\StudentSemesterStatusHistory::find()
                ->where([
                    'sssh_std_relId' => $key['id'],
                    'sssh_acadYear_relId' => $acadYearId,
                    'sssh_sem_relId' => $semId,
                ])
                ->andWhere(['OR', ['sssh_approved' => NULL], ['<>', 'sssh_approved', 0]])
                ->one();
        }
        $this->__constructor();
        return \backend\models\StudentSemesterStatusHistory::find()
            ->where([
                'sssh_std_relId' => $key['id'],
                'sssh_acadYear_relId' => $this->acadYearId,
                'sssh_sem_relId' => $this->semId,
            ])
            ->andWhere(['OR', ['sssh_approved' => NULL], ['<>', 'sssh_approved', 0]])
            ->one();
    }

    // get an approved requests for the registrar approval.
    public function actionGetStatusChangeRequests()
    {
        if (Yii::$app->user->can('Registrar')) {
            $this->__constructor();

            // 'sssh_acadYear_relId' => $this->acadYearId
            $result = \backend\models\StudentSemesterStatusHistory::find()
                ->andWhere(['sssh_approved' => NULL])
                ->all();
            $report = '';

            if ($result && count($result) > 0) {
                $report .= "<table style='font-size:10pt;' class='table table-striped table-condensed table-sm table-bordered table-hover'>";
                $report .= "<thead style='background-color:#00ce0f'>
                    <th>RegId</th>
                    <th>Full Name</th>
                    <th>Group</th>
                    <th>Sem.</th>
                    <th>Def. Status</th>
                    <th>New Status</th>
                    <th>Aprv. Status</th>
                    <th>Status Chng.</th>
                    </thead><tbody>";
                $count = 0;
                $fcount = 0;
                foreach ($result as $key) {
                    $student = \backend\models\Student::findOne(['id' => $key['sssh_std_relId']]);
                    $user = \backend\models\User::findOne(['id' => $student->std_user_relId]);

                    $statusOption = "<button class='btn btn-warning btn-xs' title='Change Status' onClick='changeStatusBack(" . $key['id'] . ")'>
                            Change
                        </button>";

                    $apprvStatus = is_null($key->sssh_approved) ? 'Pending' : ($key->sssh_approved == 1 ? 'Approved' : 'Rejected');

                    if (Yii::$app->user->can('Registrar')) {
                        if ($key->sssh_approved != 1) {
                            $statusOption = "<select class='' onchange='approveStatus({$key['id']},$(this).val())'>
                                    <option value=''> - </option>
                                    <option value='1'>Approve</option>
                                    <option value='0'>Reject</option>
                                </select>";
                        } else {
                            $statusOption = "<p class='bg-info'>already Approved, No further operation is available.</p>";
                        }
                    }
                    $gset = CCF::getStudentGroupHistoryPublic($key->sssh_clslvl_relId, $key->sssh_sem_relId, $student->id);

                    if (is_null($gset)) {
                        $fcount++;
                        continue;
                    }
                    $dstat = is_null($key->sssh_detault_sssn_relId) ? 'Pending' : \backend\models\StudentSemesterStatusName::findOne(['id' => $key['sssh_detault_sssn_relId']])->sssn_name;
                    $report .= "<tr>
                            <td>" . $student->std_regId . "</td>
                            <td>{$user->getFullName()}</td>
                            <td>{$gset->gsGroupRel->group_name}</td>
                            <td>{$gset->gsAcadYearRel->sch_year_desc}-{$gset->gsSemRel->semester_name}</td>
                            <td>{$dstat}</td>
                            <td>{$key->ssshSssnRel->sssn_name}</td>
                            <td>" . $apprvStatus . "</td>
                            <td>{$statusOption}</td>
                            </tr>";
                    $count++;
                }
                return $report . "</tbody></table>";
            } else {
                echo "<p class='bg-warning'>No un-approved status change request found.</p>";
            }
        } else {
            echo "<p class='bg-danger'>Nothing to display</p>";
        }
    }

    // Get Any change made status for this academic year.
    public function actionGetRecord()
    {
        $this->__constructor();
        // required semseter status. => 
        // die($_POST['acadYearId']);
        // $studentId = isset($_POST['studentId']) && !empty($_POST['studentId']) ? intval($_POST['studentId']) : die('Not selected Student');
        // $statusId = isset($_POST['statusId']) && !empty($_POST['statusId']) ? intval($_POST['statusId']) : die('Not selected Status');
        // $defStatId = isset($_POST['defStatId']) && !empty($_POST['defStatId']) ? intval($_POST['defStatId']) : die('<p class="bg-danger">Uknown Status, Such semester status is Uknow. UKNOW_SEMESTER_STATUS</p>');

        // acadYearId:acadYearId,
        // semId:semId,
        // admId:admId,
        // progId:progId,
        // deptId:deptId,
        // clslvlId:clslvlId,

        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : $this->acadYearId;
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : $this->semId;

        // $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : $this->semId;

        // $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : $this->semId;

        // $deptId = isset($_POST['deptId']) && !empty($_POST['deptId']) ? intval($_POST['deptId']) : $this->semId;

        if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {
            $result = NULL;
            $wreport = "";
            if (Yii::$app->user->can('Registrar')) {
                $result = \backend\models\StudentSemesterStatusHistory::find()
                    ->where(['sssh_acadYear_relId' => $acadYearId, 'sssh_sem_relId' => $semId])
                    ->andWhere(['IS NOT', 'sssh_approved', NULL])
                    ->all();
            } else {
                $result = \backend\models\StudentSemesterStatusHistory::find()
                    ->where(['sssh_acadYear_relId' => $acadYearId, 'sssh_sem_relId' => $semId])
                    ->all();
            }


            if ($result && count($result) > 0) {
                $wreport .= "<table style='font-size:10pt;' class='table table-striped table-condensed table-sm table-bordered table-hover'>";
                $wreport .= "<thead style='background-color:#00ce0f'>
                    <th>RegId</th>
                    <th>Full Name</th>
                    <th>Group</th>
                    <th>Acad. Yr</th>
                    <th>Sem.</th>
                    <th>Def. Status</th>
                    <th>New Status</th>
                    <th>Aprv. Status</th>
                    <th>Status Chng.</th>
                    </thead><tbody>";
                $count = 0;
                $fcount = 0;
                foreach ($result as $key) {
                    $student = \backend\models\Student::findOne(['id' => $key['sssh_std_relId']]);
                    $user = \backend\models\User::findOne(['id' => $student->std_user_relId]);

                    $statusOption = is_null($key->sssh_approved) ? "<button class='btn btn-warning btn-xs' title='Cancel status change request' onClick='changeStatusBack(" . $key['id'] . ")'>
                            Cancel
                        </button>" : "Completed";

                    $apprvStatus = is_null($key->sssh_approved) ? 'Pending' : ($key->sssh_approved == 1 ? 'Approved' : 'Rejected');

                    if (Yii::$app->user->can('Registrar')) {
                        if ($key->sssh_approved != 1) {
                            $statusOption = "<select class='' onchange='approveStatus({$key['id']},$(this).val())'>
                                    <option value=''> - </option>
                                    <option value='1'>Approve</option>
                                    <option value='0'>Reject</option>
                                </select>";
                        } else {
                            $statusOption = "<p class='bg-info'>Completed</p>";
                        }
                    }

                    // $student = 
                    // Student should have to be active semester student.
                    // $gset = CCF::currentStudentGroup($student);
                    $gset = CCF::getStudentGroupHistoryPublic($key->sssh_clslvl_relId, $key->sssh_sem_relId, $student->id);
                    if (is_null($gset)) {
                        $fcount++;
                        continue;
                    }

                    $grp = $gset->gsGroupRel->group_name;
                    $dstat = is_null($key->sssh_detault_sssn_relId) ? 'Pending' : $key->ssshDetaultSssnRel->sssn_name;
                    $wreport .= "<tr>
                            <td>{$student->std_regId}</td>
                            <td>" . $user->first_name . ' ' . $user->middle_name . "</td>
                            <td>{$grp}</td>
                            <td>{$key->ssshAcadYearRel->sch_year_desc}</td>
                            <td>{$key->ssshSemRel->semester_name}</td>
                            <td>{$dstat}</td>
                            <td>{$key->ssshSssnRel->sssn_name}</td>
                            <td>" . $apprvStatus . "</td>
                            <td>{$statusOption}</td>
                            </tr>";
                    $count++;
                }
                return $wreport . "</tbody></table>";
            } else {
                if (Yii::$app->user->can('Registrar')) {
                    echo "<p class='bg-warning'>No Result</p>";
                } else {

                    echo "<p class='bg-warning'>No record.</p>";
                }
                die();
            }
        }
    }

}
