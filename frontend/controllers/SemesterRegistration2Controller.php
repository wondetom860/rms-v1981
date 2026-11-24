<?php

namespace frontend\controllers;

use Yii;
use backend\models\SemesterRegistration2;
use backend\models\SemesterRegistration2Search;
use yii\web\Controller;
use backend\models\User;
use backend\models\CourseOffering2;
use backend\models\CourseOffering2Detail;
use backend\models\Student;
use backend\models\Course;
use frontend\models\SemesterRegistration2Detail;
use backend\models\PreRequisitController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\ErrorException;
use \backend\models\SelfSponsoredPaymentHistory;
use \common\components\CommonControllerFunctions as CCF;
use yii\filters\AccessControl;

/**
 * SemesterRegistration2Controller implements the CRUD actions for SemesterRegistration2 model.
 */
class SemesterRegistration2Controller extends Controller
{


    public $acadYearId;
    public $semId;
    protected function __constructor()
    {

        $cs = CCF::currentTime();
        // $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id = $cs['Y'];
        // $gset->gsActiveProgRel->semester_relId = $cs['S'];
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
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create'],
                'rules' => [
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['Admin', 'Registrar', 'Student'],
                    ],
                ],
            ],
        ];
    }



    /**
     * Lists all SemesterRegistration2 models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SemesterRegistration2Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEnrollCourses()
    {
        $semregId = Yii::$app->request->post('semregId');
        $model = $this->findModel($semregId);

        // create section for the course enrollment
        $rr = $model->createSection();
        if ($rr == true) {
            // var_dump($rr);exit;
            return $this->redirect(['/student/view', 'id' => $model->semreg_std_relId]);
        } else {
            return $rr;
        }
    }

    public function actionCreateEntry()
    {
        $stdId = Yii::$app->request->post('stdId');
        $model = new SemesterRegistration2();

        if ($model->load(Yii::$app->request->post())) {
            // var_dump(Yii::$app->request->post());
            $model->semreg_entry_type = 2;
            $model->semreg_created_at = $model->semreg_updated_at = date('Y-m-d H:i:s');
            $model->semreg_created_by = $model->semreg_updated_by = Yii::$app->user->identity->id;
            // check if already exists
            if ($model->alreadyExists()) {
                Yii::$app->session->setFlash('error', 'Such entry already exists');
            } else {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Entry successfully created');
                } else {
                    return "Creating failed, Error: " . json_encode($model->errors);
                }
            }

            return $this->redirect(['/student/view', 'id' => $model->semreg_std_relId]);
        }
        $model->semreg_std_relId = $stdId;
        $model->semreg_entry_type = 2; //student registering in this mode is transfer only.

        return $this->renderAjax('_form', ['model' => $model]);
    }

    public function actionRegradeCourses()
    {
        $semregId = Yii::$app->request->post('semregId');
        $model = $this->findModel($semregId);
        return $this->renderPartial('__report/regrading-form', ['model' => $model]);
    }

    /**
     * Displays a single SemesterRegistration2 model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionGetSrd2Detail()
    {
        $semregId = isset($_POST['semregId']) && !empty($_POST['semregId']) ? intval($_POST['semregId']) : die('InvalId request');

        $model = $this->findModel($semregId);

        return $this->renderPartial('__report/semreg-details', ['model' => $model, 'srd2s' => $model->semesterRegistration2Details]);
    }

    // Generate reort of the semsetr registration.
    public function actionReport()
    {
        $messaage =  "Genarting report for semseter registration is not completed, please come later..";
        return $this->render('report', ['message' => $messaage]);
    }

    // // Get list of course alergic students
    // public static function getCaStudets(){
    //     $fgraade = \backend\models\GradeScale::findOne(['gs_ltr_grade' => 'F']);
    //     return SemesterRegistration2Detail::find()
    //         ->innerJoin('semester_registration_2','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
    //         ->where([
    //             'srd2_grade_relId'=>$fgraade->id,
    //         ])
    //         ->andWhere(['>=','srd2_corptd_flag',2])
    //         ->all();
    // }

    // returns the list of enrolled courses if any
    // g

    public function actionGetenrolledcourses()
    {
        if (Yii::$app->user->can('Student')) {
            $usr = User::findOne(['id' => Yii::$app->user->identity->id]);

            if (!empty($usr)) {
                $wt_response = "";
                $std = Student::findOne(['std_user_relId' => $usr->id]);
                if ($std) {
                    $gset = CCF::currentStudentGroup($std);
                    $semreg2 = SemesterRegistration2::findOne([
                        'semester_registration_2.semreg_std_relId' => $std->id,
                        'semester_registration_2.semreg_acadYear_relId' => $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id,
                        'semester_registration_2.semreg_semester_relId' => $gset->gsActiveProgRel->semester_relId,
                    ]);

                    if ($semreg2) {

                        $enrolledCourses = \frontend\models\SemesterRegistration2Detail::find()
                            ->where([
                                'srd2_semreg_relId' => $semreg2->id,
                            ])->all();
                    } else {
                        die('Not enrolled any course in this semester.');
                    }

                    $spec = is_null($std->std_spec_relId) ? '-' : \backend\models\Specialization::findOne(['id' => $std->std_spec_relId])->spec_name;

                    $gset = $this->checkGroupAssigned($std);

                    // $grp = \backend\models\WGroup::findOne(['id' => $gset->gs_group_relId])->group_name;


                    $fullname = $usr->getFullName();
                    $wt_response .= "<table class='table table-hover table-sm table-bordered' style='font-size:9pt;'>";
                    $wt_response .= "<tr><td colspan=2>
                            <b>Academic Year: </b>{$gset->gsAcadYearRel->sch_year_desc}<br><b>Current Semester: </b>{$gset->gsClslvlRel->getClslvlDisp()}<hr>
                            
                            <b>Program:</b>{$std->stdProgRel->program_name} ({$std->stdAdmissionRel->admission_name})
                            <br>
                            <b>Department: </b>{$std->stdDeptRel->dept_name}<br><b>Class Year: </b>{$std->stdClslvlRel->clslvl_name}<br>
                            <b>Specialization: </b>" . $spec . "
                            <br>
                            <b>Group: </b>{$gset->gsGroupRel->group_name}
                        </td>
                        <td colspan=2>
                            <b>Full Name: </b>" . $fullname . "
                            <br>
                            <b>Reg. ID: </b>" . $std->std_regId . "
                            <br>
                            <div class='container-fluid' style=' border:1px dashed black; height:170px;'><center>" . '<img height="160" width="120" src="/uploads' . '/' . $std->stdUserRel->photo_num . '">' . "</center></div>
                        </td>
                        </tr></table>";

                    if ($enrolledCourses) {
                        $wt_response .= "<table class='table table-hover table-xs table-bordered' style='font-size:9pt;'>

                        <thead style='background-color:#99E;'><th>Course Detail</th><th>CrdtHr.</th><th>From</th></thead><tbody>";
                        $crsum = 0;

                        foreach ($enrolledCourses as $enroll) {
                            $course = $enroll->srd2CourseRel;
                            $crsum += $course->course_crdthr;
                            // $apprvd = $enroll['srd2_enrollApproved']==1?1:0 ;
                            // $el = $coff2->coff2detCocatRel->cocat_courseelectives == 0?'':"<b> (Elective) </b>";
                            $wt_response .= "<tr>
                                
                                <td>" . $course->course_code . " => " . $course->course_title . "</td>
                                <td>" . $course->course_crdthr . "</td>
                                <td>" . $this->getDept($course->course_dept_relId) . "</td>
                                
                                </tr>";
                        }
                        $wt_response .= "<tr style='background-color:#ace;'>
                            <td colspan='1'><b>Total Credit Hour:</td><td>" . $crsum . "</b></td>
                            <td colspan=2></td>
                            </tr>";
                    } else {
                        $wt_response .= "<tr style='background-color:#eac;'><td colspan=4><p class='bg-warning'>No course enrolled in this semester.</p></td></tr>";
                    }
                    $wt_response .= "</tbody></table>";
                    return $wt_response;
                }
            }
        }
    }

    protected function checkSelfSponsoredPaymentStatus($stdId, $pymntRsnId = NULL, $stdObj = NULL)
    {
        $this->__constructor();
        // check if payment redemption is made for this student in this academic year and semester.
        $modelRedemption = new \backend\models\PaymentRedemption();
        if (is_null($stdObj)) {
            return false;
        }
        $gset = $stdObj->stdGroupReal;
        $exists = $modelRedemption->getRequestExists($gset->gsActiveProgRel->academicYearRel->schoolYearRel->id, $gset->gsActiveProgRel->semester_relId, $stdId);

        if ($exists) {
            // checl its approval and used status:
            if ($exists->approved_by != NULL) {
                if ($exists->used != 1) {
                    $exists->used = 1;
                    $exists->data = $exists->getData();
                    $exists->update();
                }
                echo "<p class='bg-danger'>Registration by payment redemption!</p>";
                return true;
            } else {
                echo "<p class='bg-danger'>Your payment redemption request is NOT approved yet, Please contact registrar.</p>";
                die();
            }
        }

        // check payment ...
        $paid = NULL;
        if (is_null($pymntRsnId)) {
            $paid = \backend\models\SelfSponsoredPaymentHistory::findOne([
                'ssph_std_relId' => $stdId,
                'ssph_approved' => 1,
                'ssph_registrarApproved' => 1,
                'ssph_acadYear_relId' => $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id,
                'ssph_sem_relId' => $gset->gsActiveProgRel->semester_relId,
            ]);

            if ($paid) {
                # code...
            } else {
                $paid = SelfSponsoredPaymentHistory::find()->where([
                    'ssph_std_relId' => $stdId,
                    'ssph_acadYear_relId' => $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id,
                    'ssph_sem_relId' => $gset->gsActiveProgRel->semester_relId,
                ])->one();
            }
        } else {
            $paid = SelfSponsoredPaymentHistory::findOne([
                'ssph_std_relId' => $stdId,
                'ssph_approved' => 1,
                'ssph_registrarApproved' => 1,
                'ssph_acadYear_relId' => $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id,
                'ssph_sem_relId' => $gset->gsActiveProgRel->semester_relId,
                'ssph_pymntRsn_relId' => $pymntRsnId,
            ]);
            if ($paid) {
            } else {
                $paid = SelfSponsoredPaymentHistory::find()->where([
                    'ssph_std_relId' => $stdId,
                    'ssph_acadYear_relId' => $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id,
                    'ssph_sem_relId' => $gset->gsActiveProgRel->semester_relId,
                    'ssph_pymntRsn_relId' => $pymntRsnId,
                ])->one();
            }
        }

        if ($paid) {

            if (is_null($paid->ssph_approved)) {
                echo "<p class='bg-danger'>Semester Payment Pending approval from CEP Clerk.</p>";
            } else if ($paid->ssph_approved == 1) {
                if (is_null($paid->ssph_registrarApproved)) {
                    echo "<p class='bg-danger'>Your payment request is NOT APPROVED YET by registrar</p>";
                } else if ($paid->ssph_registrarApproved == 1) {
                    return TRUE;
                } else {
                    echo "<p class='bg-danger'>Your payment request is rejected by registrar. </p>";
                }
            } else {
                echo "<p class='bg-danger'>Your payment request is rejected by CEP Clerk. (ERR: 505)</p>";
            }
        } else {
            $model = new SelfSponsoredPaymentHistory();
            $model->ssph_std_relId = $stdId;
            $model->ssph_approved = NULL;
            $model->created_at = time();
            $model->created_by = Yii::$app->user->id;
            $model->getData();
            $model->ssph_registrarApproved = NULL;
            $model->ssph_acadYear_relId = $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id;
            $model->ssph_sem_relId = $gset->gsActiveProgRel->semester_relId;
            $model->ssph_pymntRsn_relId = $pymntRsnId;
            $model->ssph_txn_num = substr(md5(date('YmDHis')), -13);

            if ($model->save()) {
                echo "<p class='bg-danger'>Your Semester Payment is NOT approved yet, Please wait a moment, and retry!.</p>";
            } else {
                echo "<p>Error, Please retry!</p>";
            }
        }
        die();
    }

    // checks whether the student is assigned to a group , returns gset object on success, || error message 
    protected function checkGroupAssigned($stdObject)
    {
        return CCF::currentStudentGroup($stdObject);
        // $this->__constructor();
        //     $gset = \backend\models\WGroupSetup::find()
        //         ->where([
        //         'gs_acadYear_relId'=>$gset->gsActiveProgRel->academicYearRel->schoolYearRel->id,
        //         'gs_sem_relId'=>$gset->gsActiveProgRel->semester_relId,
        //         'gs_adm_relId'=>$stdObject->std_admission_relId,
        //         'gs_prog_relId'=>$stdObject->std_prog_relId,
        //         ])
        //     ->innerJoin('w_group_assignment','w_group_setup.id = w_group_assignment.gas_gset_relId')
        //     ->andWhere(['w_group_assignment.gas_std_relId'=>$stdObject->id])
        //     ->with('wGroupAssignments')
        //     ->one();
        //   return $gset;
    }

    protected function currentStudentGroup($stdObject, $semreg = null)
    {
        if ($semreg) {
            return CCF::getStudentGroupHistoryPublic($semreg->semreg_clslvl_relId, $semreg->semreg_semester_relId, $semreg->semreg_std_relId, $semreg->semreg_acadYear_relId);
        }
        return $stdObject->getRecentStudentGroup($semreg);
        // $this->__constructor();
        // $gset = \backend\models\WGroupSetup::find()
        //   ->where([
        //     'gs_acadYear_relId'=>$gset->gsActiveProgRel->academicYearRel->schoolYearRel->id,
        //     'gs_sem_relId'=>$gset->gsActiveProgRel->semester_relId,
        //     'gs_adm_relId'=>$stdObject->std_admission_relId,
        //     'gs_prog_relId'=>$stdObject->std_prog_relId,
        //   ])
        //   ->innerJoin('w_group_assignment','w_group_setup.id = w_group_assignment.gas_gset_relId')
        //   ->andWhere(['w_group_assignment.gas_std_relId'=>$stdObject->id])
        //   ->with('wGroupAssignments')
        //   ->one();
        // return $gset;
    }


    // Line Number

    // UserLoginHash::model()->deleteAll(
    //     'day <> :date',
    //     array('date' => date('Y-m-d'))
    // );
    private function removeInvalidRequests($id)
    {
        $command = Yii::$app->db->createCommand()
            ->delete('self_sponsored_payment_history', 'id = ' . $id)
            ->execute();
    }

    protected function checkSemeregistrationCalander($std)
    {
        // $acadYearId = SchoolYear::findOne(['sch_year_desc'=>Yii::$app->params['C']['Y']])->id;
        // $semId = Semester::findOne(['semester_name'=>Yii::$app->params['C']['S']])->id;

        $gset = $this->checkGroupAssigned($std);
        $this->__constructor();


        $actionName = 'Semester Registration';
        $schedule = NULL;
        $action = \backend\models\AcademicCalanderAction::findOne(['aca_name' => $actionName]);
        if ($action) {
            $schedule = CCF::getScheduleForAction($gset, $action->id);
            // if (!($schedule && count($schedule) > 0)) {
            //   die('Schedule is not open for Semester Registration. Wait for Penality Registration Time Schedule');
            // }
        } else {
            die("Unregistered action {$actionName}");
        }


        if (empty($schedule)) { //Normal registration is closed.

            // check if penality registartion is open.
            $actionName = 'Penalty registration';
            $action = \backend\models\AcademicCalanderAction::findOne(['aca_name' => $actionName]);
            if ($action) {
                $schedule = CCF::getScheduleForAction($gset, $action->id);
                if (!($schedule)) {
                    die("<p class='bg-danger'>You are out of semester registration period. wait for penality registration.</p>");
                }
            } else {
                die("Unregistered action {$actionName}");
            }

            // and check if the payment is approved for the student
            // actionName = penality registration
            // penality paymnet id is:
            $paymentType = \backend\models\PaymentReason::findOne(['pr_desc' => 'Penalty registration']);

            if (!($paymentType)) {
                die('Error with your payment type.!');
            } else {
                $payentStatus = SelfSponsoredPaymentHistory::find()->where([
                    'ssph_acadYear_relId' => $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id,
                    'ssph_sem_relId' => $gset->gsActiveProgRel->semester_relId,
                    'ssph_std_relId' => $std->id,
                    'ssph_pymntRsn_relId' => $paymentType->id,
                ])->one();

                if ($payentStatus) {
                    if (is_null($payentStatus->ssph_approved)) {
                        echo "<p class='bg-warning'>Your Penality payment request is not approved yet. Please provide your payment RECEIP hard copy to CEP Clerk before the schedule is clossed.</p>";
                        die();
                    } else if (is_null($payentStatus->ssph_registrarApproved)) {
                        echo "<p class='bg-warning'>Your Penality payment request is not approved yet. Please contact Registrar staff before the schedule is clossed.</p>";
                        die();
                    } else if ($payentStatus->ssph_approved == 1) {
                        echo "<p class='bg-success'>Your Penality Registration payment request is approved, you can continue your semsetr registration. </p>";
                        return TRUE;
                    } else {
                        echo "<p class='bg-danger'>Your payment request is rejected, please re-fill another before penalty registration closes.</p>";
                        echo "<button title='Make payment for penality registration.' class='btn btn-sm btn-warning' onclick='loadPaymentForm()'>Payment process</button>";
                        $this->removeInvalidRequests($payentStatus->id);
                        die();
                    }
                } else {
                    echo "<p class='bg-danger'>Penalty registration payment not made yet, please complete it before the calander closes.</p>";
                    echo "<button title='Make payment for penality registration.' class='btn btn-sm btn-warning' onclick='loadPaymentForm()'>Payment process</button>";
                    die();
                }
            }
        } else {
            echo "<p class='bg-success'>Registration Period Status: ACTIVE</p>";
            return TRUE;
        }
    }

    private function checkIfMaster2II($gsetObj, $stdObject)
    {
        // check if is 2,I
        // return "Going...";
        if (strtoupper($gsetObj->gsProgRel->program_name) == 'MASTERS') {
            if ($gsetObj->gsClslvlRel->clslvl_num == 2 && $gsetObj->gsSemRel->semester_name == 'II') {

                // NO choice am gonna to use hard code here::
                // 'cocat_adm_relId'=>$gsetObj->gs_adm_relId,
                // 'cocat_adm_relId'=>$gsetObj->gs_adm_relId,
                $thesisICourseId = \backend\models\CourseCatalog::find()
                    ->innerJoin('course', 'course.id = course_catalog.cocat_course_relId')
                    ->where(['course.course_title' => 'Thesis Phase I'])
                    ->andWhere([
                        'cocat_admission_relId' => $gsetObj->gs_adm_relId,
                        'cocat_program_relId' => $gsetObj->gs_prog_relId,
                        'cocat_dept_relId' => $gsetObj->gs_dept_relId,
                    ])
                    ->andWhere([
                        'cocat_courseProject' => 1
                    ])
                    ->one();
                // check if enrolled Thesis phase I and has valid pass status

                if ($thesisICourseId) {
                    // var_dump($thesisICourseId);die();
                } else {
                    return "No Course titled 'Thesis Phase I' NOT found in your course matrix";
                }
                $semregDetObj = \frontend\models\SemesterRegistration2Detail::find()
                    ->innerJoin('semester_registration_2', 'semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
                    ->where([
                        'semreg_std_relId' => $stdObject->id,
                    ])
                    ->andWhere([
                        'srd2_course_relId' => $thesisICourseId->cocat_course_relId,
                    ])
                    ->one();
                if ($semregDetObj) {
                    if (!is_null($semregDetObj->srd2GradeRel) && $semregDetObj->srd2GradeRel->gs_pass_status == 1) { //THESIS_PHASE_I_GRADE status is pass
                        echo "<p class='bg-success'>Grade obtained on {$semregDetObj->srd2CourseRel->course_title} : {$semregDetObj->srd2GradeRel->gs_ltr_grade}</p>";
                        return TRUE;
                        // die("Letter Grade: ".);
                    } else {
                        return "Minimum requirement for semester registration is NOT satisfied, Your THESIS_PHASE_I_GRADE is Invalid for registering THESIS_PHASE_II";
                    }
                } else {
                    die("<p class='bg-danger'>Enrolled 'Thesis Phase I' ? , RECORD_COULD_NOT_BE_FOUND</p>");
                }
            }
            return TRUE;
        } else {
            return TRUE; //for others than MASTERS - [2,I].
        }
    }

    private function checkIfMaster2I($gsetObj, $stdObject)
    {
        // check if is 2,I


        if (strtoupper($gsetObj->gsProgRel->program_name) == 'MASTERS') {
            if ($gsetObj->gsClslvlRel->clslvl_num == 2 && $gsetObj->gsSemRel->semester_name == 'I') {
                // get semetser grade of the student on 1,II
                $pClslvlObj = \backend\models\ClassLevel::findOne(['clslvl_program_relId' => $gsetObj->gs_prog_relId, 'clslvl_num' => 1]);
                $pSemObj = \backend\models\Semester::findOne(['semester_name' => 'II']);

                $Gset1II = CCF::getStudentGroupHistoryPublic($pClslvlObj->id, $pSemObj->id, $stdObject->id);
                CCF::getSemesterGrade($pClslvlObj->id, $pSemObj->id, $stdObject->id);

                $semregObj = \frontend\models\SemesterRegistration2::find()
                    ->where([
                        'semreg_std_relId' => $stdObject->id,
                        'semreg_semester_relId' => $pSemObj->id,
                        'semreg_clslvl_relId' => $pClslvlObj->id,
                    ])
                    ->one();
                if ($semregObj) {
                    if ($semregObj->semreg_CGPA >= 3.00) {
                        // die($semregObj->semreg_CGPA);
                        return TRUE;
                    } else {
                        $dd = sprintf("%0.2f", round($semregObj->semreg_CGPA, 2, PHP_ROUND_HALF_UP));
                        return "Minimum requirement for semester registration is NOT satisfied, Your CGPA is {$dd}";
                    }
                } else {
                    die("<p class='bg-danger'>Invalid previous semeseter status, RECORD_COULD_NOT_BE_FOUND</p>");
                }
            }
            return TRUE;
        } else {
            return TRUE; //for others than MASTERS - [2,I].
        }
    }

    // returns the list of courses for registration/enrollment.
    public function actionGetcourses($foradd  = null, $std = null, $semregId = null)
    {
        // Check if the user is Student..
        // $acadYearId = SchoolYear::findOne(['sch_year_desc'=>Yii::$app->params['C']['Y']])->id;
        // $semId = Semester::findOne(['semester_name'=>Yii::$app->params['C']['S']])->id;

        $std = Yii::$app->request->get('std');
        $foradd = Yii::$app->request->get('foradd');
        $semregId = Yii::$app->request->get('semregId');

        $this->__constructor();
        $semreg = $semregId ? $this->findModel($semregId) : null;
        if (Yii::$app->user->can('Student')) {
            $usr = User::findOne(['id' => Yii::$app->user->identity->id]);
            if (!empty($usr)) {
                $std = Student::findOne(['std_user_relId' => $usr->id]);
                return $this->CreateRegistrationForm($std, $foradd, $semreg);
            } else {
            }
        } else if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {
            // 
            if ($std) {
                $std = Student::findOne($std);
                // if($foradd) {
                //     return $this->redirect(['/add-history/get-courses-for-add','stdId' => $std->id ]);
                // }
                return $this->CreateRegistrationForm($std, $foradd, $semreg);
            } else {
                echo "<p class='bg-danger'>Invalid request.</p>";
            }
        } else {
            echo "<p class='bg-danger'>You have nothing to do here.</p>";
        }
    }

    private function CreateRegistrationForm($std, $forAdd, $semregId)
    {
        if (!empty($std)) {

            // check if the student is active student
            $this->checkStudentSemsterStatus($std->id);
            // check if group assigned for this semesetr.
            // $gset = $this->checkGroupAssigned($std);
            $gset2 = $this->currentStudentGroup($std, $semregId);
            if ($gset2) {
                # code...
            } else {
                echo "<p class='bg-danger'>You are not assigned to any group for this semester. Please contact registrar clerks if this is error.</p>";
                die();
            }
            // check for semester payment if self-sponsored student.
            $feeStudents = [];
            $selfSponsored = \backend\models\ScholarshipType::find()->where(['schType_pay' => 1])->all();
            if ($selfSponsored) {
                foreach ($selfSponsored as $key) {
                    $feeStudents[] = $key->id;
                }
            }
            if (in_array($std->std_schlrshpType_relId, $feeStudents)) {
                $pymntRsn = \backend\models\PaymentReason::findOne(['pr_desc' => 'self-sponsored semester payment']);
                if (!is_null($forAdd)) {
                    // die("dying");
                    $this->checkSelfSponsoredPaymentStatus($std->id, $pymntRsn->id, $std);
                }
                $this->checkSelfSponsoredPaymentStatus($std->id, $pymntRsn->id, $std);
            }

            // check Calander.
            if (!(Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin'))) {
                $this->checkSemeregistrationCalander($std);
            }

            // Check if the student registered some courses before.  -----=
            $semreg2 = SemesterRegistration2::findOne([
                'semester_registration_2.semreg_std_relId' => $std->id,
            ]);
            $semreg2courses  =  [];

            if (!empty($semreg2)) {
                $id = $semreg2->id;

                // check for already registered courses.
                $semreg2detail = SemesterRegistration2Detail::find()->where([
                    'srd2_semreg_relId' => $id
                ])->all();
                if ($semreg2detail) {
                    foreach ($semreg2detail as $cid) {
                        array_push($semreg2courses, $cid['srd2_course_relId']);
                    }
                }
                $semreg2courses = array_unique($semreg2courses);
            }

            // Ckeck if course offering is made for the group.
            $cofId = CourseOffering2::find()->where([
                'course_offering2.coff2_acadYear_relId' => $gset2->gs_acadYear_relId,
                'course_offering2.coff2_sem_relId' => $gset2->gs_sem_relId,
                'course_offering2.coff2_group_relId' => $gset2->id,
            ])->one();

            if ($cofId) {
            } else {
                echo "<p class='bg-danger'>Course offering not made yet. please wait.</p>";
                die();
            }

            $dropedCourses = new \backend\models\CourseDropHistory();
            $rep = "";
            // This semester enrolled courses
            $thisSemesterEnrolledCourses = SemesterRegistration2Detail::find()
                ->innerJoinWith('srd2SemregRel')
                ->where([
                    'semester_registration_2.semreg_std_relId' => $std->id,
                    'semester_registration_2.semreg_acadYear_relId' => $gset2->gsActiveProgRel->academicYearRel->schoolYearRel->id,
                    'semester_registration_2.semreg_semester_relId' => $gset2->gsActiveProgRel->semester_relId
                ])
                ->all();

            $thisSemesterEnrolledCoursesArrays = [];
            if ($thisSemesterEnrolledCourses) {
                foreach ($thisSemesterEnrolledCourses as $key) {
                    $thisSemesterEnrolledCoursesArrays[] = $key['srd2_course_relId'];
                }

                $thisSemesterEnrolledCoursesArrays = array_unique($thisSemesterEnrolledCoursesArrays);
            }
            // ->andFilterWhere(['NOT IN','course_offering2_detail.coff2det_course_relId',$semreg2courses])
            // ->andFilterWhere(['NOT IN','course_offering2_detail.coff2det_course_relId',$thisSemesterEnrolledCourses])
            $coff22 = CourseOffering2Detail::find()
                ->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                ->where(['course_offering2.coff2_group_relId' => $gset2->id])
                ->andWhere(['coff2det_registrarApproved' => 1])
                ->distinct('coff2det_course_relId')
                ->all();

            if (!empty($coff22)) {
                $c = 0;
                $crsum = 0;
                $maxAllowedCedit = 0; //default maxCredit for semester registration is 19.
                $minAllowedCedit = 0; //default maxCredit for semester registration is 19.

                $maxAllowedObject = CCF::getMaxAllowedCreditHrPerSemester($gset2);

                if ($maxAllowedObject) {
                    $maxAllowedCedit = $maxAllowedObject->src_max_crdthr;
                    $minAllowedCedit = $maxAllowedObject->src_min_crdthr;
                }
                $rep .= "<table style='font-size:9pt;' class='table table-hover table-xs table-bordered'>";
                $rep .= "<caption style='font-weight:bold; color: #000; text-align:center;'>
                            <h5>Course Registration for ACY:{$gset2->getAcadYearHeader()} |Group:  {$gset2->getGsDisp()}</h5>
                    </caption>";
                $rep .= "<thead style='background-color:#99E;'>
                        <th>#</th>
                        <th>Course Detail</th>
                        <th>CrdtHr.</th>
                        <th>From</th>
                        <th>Type</th>
                        <th>Option</th>
                    </thead><tbody>";

                $displayedCourseList = [];
                // $preqModel
                foreach ($coff22 as $coff2) {
                    $course = $coff2->coff2detCourseRel;
                    if (in_array($course->id,  $thisSemesterEnrolledCoursesArrays)) {
                        continue;
                    }
                    if (in_array($course->id,  $semreg2courses)) {
                        continue;
                    }

                    // Drop Approved courses should NOT be displayed in Semester registartion
                    if ($dropedCourses->dropApproved($course->id, $std->id, $gset2->gsActiveProgRel->academicYearRel->schoolYearRel->id, $gset2->gsActiveProgRel->semester_relId)) {
                        // echo "Droped Course";
                        continue;
                    }

                    if (in_array($course->id, $displayedCourseList)) {
                        continue;
                    }
                    $displayedCourseList[] = $course->id;

                    $chkBx = "<input type='checkbox' onchange='addEnroll(" . $course->id . "," . $coff2['id'] . ",{$course->course_crdthr},{$maxAllowedCedit},{$minAllowedCedit})'>";

                    // 
                    $enn = $this->alreadyEnrolledAndHasValidGrade($course->id, $std->id);

                    if ($enn) {
                        if (strtoupper($std->stdProgRel->program_name) == 'MASTERS' && $enn->srd2GradeRel->gs_pass_status == 0) {
                            $chkBx = "<input type='checkbox' title='Enrolled Before' class='bg-info' onchange='addEnroll(" . $course->id . "," . $coff2['id'] . ",{$course->course_crdthr},{$maxAllowedCedit},{$minAllowedCedit})'>";
                        } else {
                            $chkBx = "<span title='Already Enrolled'>+</span>";
                        }
                    } else {
                        // check if safe of pre-requisite:
                        $precModel = new PreRequisitController();
                        $prec = $precModel->safeOfPrerequisit($course, $std, $gset2);

                        if (isset($prec[0]) && $prec[0]) {
                            $chkBx = "<input type='checkbox' title='Enrolled Before' class='bg-info' onchange='addEnroll(" . $course->id . "," . $coff2['id'] . ",{$course->course_crdthr},{$maxAllowedCedit},{$minAllowedCedit})'>";
                        } else {
                            $chkBx = "<span title='Pre-requisite Message'>{$prec[1]}</span>";
                        }
                    }

                    // $rptd = $this->checkIfRepeated($course->id,$std->id);
                    $crsum += $course->course_crdthr;
                    $el = $coff2->coff2detCocatRel->cocat_courseelectives == 0 ? '' : "<b> (Elective) </b>";
                    $rep .= "<tr>
                        <td>" . ++$c . "</td>
                        <td>" . $course->course_code . " => " . $course->course_title . " {$el}</td>
                        <td>" . $course->course_crdthr . "</td>
                        <td><b>{$course->courseDeptRel->dept_code}</b></td>
                        <td><b>{$course->getCourseEntryType()}</b></td>
                        <td>{$chkBx}</td>
                        </tr>";
                    // echo "<div class='container'>". $course->course_title ."</div>";
                }
                $rep .= "<tr style='background-color:#ace;'>
                    <td colspan='2'><b>Total Credit Hour:</td><td>" . $crsum . "</b></td>
                    <td colspan='2' style='background-color: #0FF;'>Required : MIN = {$minAllowedCedit}, MAX = {$maxAllowedCedit}</td>
                    </tr></tbody>";
                $rep .= "</table>";
                return $rep;
            } else {
                if (count($semreg2courses) > 0) {
                    if (count($thisSemesterEnrolledCourses) > 0) {
                        echo "<p class='bg-success'>You enrolled all the courses offered in this semester successfully, (::)</p>";
                        die();
                    } else {
                        echo "<p class='bg-warning'>Course Offering Not Approved By registrar</p>";
                        die();
                    }
                } else {
                    echo "<p class='bg-danger'>No course offered for your section, please wait a moment.</p>";
                    die();
                    //echo "";
                }
            }
        }
    }

    private function checkIfRepeated($courseId, $stdId)
    {
        // $fgrade = \backend\models\GradeScale::findOne(['gs_ltr_grade' => 'F']);
        // ->andWhere([
        //     'srd2_grade_relId' => $fgrade->id
        // ])
        // ?Why do I check for F grade, course can be added with out having F  grade, eg. GC students can clear C,C-.. course grades
        return \frontend\models\SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'srd2_course_relId' => $courseId,
                'semreg_std_relId' => $stdId,
            ])
            ->count();;
    }
    private function alreadyEnrolledAndHasValidGrade($courseId, $stdId)
    {
        $gradeValid = \backend\models\GradeScale::find()->where(['gs_valid' => 1])->all();
        // $fgrade = \backend\models\GradeScale::findOne(['gs_ltr_grade'=>'F']);

        $validGradeArray = [];
        if ($gradeValid) {
            foreach ($gradeValid as $VGkey) {
                if ($VGkey->gs_ltr_grade == 'F') {
                    continue;
                }
                $validGradeArray[] = $VGkey->id;
            }
        }
        $validGradeArray[] = NULL;

        $validGradeArray = array_unique($validGradeArray);


        return \frontend\models\SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'srd2_course_relId' => $courseId,
            ])
            ->andWhere([
                'srd2_grade_relId' => $validGradeArray
            ])
            ->andWhere([
                'semreg_std_relId' => $stdId,
            ])
            ->one();
    }

    protected function getDept($id)
    {
        return \backend\models\Department::findOne(['id' => $id])->dept_name;
    }

    protected function getStudentLastEnrolSem($stdId)
    {
        $semreg = SemesterRegistration2::find()
            ->where([
                'semreg_std_relId' => $stdId,
            ])
            ->all();
    }

    protected function checkStudentSemsterStatus($stdId)
    {
        $student = Student::findOne(['id' => $stdId]);

        $gset = CCF::currentStudentGroup($student);
        // Valid semester statuses. 
        $status = $student->std_semester_status;
        $validStatus = ['-', 'ACTIVE', 'PASS', 'WARNING', 'PROBATION 1', 'PROBATION 2', '2ND WARNING'];  //Hard 

        if (!in_array(strtoupper($student->std_semester_status), $validStatus)) {
            echo "<p class='bg-danger'>You can not register course for this semester. Yor semester status is <b>{$status}</b></p>";

            die();
        }
    }

    private function checkMinMaxCredit($gset, $crdtSum, $stdId)
    {
        if (Yii::$app->user->can('Admin') || Yii::$app->user->can('Registrar')) {
            return true;
        }

        $enrolledCourses1 = SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'semester_registration_2.semreg_std_relId' => $stdId,
                'semester_registration_2.semreg_acadYear_relId' => $gset->gs_acadYear_relId,
                'semester_registration_2.semreg_semester_relId' => $gset->gs_sem_relId,
                'semester_registration_2.semreg_clslvl_relId' => $gset->gs_clslvl_relId,
            ])
            ->all();
        if ($enrolledCourses1) {
            foreach ($enrolledCourses1 as $srd) {
                $crdtSum += $srd->srd2CourseRel->course_crdthr;
            }
        }

        if ($crdtSum > 0) {
            $maxCredit = CCF::getMaxAllowedCreditHrPerSemester($gset);
            if ($maxCredit) {
                if ($crdtSum <= $maxCredit->src_max_crdthr && $crdtSum >= $maxCredit->src_min_crdthr) {
                    return TRUE;
                }
                return "Total credit hour to enroll in this semester is <b>[{$maxCredit->src_min_crdthr} - {$maxCredit->src_max_crdthr}]</b>, But your total collected Credit sum is <b>{$crdtSum}</b></i>";
            }
            return "Semester Registration Control configuration NOT completed yet";
        }
        return "Credit Hours to enroll per semester cannot be {$crdtSum}";
    }

    /**
     * Creates a new SemesterRegistration2 model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SemesterRegistration2();
        $std = null;
        $semregId = null;
        if (Yii::$app->user->can('Student')) {
            $std = Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
        } else if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {
            $stdId = Yii::$app->request->post('stdId');
            $semregId = Yii::$app->request->post('semregId');
            $std = Student::findOne($stdId);
        }
        //$acadYearId= SchoolYear::findOne(['sch_year_desc'=>Yii::$app->params['C']['Y']])->id;
        //$semId = Semester::findOne(['semester_name'=>Yii::$app->params['C']['S']])->id;
        $gset = $this->currentStudentGroup($std);
        //var_dump($std);die();

        if (isset($_POST['coList']) && !empty($_POST['coList']) && isset($_POST['coffList']) && !empty($_POST['coffList'])) {
            $courses = json_decode($_POST['coList']);
            $coffs = json_decode($_POST['coffList']);
            $modelId = NULL;

            // Table for displaying enroll status of each course.
            $table = "<table class='table table-sm table-condensed table-bordered' style='font-size:9pt;'>";
            $table .= "<thead><th>Course</th><th>Enroll Status</th></thead><tbody>";

            // print_r(array_unique($courses));die();

            $enMessage = [];
            $passList = [];
            // check point 1: check for course pre-requisit

            // check point 2: check for semester status of student

            // check point 3: check for registration schedule time

            // check point 4: let me enroll the student simply without checking any.

            $message = "";
            $pass = FALSE;
            $crdtSum = 0;
            foreach ($coffs as $coff) {
                //$coffDet = CourseOffering2::findOne(['id'=>$coff]);
                $courseId = CourseOffering2Detail::findOne(['id' => $coff])->coff2det_course_relId;
                $course = Course::findOne($courseId);
                // check if masters and [2,I] //because there is Thesis there, student's CGPA must be >=3.00
                if (strtoupper($course->course_title) == 'THESIS PHASE I') {
                    $eligible = $this->checkIfMaster2I($gset, $std, $course->id);
                    if (is_bool($eligible) && $eligible == TRUE) { //check for Thesis I
                        // check if eligible for Thesis,
                    } else {
                        $message .= $course->course_title . " Not enrolled, ";
                        $message .= " Reason: -> {$eligible}.<br>";
                        $pass = FALSE;
                        continue;
                        // die("{$eligible}");
                    }
                }

                if (strtoupper($course->course_title) == 'THESIS PHASE II') {
                    $eligible2 = $this->checkIfMaster2II($gset, $std, $course->id, $gset);
                    if (is_bool($eligible2) && $eligible2 == TRUE) { //check foe Thesis II
                        // check if eligible for Thesis,
                    } else {
                        $message .= $course->course_title . " Not enrolled, ";
                        $message .= " Reason: -> {$eligible2}.<br>";
                        $pass = FALSE;
                        continue;
                        // die("{$}");
                    }
                }

                $precModel = new \backend\models\PreRequisitController();
                $prec = $precModel->safeOfPrerequisit($course, $std, $gset);
                $message .= $prec[1];
                $pass = $prec[0];

                if ($pass) {
                    // Register this course for the student.
                    array_push($passList, $coff);
                    $crdtSum += $course->course_crdthr;
                } else {
                    $table .= "<tr class='bg-danger'><td>{$course->course_title}</td><td>" . $message . "</td></tr>";
                    // $message = null;
                }
            }

            $creditAllowed = $this->checkMinMaxCredit($gset, $crdtSum, $std->id);

            if (is_bool($creditAllowed) && $creditAllowed = TRUE) {
            } else {
                echo "{$message}";
                echo "<br>{$creditAllowed}";
                die();
            }

            if (!empty($passList)) {
                // $this->__constructor();
                // $modelId = NULL;
                // Check if the student have enrolled for someCourses before now in this Semster.

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($semregId) {
                        $regRec = SemesterRegistration2::findOne($semregId);
                    } else {
                        $regRec = SemesterRegistration2::findOne([
                            'semreg_std_relId' => $std->id,
                            'semreg_semester_relId' => $gset->gsActiveProgRel->semester_relId,
                            'semreg_acadYear_relId' => $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id,
                        ]);
                    }

                    // var_dump($regRec);die();

                    if (empty($regRec)) {
                        $model = new SemesterRegistration2();
                        $model->semreg_std_relId = $std->id;
                        $model->semreg_semester_relId = $gset->gsActiveProgRel->semester_relId;
                        $model->semreg_acadYear_relId = $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id;
                        $model->semreg_clslvl_relId = $std->std_clslvl_relId;
                        $model->semreg_created_by = $model->semreg_updated_by = Yii::$app->user->identity->id;
                        $model->semreg_created_at = $model->semreg_updated_at = date('Y-m-d h:i:s');
                        if ($model->save()) {
                            $modelId = $model->id;
                        } else {
                            $table .= "<tr class='bg-danger'><td colspan=2>" . json_encode($model->errors) . "</td></tr>";
                        }
                    } else {
                        $modelId = $regRec->id;
                    }

                    if ($modelId !== NULL) {
                        $semreg = SemesterRegistration2::findOne($modelId);
                        $table .= $this->saveToDetail($std, $semreg, $passList, $table);
                        $table .= "</tbody></table>";
                        $transaction->commit();
                        if (!Yii::$app->user->can('Student')) {
                            return 1;
                        }
                        return $table;
                    } else {
                        $table .= "</tbody></table>";
                        throw new ErrorException("Semester registration entry creation failed, please try again after a while.", 1);
                    }
                } catch (ErrorException $e) {
                    $transaction->rollBack();
                    die($e);
                }
            } else {
                echo "{$message}";
                echo "<br>{$creditAllowed}";
                die();
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    protected function saveToDetail($std, $semreg, $courses, $t)
    {
        $message = null;
        $status = 0;

        if (!empty($courses)) {
            //$gset = $this->checkGroupAssigned($std);
            $gset = $this->currentStudentGroup($std);
            //var_dump($gset);die();
            foreach ($courses as $coffId) {
                // check if repeated,
                $coffIdd = CourseOffering2Detail::findOne(['id' => $coffId])->coff2det_coff2_relId;
                $courseId = CourseOffering2Detail::findOne(['id' => $coffId])->coff2det_course_relId;
                $course = Course::findOne(['id' => $courseId]);
                // check if repeated in this semester:
                $semCourses = $semreg->getSemesterRegistration2Details()->select('srd2_course_relId')->all();
                if ($semCourses && in_array($course->id, array_column($semCourses, 'srd2_course_relId'))) {
                    continue;
                }
                $repeated = $this->checkIfRepeated($course->id, $std->id);

                $model = new SemesterRegistration2Detail();
                $model->srd2_semreg_relId = $semreg->id;
                $model->srd2_coff_relId = $coffIdd;
                $model->srd2_corptd_flag = $repeated;
                $model->srd2_course_relId = $courseId;
                $model->srd2_total_mark = 0;
                $model->srd2_created_at = $model->srd2_updated_at = date('Y-m-d h:i:s');
                $model->srd2_created_by = $model->srd2_updated_by = Yii::$app->user->identity->id;
                $model->srd2_group_relId = $gset->id;

                if ($model->save()) {
                    $message = 'Enrolled Successfuly';
                    $status = 1;
                } else {
                    throw new ErrorException("Error while registering courses." . $model->errors, 1);
                    $status = 0;
                }
                $bg = $status ? 'bg-success' : 'bg-danger';
                $t .= "<tr class=" . $bg . "><td>" . $course->course_title . "</td><td>" . $message . "</td></tr>";
            }
        }
        return $t;
    }

    /**
     * Updates an existing SemesterRegistration2 model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SemesterRegistration2 model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $stdId = $model->semreg_std_relId;
        $model->delete();

        return $this->redirect(['student/view', 'id' => $stdId]);
    }

    /**
     * Finds the SemesterRegistration2 model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SemesterRegistration2 the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SemesterRegistration2::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
