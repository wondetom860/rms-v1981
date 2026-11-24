<?php

namespace frontend\controllers;

use backend\models\GradeScale;
use Yii;
use backend\models\WStudentWithdrawalHistory;
use backend\models\WStudentWithdrawalHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\RemovedCourse;
use \common\components\CommonControllerFunctions as CCF;
use \backend\models\ReadmissionCalculationControl;


/**
 * This is the model class for table "removed_course".
 *
 * @property integer $id
 * @property integer $rc_std_relId
 * @property integer $rc_acadYear_relId
 * @property integer $rc_sem_relIdF
 * @property integer $rc_course_relId
 * @property integer $rc_coff_relId
 * @property integer $rc_withdrawal_relId
 * @property integer $rc_removal_approved
 * @property integer $rc_romoval_approved_by
 * @property string $rc_removal_approved_at
 *
 * @property SchoolYear $rcAcadYearRel
 * @property CourseOffering2Detail $rcCoffRel
 * @property Course $rcCourseRel
 * @property Semester $rcSemRel
 * @property WStudentWithdrawalHistory $rcWithdrawalRel
 */
/**
 * WStudentWithdrawalHistoryController implements the CRUD actions for WStudentWithdrawalHistory model.
 */
class WStudentWithdrawalHistoryController extends Controller
{

    public $acadYearId;
    public $semId;
    public $gset;

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
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all WStudentWithdrawalHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WStudentWithdrawalHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetAllRequests()
    {
        $requests = "";
        $this->__constructor();
        if (Yii::$app->user->can('DepartmentHead')) {
            $instdept = \backend\models\Instructor::find()
                ->where(['inst_user_relId' => Yii::$app->user->identity->id])
                ->one()->inst_dep_relId;

            $requests = \backend\models\WStudentWithdrawalHistory::find()
                ->innerJoin('student', 'student.id = w_student_withdrawal_history.swh_std_relId')
                ->andWhere(['student.std_dept_relId' => $instdept])
                ->all();
        } else if (Yii::$app->user->can('Student')) {
            $std = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
            $requests = \backend\models\WStudentWithdrawalHistory::find()
                ->where([
                    'swh_std_relId' => $std->id,
                ])
                ->innerJoin('student', 'student.id = w_student_withdrawal_history.swh_std_relId')
                ->all();
        } else {
            $requests = \backend\models\WStudentWithdrawalHistory::find()
                ->innerJoin('student', 'student.id = w_student_withdrawal_history.swh_std_relId')
                ->all();
        }


        if ($requests && count($requests) > 0) {
            $repo = "";

            $repo = "<table class='table table-condensed table-hover table-striped table-bordered' style='font-size:9pt;'>
                        <caption>
                            <h4 style='text-align:center;'>Withdrawal Requests Report</h4>
                        </caption>
                        <thead style='background-color:#8D0;'>
                            <th>#</th>
                            <th>Acad. Year</th>
                            <th>Reg. ID</th>
                            <th>Student Name</th>
                            <th>Admission</th>
                            <th>Program</th>
                            <th>Department</th>
                            <th>Class level</th>
                            <th>Specialization</th>
                            <th>Withdrawal Reason</th>
                            <th>HOD Approval</th>
                            <th>Registrar Approval</th>
                            <th>Re-admitted</th>
                        </thead>
                        <tbody>";
            $count = 0;
            foreach ($requests as $key) {
                $std = \backend\models\Student::findOne($key->swh_std_relId);
                $user = \common\models\User1::findOne($std->std_user_relId);

                $gset = CCF::currentStudentGroup($std);

                if ($gset) {


                    // $user = \common\models\User1::findOne($userId);
                    $adm = \backend\models\Admission::findOne($gset->gs_adm_relId);
                    $prog = \backend\models\Program::findOne($gset->gs_prog_relId);
                    $dept = \backend\models\Department::findOne($gset->gs_dept_relId);
                    $spec = is_null($gset->gs_spec_relId) ? ' - ' : \backend\models\Specialization::findOne($gset->gs_spec_relId)->spec_name;
                    $clslvl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId);
                    $reason = \backend\models\WithdrawalReason::findone($key->swh_reason_relId)->withdrawalres_description;
                    $acadYear = \backend\models\SchoolYear::findOne($key->swh_acadYear_relId);
                    $semester = \backend\models\Semester::findOne($key->swh_sem_relId);
                } else {
                    $adm = \backend\models\Admission::findOne($std->std_admission_relId);
                    $prog = \backend\models\Program::findOne($std->std_prog_relId);
                    $dept = \backend\models\Department::findOne($std->std_dept_relId);
                    $spec = is_null($std->std_spec_relId) ? ' - ' : \backend\models\Specialization::findOne($std->std_spec_relId)->spec_name;
                    $clslvl = \backend\models\ClassLevel::findOne($std->std_clslvl_relId);
                    $reason = \backend\models\WithdrawalReason::findone($key->swh_reason_relId)->withdrawalres_description;
                    $acadYear = \backend\models\SchoolYear::findOne($key->swh_acadYear_relId);
                    $semester = \backend\models\Semester::findOne($key->swh_sem_relId);
                }

                $approval1 = NULL;
                $approval2 = NULL;
                $readmitted = 'N/A';

                if (is_null($key->swh_HOD_approved) && is_null($key->swh_approved) && is_null($key->swh_lastApproved)) {
                    $approval1 = $approval2 = 'Pending';
                    $readmitted = is_null($key->swh_returnedBack) ? 'NOT YET' : ($key->swh_returnedBack == 1 ? 'In-Process' : 'YES');
                } else {
                    if ($key->swh_HOD_approved == 0) {
                        $approval1 = 'Rejected';
                        $approval2 = 'Rejected By HoD';
                    } else if ($key->swh_approved == 0) {
                        $approval1 = 'Rejected By Registrar';
                        $approval2 = 'Rejected.';
                    } else {
                        $approval1 = 'Approved';
                        $approval2 = 'Approved';
                        $readmitted = is_null($key->swh_returnedBack) ? 'NOT YET' : ($key->swh_returnedBack == 1 ? 'In-Process' : 'YES');
                    }
                }

                if (!is_null($key->swh_returnedBack)) {
                    if (is_null($std->std_group_realId)) {
                        $readmitted = 'In-Process';
                    } else {
                        $readmitted = 'YES';
                    }
                }
                // $approved = "";
                // if (Yii::$app->user->can('DepartmentHead')) {
                //     $approved = is_null($key->swh_approved)?'Pending':($key->swh_approved == 1?'Approved':'Rejected');
                // }else if (Yii::$app->user->can('Registrar')) {
                //     $approved = is_null($key->swh_HOD_approved)?'Pending':($key->swh_HOD_approved == 1?'Approved':'Rejected');
                // }

                $repo .= "
                    <tr>
                        <td>" . ++$count . "</td>
                        <td>{$acadYear->sch_year_desc}-{$semester->semester_name}</td>
                        <td>{$std->std_regId}</td>
                        <td>{$user->first_name} {$user->middle_name} {$user->last_name}</td>
                        <td>{$adm->admission_name}</td>
                        <td>{$prog->program_name}</td>
                        <td>{$dept->dept_name}</td>
                        <td>{$clslvl->clslvl_num}</td>
                        <td>{$spec}</td>
                        <td>{$reason}</td>
                        <td>{$approval1}</td>
                        <td>{$approval2}</td>
                        <td>{$readmitted}</td>
                    </tr>                                
                    ";
            }
            $repo .= "</tbody>
                    </table>
                </div>

            </div>";
            echo "{$repo}";
            die();
        } else {
            echo "<p class='bg-danger'> No request found.</p>";
            die();
        }
    }

    public function actionGetApprovedRequests()
    {
        $requests = "";
        // $this->__constructor();
        if (Yii::$app->user->can('DepartmentHead')) {
            $instdept = \backend\models\Instructor::find()
                ->where(['inst_user_relId' => Yii::$app->user->identity->id])
                ->one()->inst_dep_relId;

            $requests = \backend\models\WStudentWithdrawalHistory::find()
                ->innerJoin('student', 'student.id = w_student_withdrawal_history.swh_std_relId')
                ->where(['student.std_dept_relId' => $instdept])
                ->all();
        } else if (Yii::$app->user->can('Student')) {
            $std = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
            $requests = \backend\models\WStudentWithdrawalHistory::find()
                ->where([
                    'swh_std_relId' => $std->id,
                ])
                ->all();
        } else {
            $requests = \backend\models\WStudentWithdrawalHistory::find()
                ->all();
        }


        if ($requests && count($requests) > 0) {
            $repo = "";

            $repo = "<table class='table table-condensed table-hover table-striped table-bordered' style='font-size:9pt;'>
                        <caption>
                            <h4 style='text-align:center;'>Withdrawal requests</h4>
                        </caption>
                        <thead style='background-color:#8D0;'>
                            <th>#</th>
                            <th>Reg. ID</th>
                            <th>Acad. Year</th>
                            <th>Student Name</th>
                            <th>Program</th>
                            <th>Dept.</th>
                            <th>Year</th>
                            <th>Specialization</th>
                            <th>Withdrawal Reason</th>
                            <th>Course Rmvd</th>
                            <th>HOD Approval</th>
                            <th>Registrar Approval</th>
                            <th>Re-admitted</th>
                            <th>Report</th>
                        </thead>
                        <tbody>";
            $count = 0;
            foreach ($requests as $key) {
                $std = \backend\models\Student::findOne($key->swh_std_relId);
                $user = \common\models\User1::findOne($std->std_user_relId);

                // CCF::currentStudentGroup($std)
                $gset = NULL;

                if ($gset) {


                    // $user = \common\models\User1::findOne($userId);
                    $adm = \backend\models\Admission::findOne($gset->gs_adm_relId);
                    $prog = \backend\models\Program::findOne($gset->gs_prog_relId);
                    $dept = \backend\models\Department::findOne($gset->gs_dept_relId);
                    $spec = is_null($gset->gs_spec_relId) ? ' - ' : \backend\models\Specialization::findOne($gset->gs_spec_relId)->spec_name;
                    $clslvl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId);
                    $reason = \backend\models\WithdrawalReason::findone($key->swh_reason_relId)->withdrawalres_description;
                } else {
                    $adm = \backend\models\Admission::findOne($std->std_admission_relId);
                    $prog = \backend\models\Program::findOne($std->std_prog_relId);
                    $dept = \backend\models\Department::findOne($std->std_dept_relId);
                    $spec = is_null($std->std_spec_relId) ? ' - ' : \backend\models\Specialization::findOne($std->std_spec_relId)->spec_name;
                    $clslvl = \backend\models\ClassLevel::findOne($std->std_clslvl_relId);
                    $reason = \backend\models\WithdrawalReason::findone($key->swh_reason_relId)->withdrawalres_description;
                    $acadYear = \backend\models\SchoolYear::findOne($key->swh_acadYear_relId);
                    $semester = \backend\models\Semester::findOne($key->swh_sem_relId);
                }

                $approval1 = NULL;
                $approval2 = NULL;
                $readmitted = 'N/A';
                $btn33 = "";

                $rmvd = 0;

                $rmvd = $key->swh_course_removed == 1 ? (\backend\models\RemovedCourse::find()->where(['rc_withdrawal_relId' => $key->id])->count()) : 0;

                if (is_null($key->swh_HOD_approved) && is_null($key->swh_approved) && is_null($key->swh_lastApproved)) {
                    $approval1 = $approval2 = 'Pending';
                    $readmitted = is_null($key->swh_returnedBack) ? 'NOT YET' : ($key->swh_returnedBack == 1 ? 'In-Process' : 'YES');
                } else {

                    $approval1 = is_null($key->swh_HOD_approved) ? 'Pending' : ($key->swh_HOD_approved == 0 ? 'Rejected' : 'Approved');
                    $approval2 = is_null($key->swh_approved) ? 'Pending' : ($key->swh_approved == 0 ? 'Rejected' : 'Approved');
                    $readmitted = is_null($key->swh_returnedBack) ? 'NOT YET' : ($key->swh_returnedBack == 1 ? 'In-Process' : 'YES');
                }

                if (is_null($key->swh_stdSigned)) {
                    $readmitted = "Waiting for Student withdrawal process.";
                } else {
                    if (is_null($key->swh_approved)) { //registrar approval attribute 
                        $readmitted = "Waiting for Registrar Last approve.";
                    } else {
                        if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Student')) {
                            $btn33 = "<button class='btn btn-link' target='_blank' onclick='generateReport({$key->id})' title='Generate repaort'>Report</button>";
                        }
                    }
                }

                if (!is_null($key->swh_returnedBack)) {
                    if (is_null($std->std_group_realId)) {
                        $readmitted = 'In-Process';
                    } else {
                        $readmitted = 'YES';
                    }
                }

                // $approved = "";
                // if (Yii::$app->user->can('DepartmentHead')) {
                //     $approved = is_null($key->swh_approved)?'Pending':($key->swh_approved == 1?'Approved':'Rejected');
                // }else if (Yii::$app->user->can('Registrar')) {
                //     $approved = is_null($key->swh_HOD_approved)?'Pending':($key->swh_HOD_approved == 1?'Approved':'Rejected');
                // }

                $cll = $clslvl->clslvl_num == 1 ? "1<sup>st</sup>" : ($clslvl->clslvl_num == 2 ? "2<sup>nd</sup>" : ($clslvl->clslvl_num == 3 ? "3<sup>rd</sup>" : $clslvl->clslvl_num . '<sup>th</sup>'));
                $repo .= "
                    <tr>
                        <td>" . ++$count . "</td>
                        <td>{$std->std_regId}</td>
                        <td>{$key->swhAcadYearRel->sch_year_desc}-{$key->swhSemRel->semester_name}</td>
                        <td>{$user->first_name} {$user->middle_name} {$user->last_name}</td>
                        <td><b>{$adm->admission_name[0]}{$prog->program_name[0]}</b></td>
                        <td><b>{$dept->dept_code}</b></td>
                        <td>{$cll}Yr.</td>
                        <td>{$spec}</td>
                        <td>{$reason}</td>
                        <td style='text-align:center;'>{$rmvd}</td>
                        <td>{$approval1}</td>
                        <td>{$approval2}</td>
                        <td>{$readmitted}</td>
                        <td>{$btn33}</td>
                    </tr>                                
                    ";
                // }else{
                //     continue;
                // }
            }
            $repo .= "</tbody>
                    </table>
                </div>

            </div>";
            echo "{$repo}";
            die();
        } else {
            echo "<p class='bg-danger'> No request found.</p>";
            die();
        }
    }

    // privaye

    private function removeCourse($req, $transaction, $err)
    {
        // var_dump("Deleting....00");
        $RemovedCourse = \backend\models\RemovedCourse::find()->where(['rc_withdrawal_relId' => $req->id])->all();
        if ($RemovedCourse && count($RemovedCourse) > 0) {
            $ccs = [];
            $cfs = [];
            $cids = [];
            $message = "";
            foreach ($RemovedCourse as $key) {
                $ccs[] = $key->rc_course_relId;
                $cfs[] = $key->rc_coff_relId;
                $cids[] = $key->id;
            }

            $regExists = \frontend\models\SemesterRegistration2Detail::find()
                ->where([
                    'srd2_course_relId' => $ccs,
                    'srd2_coff_relId' => $cfs,
                ])
                ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                ->andWhere([
                    'semester_registration_2.semreg_std_relId' => $req->swh_std_relId,
                    'semester_registration_2.semreg_acadYear_relId' => $RemovedCourse[0]->rc_acadYear_relId,
                    'semester_registration_2.semreg_semester_relId' => $RemovedCourse[0]->rc_sem_relId,
                ])
                ->all();

            if ($regExists && count($regExists) > 0) {
                $semregId = $regExists[0]['srd2_semreg_relId'];
                // if there is any continous assessment entry related with registration entry, remove all
                // check if any countinous assesment record is found.
                $contAss = new \backend\models\ContinuousAssessment2();
                $contAss->deleteContAsssEntry($semregId);

                foreach ($regExists as $key3) {
                    \frontend\models\SemesterRegistration2Detail::findOne($key3->id)->delete();
                }

                \frontend\models\SemesterRegistration2::findOne($semregId)->delete();
                $resultUpdate = Yii::$app->db->createCommand()
                    ->update(
                        'removed_course',
                        [
                            'rc_removal_approved' => 1,
                            'rc_removal_approved_at' => date('Y-m-d h:i:s'),
                            'rc_romoval_approved_by' => Yii::$app->user->identity->id
                        ],
                        [
                            'id' => $cids,
                        ]
                    )
                    ->execute();
            }

            if ($err == 1) {
                $transaction->rollBack();
                echo "<p class='bg-danger'>Error occured while updating the request. Error with courses enrolled. {$message}</p>";
                die();
            } else {
                $this->updateProfile($req, $transaction);
            }
        } else {
            $transaction->rollBack();
            echo "<p class='bg-danger'>Error in request, Course removal status is set, but no course to remove. Please notify to correct it.</p>";
            die();
        }
    }

    public function actionApproveRequestLast()
    {
        $reqId = isset($_POST['reqId']) && !empty($_POST['reqId']) ? intval($_POST['reqId']) : die('Invalid request, please refine.');
        $action = isset($_POST['action']) && !empty($_POST['action']) ? intval($_POST['action']) : die('Invalid status change, please refine.');

        $action = $action == 2 ? 0 : $action;

        $message = "";

        // $this->__constructor();
        $req = \backend\models\WStudentWithdrawalHistory::findOne($reqId);
        $err = 0;

        if ($req) {
            $transaction = Yii::$app->db->beginTransaction();
            if (Yii::$app->user->can('Registrar')) {
                $result = Yii::$app->db->createCommand()
                    ->update(
                        'w_student_withdrawal_history',
                        [
                            'swh_lastApproved' => $action,
                            'swh_lastApproved_at' => date('Y-m-d h:i:s'),
                            'swh_lastApproved_by' => Yii::$app->user->identity->id,
                        ],
                        [
                            'id' => $reqId,
                        ]
                    )
                    ->execute();
                if ($result && $req->swh_stdSigned == 1) { //last approved case.
                    if ($req->swh_course_removed == 1 && $action == 1) {
                        // remove courses
                        $this->removeCourse($req, $transaction, $err);
                    } else if ($req->swh_igraded_srd2s && $action == 1) {
                        // change enrolled course grades to I:
                        $resp = $req->iGradeSrd2s();
                        if ($resp[0]) {
                            $this->updateProfile($req, $transaction);
                            // $transaction->commit();
                            Yii::$app->session->setFlash('success', 'Success: ' . $resp[1]);
                            return 1;
                        } else {
                            // $transaction->rollBack();
                            // Yii::$app->session->setFlash('success','Success: '.$resp[1]);
                            return $resp[1];
                        }
                    } else {
                        if ($req->swh_stdSigned == 1 && $req->swh_course_removed == 0 && $action == 1) {
                            $this->updateProfile($req, $transaction);
                        } else if ($action == 0) {
                            echo $req->delete();
                        }

                        return 1;
                    }
                } else {
                    // approve only status change.
                    $transaction->commit();
                    echo 1;
                    die();
                }
            } else {
                echo "<p class='bg-danger'>Impossible operation!! (::)</p>";
                die();
            }
        }
    }

    public function actionApproveRequest()
    {
        $reqId = isset($_POST['reqId']) && !empty($_POST['reqId']) ? intval($_POST['reqId']) : die('Invalid request, please refine.');
        $action = isset($_POST['action']) && !empty($_POST['action']) ? intval($_POST['action']) : die('Invalid status cahnge, please refine.');

        $action = $action == 2 ? 0 : $action;

        $message = "";

        // $this->__constructor();
        $req = \backend\models\WStudentWithdrawalHistory::findOne($reqId);
        $err = 0;

        if ($req) {
            $transaction = Yii::$app->db->beginTransaction();
            if (Yii::$app->user->can('DepartmentHead')) {
                $result = Yii::$app->db->createCommand()
                    ->update(
                        'w_student_withdrawal_history',
                        [
                            'swh_HOD_approved' => $action,
                            'swh_HOD_approved_at' => date('Y-m-d h:i:s'),
                            'swh_HOD_approved_by' => Yii::$app->user->identity->id,
                        ],
                        [
                            'id' => $reqId,
                        ]
                    )
                    ->execute();
                if ($result) {
                    $transaction->commit();
                    echo 1;
                    die();
                } else {
                    echo "<p class='bg-danger'>Error occured while approving the request. Please retry later.</p>";
                    die();
                }
            } else if (Yii::$app->user->can('Registrar')) {
                $result = Yii::$app->db->createCommand()
                    ->update(
                        'w_student_withdrawal_history',
                        [
                            'swh_approved' => $action,
                            'swh_approved_at' => date('Y-m-d h:i:s'),
                            'swh_approved_by' => Yii::$app->user->identity->id,
                        ],
                        [
                            'id' => $reqId,
                        ]
                    )
                    ->execute();
                if ($req->swh_lastApproved == 1 && $req->swh_stdSigned == 1) { //last approved case.
                    if ($req->swh_course_removed == 1 && $action == 1) {
                        $this->removeCourse($req, $transaction, $err);
                    } else {
                        if ($req->swh_stdSigned == 1 && $req->swh_course_removed == 0 && $action == 1) {
                            $this->updateProfile($req, $transaction);
                        } else if ($action == 0) {
                            echo $req->delete();
                        }
                    }
                } else {
                    // approve only status change.
                    $transaction->commit();
                    echo 1;
                    die();
                }
            } else {
                echo "<p class='bg-danger'>Impossible operation!! (::)</p>";
                die();
            }
        }
    }

    private function updateProfile($req, $transaction)
    {

        // update student semester status.
        // $result4 = Yii::$app->db->createCommand()
        //     ->update('student',
        //         ['std_semester_status'=>],
        //         ['id'=>$req->swh_std_relId])
        //     ->execute();
        $student = $req->swhStdRel;
        $student->std_semester_status = 'WITHDRAWN';
        if ($student->update(false) || $student->std_semester_status == 'WITHDRAWN') {
            $transaction->commit();
            return 1;
        } else {
            var_dump($student->getErrors());
            $transaction->rollBack();
            echo "<p class='bg-danger'>Failed!, Error while updating student semester status. Please re-try later.</p>";
            return;
        }
        // $resulr = \backend\models\WGroupAssignment::find()
        //     ->innerJoin('w_group_setup','w_group_setup.id = w_group_assignment.gas_gset_relId')
        //     ->where(['gs_acadYear_relId'=>$req->swh_acadYear_relId,
        //         'gs_sem_relId'=>$req->swh_sem_relId])
        //     ->andWhere(['gas_std_relId'=>$req->swh_std_relId])
        //     ->one()
        //     ->delete();
        // if ($result4) {
        //     $transaction->commit();
        //     echo 1;
        //     die();
        // }else{
        //     var_dump($result4);
        //     $transaction->rollBack();
        //     echo "<p class='bg-danger'>Failed!, Error while updating student semester status. Please re-try later.</p>";
        //     die();
        // }
    }

    public function actionGetRequests()
    {
        if (Yii::$app->user->can('DepartmentHead') || Yii::$app->user->can('Registrar')) {
            $requests = "";
            // $this->__constructor();
            if (Yii::$app->user->can('DepartmentHead')) {
                $instdept = \backend\models\Instructor::find()
                    ->where(['inst_user_relId' => Yii::$app->user->identity->id])
                    ->one()->inst_dep_relId;

                $requests = \backend\models\WStudentWithdrawalHistory::find()
                    ->where([
                        'swh_stdSigned' => 1,
                        'swh_approved' => NULL,
                        'swh_HOD_approved' => NULL,
                    ])
                    ->innerJoin('student', 'student.id = w_student_withdrawal_history.swh_std_relId')
                    ->andWhere(['student.std_dept_relId' => $instdept])
                    ->all();
            } else {
                $requests = \backend\models\WStudentWithdrawalHistory::find()
                    ->where(['swh_HOD_approved' => 1])
                    ->andWhere(['OR', ['swh_approved' => NULL], ['swh_lastApproved' => NULL]])
                    ->innerJoin('student', 'student.id = w_student_withdrawal_history.swh_std_relId')
                    ->all();
            }


            if ($requests && count($requests) > 0) {
                $repo = "";

                $repo = "<table class='table table-condensed table-hover table-striped table-bordered' style='font-size:9pt;'>
                            <caption>
                                <h4 style='text-align:center;'>Withdrawal requests - Approval pending requests</h4>
                            </caption>
                            <thead style='background-color:#8D0;'>
                                <th>#</th>
                                <th>Reg. ID</th>
                                <th>Student Name</th>
                                <th>Program</th>
                                <th>Department</th>
                                <th>Class level</th>
                                <th>Specialization</th>
                                <th>Withdrawal Reason</th>
                                <th>Courses removed</th>";
                $repo .= Yii::$app->user->can('Registrar') ? '<th>HoD approval Status</th>' : '<th>Registrar Approval Status</th>';

                $repo .= "
                                <th colspan=2>Approve</th>
                                <th>Last Approve</th>
                            </thead>
                            <tbody>";
                $count = 0;
                foreach ($requests as $key) {
                    $std = \backend\models\Student::findOne($key->swh_std_relId);
                    $user = \common\models\User1::findOne($std->std_user_relId);

                    $gset = $std->stdGroupReal;

                    if ($gset) {


                        // $user = \common\models\User1::findOne($userId);
                        $adm = \backend\models\Admission::findOne($gset->gs_adm_relId);
                        $prog = \backend\models\Program::findOne($gset->gs_prog_relId);
                        $dept = \backend\models\Department::findOne($gset->gs_dept_relId);
                        $spec = is_null($gset->gs_spec_relId) ? ' - ' : \backend\models\Specialization::findOne($gset->gs_spec_relId)->spec_name;
                        $clslvl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId);
                        $reason = \backend\models\WithdrawalReason::findone($key->swh_reason_relId)->withdrawalres_description;


                        $css = "NONE";

                        if ($key->swh_course_removed == 1) {
                            $rCourses = \backend\models\RemovedCourse::find()->where(['rc_withdrawal_relId' => $key->id])->all();

                            if ($rCourses && count($rCourses) > 0) {
                                $css = "";
                                foreach ($rCourses as $key2) {
                                    $course = \backend\models\Course::findOne($key2->rc_course_relId);
                                    $css .= "[{$course->course_code} => {$course->course_title}]<br>";
                                }
                            }
                        }

                        $btn = "<button class='btn btn-success btn-xs' onclick='approveRequest($key->id,1)'>Yes</button>";
                        $btn2 = "<button class='btn btn-warning btn-xs' onclick='approveRequest($key->id,0)'>No</button>";

                        $approved = "";
                        if (Yii::$app->user->can('DepartmentHead')) {
                            $approved = is_null($key->swh_approved) ? 'Pending' : ($key->swh_approved == 1 ? 'Approved' : 'Rejected');
                        } else if (Yii::$app->user->can('Registrar')) {
                            $approved = is_null($key->swh_HOD_approved) ? 'Pending' : ($key->swh_HOD_approved == 1 ? 'Approved' : 'Rejected');
                        }

                        $btnLast = NULL;
                        if ($key->swh_HOD_approved == 1 && $key->swh_approved == 1 && is_null($key->swh_lastApproved)) {
                            if ($key->swh_stdSigned == 1) {
                                $btnLast = "<button class='btn btn-success btn-xs' onclick='approveRequestLast($key->id,1)'>Yes</button>";
                            } else {
                                $btnLast = "Waiting for Student process";
                            }

                            $btn = NULL;
                            $btn2 = NULL;
                        }

                        // else if ($key->swh_HOD_approved == 1 && $key->swh_approved == 0) {
                        //     $btnLast ="";
                        // }

                        $repo .= "
                            <tr>
                                <td>" . ++$count . "</td>
                                <td>{$std->std_regId}</td>
                                <td>{$user->first_name} {$user->middle_name} {$user->last_name}</td>
                                <td><b>{$adm->admission_name[0]} {$prog->program_name[0]}</b></td>
                                <td>{$dept->dept_name}</td>
                                <td>{$clslvl->clslvl_num}</td>
                                <td>{$spec}</td>
                                <td>{$reason}</td>
                                <td style='background-color:#DDFF00; padding:5px;'>{$css}</td>
                                <td style='vertical-align:middle;'>{$approved}</td>
                                <td style='vertical-align:middle;'>{$btn}</td>
                                <td style='vertical-align:middle;'>{$btn2}</td>
                                <td style='vertical-align:middle;'>{$btnLast}</td>
                            </tr>                                
                            ";
                    } else {
                        continue;
                    }
                }
                $repo .= "</tbody>
                        </table>
                    </div>

                </div>";
                echo "{$repo}";
                die();
            } else {
                echo "<p class='bg-danger'> No request found.</p>";
                die();
            }
        } else {
        }
    }

    /**
     * Displays a single WStudentWithdrawalHistory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new WStudentWithdrawalHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WStudentWithdrawalHistory();

        if ($model->load(Yii::$app->request->post())) {

            $this->__constructor();

            $std = \backend\models\Student::find()->where(['std_user_relId' => Yii::$app->user->identity->id])->one();

            $model->swh_std_relId = $std->id;
            $model->swh_acadYear_relId = $this->acadYearId;
            $model->swh_sem_relId = $this->semId;

            $model->swh_created_by = Yii::$app->user->identity->id;
            $model->swh_created_at = date('Y-m-d h:i:s');

            $transaction = Yii::$app->db->beginTransaction();
            $message = "";

            // get student current group.
            $gset = CCF::currentStudentGroup($std);
            if ($gset) {
                // semregDet
                $courses = CCF::getStudentGroupCoursesP($gset->id, $std->id, $this->semId, $gset->gs_clslvl_relId);

                // echo "<pre>";
                // print_r($courses);die();


                if ($courses && count($courses) > 0) {
                    $model->swh_course_removed = 1;
                    if ($model->save()) {

                        foreach ($courses as $key) {
                            $rc = new RemovedCourse();
                            $rc->rc_acadYear_relId = $model->swh_acadYear_relId;
                            $rc->rc_sem_relId = $this->semId;
                            $rc->rc_std_relId = $std->id;
                            $rc->rc_withdrawal_relId = $model->id;
                            $rc->rc_course_relId = $key->srd2_course_relId;
                            $rc->rc_coff_relId = $key->srd2_coff_relId;

                            $rc->rc_grade = is_null($key->srd2_grade_relId) ? NULL : \backend\models\GradeScale::findOne($key->srd2_grade_relId)->gs_ltr_grade;

                            if (!$rc->save()) {

                                $message = "Error while buck_uping course enrollment information. not all courses bucked up.";
                                // var_dump($rc->getErrors());die();
                            }
                            unset($rc);
                        }
                        $transaction->commit();
                        return $this->redirect(['/request-withdrawal/index'])->send();
                    } else {
                        print_r('No course enrolled in this ssemester');
                        die();
                    }
                    // $transaction->commit();
                    // echo 1;
                }
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('index', [
                    'model' => $model,
                ]);
            }
        }
    }

    protected function checkIfReAdmittable($model)
    {
        // return true;


        // get all courses enrolled.

        $std = \backend\models\Student::findOne($model->swh_std_relId);

        // get group

        $gset = CCF::currentStudentGroup($std);

        // get acc course emrolled by the student

        // check if grade submission completed.
        $completed = CCF::checkIfGradeSubmissionCompletedP($gset->id, $gset->gs_clslvl_relId, $gset->gs_sem_relId, $model->swh_std_relId);

        if ($completed == 0) {
            $srd2s = CCF::getStudentGroupCoursesP($gset->id, $model->swh_std_relId, $gset->gs_sem_relId, $gset->gs_clslvl_relId);

            $totalCrdtHour = 0;
            $totalGradePoint = 0;
            $SGPA = 0.00;
            $removedCourseList = json_decode($model->removedCourseList);
            // loop over each course and its corresponding grade.
            foreach ($srd2s as $key) {
                $course = \backend\models\Course::findOne($key->srd2_course_relId);

                if (in_array($course->id, $removedCourseList)) {
                    $totalGradePoint += 0;
                } else {
                    $grade = \backend\models\GradeScale::findOne($key->srd2_grade_relId)->gs_gradePoint;
                    $totalGradePoint += ($grade * $course->course_crdthr);
                }

                $totalCrdtHour += $course->course_crdthr;
            }
            return [$totalGradePoint, $totalCrdtHour];
        } else {
            // grade submission not completed.
            echo 'Grade submission not completed';
            return false;
        }
        // $gsetId,$studentId,$semesterId,$clslvlId
    }

    private function getPrevCGPA($gset, $student)
    {
        $clslvl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId)->clslvl_num;
        $sem = \backend\models\Semester::findOne($gset->gs_sem_relId)->semester_name;
        $prevSem = '';
        $prevClslvl = 9999;
        if ($sem == 'I') {
            $prevSem = 'II';
            $prevClslvl = $clslvl - 1;
        } else {
            $prevSem = 'I';
            $prevClslvl = $clslvl;
        }

        if ($prevClslvl >= 1) {
            $prevTotalGrpt = 0;
            $prevTotalCrdtHr = 0;

            $semId = \backend\models\Semester::find()->where(['semester_name' => $prevSem])->one()->id;
            $clsId = \backend\models\ClassLevel::find()->where(['clslvl_num' => $prevClslvl])->one()->id;

            $semreg = \frontend\models\SemesterRegistration2::find()
                ->where([
                    'semreg_std_relId' => $student->id,
                    'semreg_semester_relId' => $semId,
                    'semreg_clslvl_relId' => $clsId,
                    'semreg_gradeCompleted' => 1,
                ])
                ->one();
            if ($semreg && count($semreg) > 0) {
                return [$semreg->semreg_totalCrdtHr, ($semreg->semreg_CGPA * $semreg->semreg_totalCrdtHr)];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    private function fGrdaed($ECkey, $fgrade)
    {
        return \frontend\models\SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'semreg_std_relId' => $ECkey->srd2SemregRel->semreg_std_relId,
                'srd2_course_relId' => $ECkey->srd2_course_relId,
                'srd2_grade_relId' => $fgrade->id
            ])
            ->andWhere(['<>', 'semester_registration_2_detail.id', $ECkey->id])
            ->count() > 0;
    }

    private function calculateSemGrade($enrolledCourses)
    {
        $semMark = NULL;
        if ($enrolledCourses && count($enrolledCourses) > 0) {
            $totalCredt = 0;
            $totalGpoint = 0;
            $fgrade = \backend\models\GradeScale::findOne(['gs_ltr_grade' => 'F']);
            foreach ($enrolledCourses as $ECkey) {
                // check repeated and F graded course
                $gradeObtained = is_null($ECkey->srd2_grade_relId) ? $fgrade : ($ECkey->srd2GradeRel->gs_valid == 1 ? $ECkey->srd2GradeRel : $fgrade);
                $course = $ECkey->srd2CourseRel;
                if (!$this->fGrdaed($ECkey, $fgrade)) { //check if the course is F graded before.
                    $totalCredt += $course->course_crdthr;
                }

                $totalGpoint += $course->course_crdthr * $gradeObtained->gs_gradePoint;
            }

            return $totalGpoint / $totalCredt;
        }

        return NULL;
    }

    private function calculateSemGradeCGPA($enrolledCourses)
    {
        $semMark = NULL;
        if ($enrolledCourses && count($enrolledCourses) > 0) {
            $totalCredt = 0;
            $totalGpoint = 0;
            $fgrade = \backend\models\GradeScale::findOne(['gs_ltr_grade' => 'F']);
            foreach ($enrolledCourses as $ECkey) {
                // check repeated and F graded course
                $gradeObtained = is_null($ECkey->srd2_grade_relId) ? $fgrade : ($ECkey->srd2GradeRel->gs_valid == 1 ? $ECkey->srd2GradeRel : $fgrade);
                $course = $ECkey->srd2CourseRel;
                if (!$this->fGrdaed($ECkey, $fgrade)) { //check if the course is F graded before.
                    $totalCredt += $course->course_crdthr;
                }

                $totalGpoint += $course->course_crdthr * $gradeObtained->gs_gradePoint;
            }

            return $totalGpoint / $totalCredt;
        }

        return NULL;
    }

    public function actionCreate2()
    {
        $model = new WStudentWithdrawalHistory();
        $semregModel = new \frontend\models\SemesterRegistration2Detail();

        if ($model->load(Yii::$app->request->post())) {

            // $this->__constructor();

            if (is_null($model->swh_std_relId)) {
                Yii::$app->session->setFlash('message', "Student information Uknown.");
                return $this->redirect('/request-withdrawal/request-hod')->send();
            }

            $std = \backend\models\Student::findOne($model->swh_std_relId);
            $reasonType = \backend\models\WithdrawalReason::findOne($model->swh_reason_relId);

            //get all the courses enrolled by the student.
            $enrolledCourses = \frontend\models\SemesterRegistration2Detail::find()
                ->where(['id' => explode(',', $model->removedCourseList)])
                ->andWhere(['IS NOT', 'srd2_grade_relId', NULL])
                ->all();

            $gset = \backend\models\WGroupSetup::findOne($model->gsetId);

            // var_dump($model->gsetId);die();

            if ($enrolledCourses && count($enrolledCourses) > 0) {
                $semRegObj = $enrolledCourses[0]->srd2SemregRel;
                $prevGset = $gset;
                $mappedClslvl = CCF::mapClassLevelReadmission($prevGset->gsClslvlRel, $prevGset->gsSemRel);
                if ($semRegObj->semreg_gradeCompleted == 1) {
                    $SGPA = $semRegObj->semreg_SGPA;
                    $CGPA = $semRegObj->semreg_CGPA;

                    if (CCF::specialCaseCheckP($prevGset)) {
                        $control = ReadmissionCalculationControl::find()
                            ->where([
                                'sssccd_adm_relId' => $prevGset->gs_adm_relId,
                                'sssccd_prog_relId' => $prevGset->gs_prog_relId,
                                'sssccd_sem_relId' => $mappedClslvl[1]->id,
                                'sssccd_clslvl_relId' => $mappedClslvl[0]->id,
                            ])
                            ->andWhere(['>=', 'sssccd_max_SGPA', $SGPA])
                            ->andWhere(['<=', 'sssccd_min_SGPA', $SGPA])
                            ->one();
                    } else {
                        $control = ReadmissionCalculationControl::find()
                            ->where([
                                'sssccd_adm_relId' => $prevGset->gs_adm_relId,
                                'sssccd_prog_relId' => $prevGset->gs_prog_relId,
                                'sssccd_sem_relId' => $mappedClslvl[1]->id,
                                'sssccd_clslvl_relId' => $mappedClslvl[0]->id,
                            ])
                            ->andWhere(['>=', 'sssccd_max_SGPA', $SGPA])
                            ->andWhere(['<=', 'sssccd_min_SGPA', $SGPA])
                            ->andWhere(['>=', 'sssccd_max_CGPA', $CGPA])
                            ->andWhere(['<=', 'sssccd_min_CGPA', $CGPA])
                            ->one();
                    }
                    if ($control && count($control) > 0) {
                        // $std = \backend\models\Student::findOne($model->swh_std_relId);

                        $model->swh_std_relId = $std->id;
                        $model->swh_acadYear_relId = $gset->gs_acadYear_relId;
                        $model->swh_sem_relId = $gset->gs_sem_relId;

                        $model->swh_created_by = Yii::$app->user->identity->id;
                        $model->swh_created_at = date('Y-m-d h:i:s');
                        $model->swh_course_removed = 0;

                        $model->swh_stdSigned = NULL;
                        $model->swh_HOD_approved = 1;
                        $model->swh_HOD_approved_at = date("Y-m-d h:i:s");
                        $model->swh_HOD_approved_by = Yii::$app->user->identity->id;
                        $model->swh_lastApproved = NULL;

                        if ($model->save()) {
                            Yii::$app->session->setFlash('message', "Report form application successfully completed.");
                            return $this->redirect('/request-withdrawal/request-hod')->send();
                        } else {
                            var_dump($model->getErrors());
                            die();
                        }
                    } else {
                        Yii::$app->session->setFlash('message', "Minimum requirement for readmission is NOT satisfied. CGPA= {$CGPA} and SGPA = {$SGPA}");
                        return $this->redirect('/request-withdrawal/request-hod')->send();
                    }
                } else {

                    // swh_igraded_srd2s
                    $model->swh_std_relId = $std->id;
                    $model->swh_acadYear_relId = $gset->gs_acadYear_relId;
                    $model->swh_sem_relId = $gset->gs_sem_relId;

                    $model->swh_created_by = Yii::$app->user->identity->id;
                    $model->swh_created_at = date('Y-m-d h:i:s');
                    $model->swh_course_removed = 0;

                    $model->swh_stdSigned = NULL;
                    $model->swh_igraded_srd2s = $model->removedCourseList;
                    $model->swh_HOD_approved = 1;
                    $model->swh_HOD_approved_at = date("Y-m-d h:i:s");
                    $model->swh_HOD_approved_by = Yii::$app->user->identity->id;
                    $model->swh_lastApproved = NULL;

                    if ($model->save()) {
                        Yii::$app->session->setFlash('success', "Report form application successfully completed.");
                        return $this->redirect('/request-withdrawal/request-hod')->send();
                    } else {
                        var_dump($model->getErrors());
                        die();
                    }

                    // // change all non-graded course enrollments to I
                    // // explode(',', $model->removedCourseList) 
                    // // $iGrade = GradeScale::getIGrade();
                    // // if($iGrade){
                    // //     $uodate = Yii::$app->db->createCommand()
                    // //         ->update('semester_registration_2_detail',
                    // //             [
                    // //                 'srd2_grade_relId' => $iGrade
                    // //             ],
                    // //             [
                    // //                 'id' => explode(',', $model->removedCourseList),
                    // //                 'srd2_grade_relId' => NULL
                    // //                 ])
                    // //         ->execute();
                    // // }
                    // Yii::$app->session->setFlash('message', "Grade submission NOT completed");
                    // Yii::$app->session->setFlash('type', "error");
                    // return $this->redirect('/request-withdrawal/request-hod')->send();
                }
            } else {
                // for the case of illegal or diciplinary dismissal, all courses enroled in the active semester are removed.

                Yii::$app->session->setFlash('message', "Course Not Enrolled / Grade submission NOT completed yet.");
                return $this->redirect('/request-withdrawal/request-hod')->send();
            }

            // $semGrade = $semregModel->getCGPAStatusP($std,$gset,NULL);
            // $SGPA = $this->calculateSemGrade($enrolledCourses);
            // // explode(',', $model->removedCourseList);
            // echo "<pre>";
            // var_dump($SGPA);
            // var_dump($semGrade[2]);
            // // var_dump(json_decode($model->removedCourseList));
            // die();


            // if ($reasonType->withdrawalres_type != 'NORMAL') {
            //     $status = $this->checkIfReAdmittable($model);
            //     if(is_bool($status) && $status == true) {

            //     }else if(is_array($status)){

            //         $student = \backend\models\Student::findOne($model->swh_std_relId);

            //         $gset = CCF::currentStudentGroup($student);
            //         $statusName = 'Readmission';

            //         if ($gset) {
            //             $statusInterval = \backend\models\StdStatusCalculationControlDefault::find()
            //                 ->where([
            //                     'sssccd_adm_relId'=>$gset->gs_adm_relId,
            //                     'sssccd_prog_relId' => $gset->gs_prog_relId,
            //                     'sssccd_sem_relId' => $gset->gs_sem_relId,
            //                     'sssccd_clslvl_relId' => $gset->gs_clslvl_relId,
            //                 ])
            //                 ->innerJoin('student_semester_status_name','student_semester_status_name.id = std_status_calculation_control_default.sssccd_sssn_relId')
            //                 ->andWhere(['student_semester_status_name.sssn_name'=>strtoupper($statusName)])
            //                 ->one();

            //             if ($statusInterval && count($statusInterval) > 0) {
            //                 $status1 = $status[0] / $status[1]; //statas[0] => totalGradePoint

            //                 if ($this->specialCase($gset)) {
            //                     // check only for SGPA
            //                     if ($status1 >= $statusInterval->sssccd_min_SGPA) {
            //                         // return true;
            //                         // why return????l
            //                     }else{
            //                         // die("<p class='bg-danger'> </p>");
            //                         $status1 = round($status1,2);
            //                         Yii::$app->session->setFlash('message', "Cannot apply withdrawal, because SGPA = {$status1} is below Readmission entry point = {$statusInterval->sssccd_min_SGPA}..");
            //                         return $this->redirect('/request-withdrawal/request-hod')->send();
            //                     }
            //                 }else{
            //                     // check both SGPA and CPGA.

            //                     // get CGPA of the student. and compare it with sssccd

            //                     $precStat = $this->getPrevCGPA($gset,$student);

            //                     if (is_array($precStat)) {

            //                         $prevTotalGrpt = $precStat[1];
            //                         $prevTotalCrdtHr = $precStat[0];

            //                         $totalCrdtHr = $prevTotalCrdtHr + $status[1];
            //                         $totalGrpt = $prevTotalGrpt + $status[0];

            //                         $status2 = $totalGrpt / $totalCrdtHr;

            //                         if (($status1 >= $statusInterval->sssccd_min_SGPA) && $status2 >= $statusInterval->sssccd_min_CGPA) {
            //                             // return true;
            //                             // why return????
            //                         }else{
            //                             $status1 = round($status1,2);
            //                             $status2 = round($status2,2);
            //                             Yii::$app->session->setFlash('message', "Cannot apply withdrawal, because SGPA = {$status1} is below Readmission entry SGPA point = {$statusInterval->sssccd_min_SGPA} OR your CGPA = {$status2} is below Readmission entry CGPA point = {$statusInterval->sssccd_min_CGPA}.");
            //                             return $this->redirect('/request-withdrawal/request-hod')->send();
            //                         }
            //                     }else{
            //                         // die("<p class='bg-danger'></p>");
            //                         Yii::$app->session->setFlash('message', "Error with previous semester registration. Grade submission not completed in prev. semester.");
            //                         return $this->redirect('/request-withdrawal/request-hod')->send();
            //                     } 
            //                 }
            //             }else{
            //                 // die("<p class='bg-danger'> ");
            //                 Yii::$app->session->setFlash('message', "Student re-adimission check-up entry not made yet, not available for the selected level.");
            //                 return $this->redirect('/request-withdrawal/request-hod')->send();
            //             }

            //         }else{
            //             // die("<p class='bg-danger'> </p>");
            //             Yii::$app->session->setFlash('message', "Student cannot request withdrawal. Not assignned to any group in this semester.");
            //                 return $this->redirect('/request-withdrawal/request-hod')->send();
            //         }

            //         // $statusInterval = 

            //         // die(" == SGPA = {$status} . Checking for Re-admission entry.");

            //     }else{
            //         // die('');
            //         Yii::$app->session->setFlash('message', "You cannot fill withdrawal form. Please contact Registrar clerks.");
            //         return $this->redirect('/request-withdrawal/request-hod')->send();
            //     }
            // }

            // $std = \backend\models\Student::findOne($model->swh_std_relId);

            // $model->swh_std_relId = $std->id;
            // $model->swh_acadYear_relId = $this->acadYearId;
            // $model->swh_sem_relId = $this->semId;

            // $model->swh_created_by = Yii::$app->user->identity->id;
            // $model->swh_created_at = date('Y-m-d h:i:s');

            // $transaction = Yii::$app->db->beginTransaction();
            // $message = "";
            // $s = 1;
            // $model->swh_course_removed = 1;

            // // echo "<pre>";
            // // $gg = json_decode($model->removedCourseList);
            // // var_dump(!empty($gg));die();


            // if ($model->save()) {
            //     $courses = json_decode($model->removedCourseList);
            //     if (is_array($courses) && !empty($courses)) {
            //         foreach ($courses as $key) {
            //             $srd2  = \frontend\models\SemesterRegistration2Detail::findOne($key);
            //             $rc = new RemovedCourse();
            //             $rc->rc_acadYear_relId = $model->swh_acadYear_relId;
            //             $rc->rc_sem_relId = $this->semId;
            //             $rc->rc_std_relId = $std->id;
            //             $rc->rc_withdrawal_relId = $model->id;
            //             $rc->rc_course_relId = $srd2->srd2_course_relId;
            //             $rc->rc_coff_relId = $srd2->srd2_coff_relId;


            //             $rc->rc_grade = is_null($srd2->srd2_grade_relId)?NULL:\backend\models\GradeScale::findOne($srd2->srd2_grade_relId)->gs_ltr_grade;

            //             if (!$rc->save()) {
            //                 $s = 0;
            //                 $message = "Error while buck_uping course enrollment information. not all courses bucked up.";
            //                 break;
            //                 // var_dump($rc->getErrors());die();
            //             }
            //             unset($rc);
            //         }
            //     }else{
            //         $s = 0;
            //         $message = "Error with the courses to remove.";
            //     }
            // }else{
            //     $s=0;
            //     echo "<p class='bg-error'>Error on operation...</p>";
            //     var_dump($model->getErrors());
            //     // die();
            // }
            // if ($s == 1) {
            //     $transaction->commit();
            //     echo "<p class='bg-success'>Operation completed.</p>";
            //     if (Yii::$app->user->can('DepartmentHead')) {
            //         echo "Redirecting to approval page...";
            //         return $this->redirect('/request-withdrawal/request-approve')->send();
            //     }
            // }else{
            //     $transaction->rollBack();
            //     echo "ERROR: {$message}";
            // }
            // die();
        } else {
            echo "<p class='bg-warning'>No Data provided.</p>";
            die();
            // return $this->redirect(['//index'])
            return $this->redirect('/request-withdrawal/request-hod', ['model' => new WStudentWithdrawalHistory()])->send();
        }
    }

    private function specialCase($gset)
    {
        $clslvl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId);
        $semester = \backend\models\Semester::findOne($gset->gs_sem_relId);

        return ($clslvl->clslvl_num == 1 && $semester->semester_name == 'I');
    }

    /**
     * Updates an existing WStudentWithdrawalHistory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing WStudentWithdrawalHistory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the WStudentWithdrawalHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WStudentWithdrawalHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WStudentWithdrawalHistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
