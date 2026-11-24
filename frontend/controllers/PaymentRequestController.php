<?php

namespace frontend\controllers;

use yii;
use backend\models\SelfSponsoredPaymentHistory;
use backend\models\Student;
use \common\components\CommonControllerFunctions as CCF;

class PaymentRequestController extends \yii\web\Controller
{


    public $acadYearId;
    public $semId;
    protected function __constructor()
    {

        // $cs = CCF::currentTime();
        // $this->acadYearId = $cs['Y'];
        // $this->semId = $cs['S'];

        $activeAcademicYear = null;
        $ac = Yii::$app->AcademicCalendar->getActiveProgramModels(null);
        if ($ac && count($ac) > 0) {
            $activeAcademicYear = $ac[0];
        } else {
            return "Academic year is not active";
        }
        $thisSem = $activeAcademicYear->semesterRel;
        $this->acadYearId = $activeAcademicYear->schoolYearRel->id;
        $this->semId = $thisSem->id;
    }

    public function actionIndexx()
    {
        $model = new SelfSponsoredPaymentHistory();
        $courseId = isset($_POST['courseId']) && !empty($_POST['courseId']) ? intval($_POST['courseId']) : NULL;
        return $this->renderAjax(
            '__index',
            [
                'model' => $model,
                'courseId' => $courseId,
            ]
        );
    }

    public function actionIndex()
    {
        $model = new SelfSponsoredPaymentHistory();
        // 
        if (Yii::$app->user->can('CEP Clerk') || Yii::$app->user->can('Registrar')) {
            return $this->render('__index__');
        } else if (\Yii::$app->user->can('Student')) {
            return $this->render(
                'index',
                [
                    'model' => $model,
                ]
            );
        }
        return $this->render('__index__');
    }

    public function actionChangerequeststatus()
    {

        $actionId = isset($_POST['actionId']) && !empty($_POST['actionId']) ? intval($_POST['actionId']) : die('Uknown Action');
        $reqId = isset($_POST['reqId']) && !empty($_POST['reqId']) ? intval($_POST['reqId']) : die('Uknown Request');

        $actionId = $actionId == 1 ? $actionId : 0;

        $this->__constructor();

        $request = NULL;
        if (Yii::$app->user->can('CEP Clerk')) {
            // 'ssph_sem_relId'=>$this->semId,
            //    'ssph_acadYear_relId'=>$this->acadYearId,
            $request  = SelfSponsoredPaymentHistory::find()->where([
                'id' => $reqId,
                'ssph_approved' => NULL,
            ])->one();
            //$this->__constructor();

            if ($request) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $result = Yii::$app->db->createCommand()
                        ->update(
                            'self_sponsored_payment_history', //add_history
                            [
                                'ssph_approved' => $actionId,
                                'ssph_approved_by' => Yii::$app->user->identity->id,
                                'ssph_approved_at' => date('Y-m-d h:i:s'),

                            ],
                            'id=' . $reqId
                        )
                        ->execute();

                    if ($result) {
                        $transaction->commit();
                        echo 1;
                    } else {
                        echo "ERROR_ Failed to update requsset.";
                        print_r($result);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    echo "Failed to approve the request." . $e;
                }
            } else {
                echo "No such payment request found!";
                die();
            }
        } else if (Yii::$app->user->can('Registrar')) {
            // 'ssph_sem_relId'=>$this->semId,
            $request  = SelfSponsoredPaymentHistory::find()->where([
                'id' => $reqId,
                'ssph_registrarApproved' => NULL,
                // 'ssph_acadYear_relId'=>$this->acadYearId,
            ])->one();
            //$this->__constructor();

            if ($request) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($actionId == 1) { //approve case
                        $result = Yii::$app->db->createCommand()
                            ->update(
                                'self_sponsored_payment_history', //add_history
                                [
                                    'ssph_registrarApproved' => $actionId,
                                    'ssph_registrarApproved_by' => Yii::$app->user->identity->id,
                                    'ssph_registrarApproved_at' => date('Y-m-d h:i:s'),

                                ],
                                'id=' . $reqId
                            )
                            ->execute();
                    } else {
                        $result = Yii::$app->db->createCommand()
                            ->update(
                                'self_sponsored_payment_history', //add_history
                                [
                                    'ssph_registrarApproved' => NULL,
                                    'ssph_approved' => NULL,
                                    'ssph_registrarApproved_by' => Yii::$app->user->identity->id,
                                    'ssph_registrarApproved_at' => date('Y-m-d h:i:s'),

                                ],
                                'id=' . $reqId
                            )
                            ->execute();
                    }
                    if ($result) {
                        $transaction->commit();
                        echo 1;
                    } else {
                        echo "ERROR_ Failed to update requsset.";
                        print_r($result);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    echo "Failed to approve the request." . $e;
                }
            } else {
                echo "No such payment request found!";
                die();
            }
        } else {
            echo "ERROR_ Failed to update requsset.";
            die();
        }
        // echo json_encode(['action Id: '=>$actionId,'Req. Id: '=>$reqId]);die();
    }

    public function actionRemoveRequest()
    {
        $reqId = Yii::$app->request->post('reqid');

        $req = SelfSponsoredPaymentHistory::find()
            ->where([
                'OR',
                ['ssph_registrarApproved' => NULL],
                ['<>', 'ssph_approved', 1],
            ])
            ->andWhere(['id' => $reqId])
            ->one();

        if (Yii::$app->user->can('Admin') && $req) {
            $resp = $req->delete();
            if ($resp) {
                return 1;
            } else {
                return "Failed to delete the request, unknown error occured.";
            }
        } else {
            return "Request can NOT be removed. Either you are not authorized or the request is already processed.";
        }
    }

    public function actionGetpaymentreport()
    {
        $this->__constructor();
        $report = NULL;
        $student = NULL;
        if (Yii::$app->user->can('Student')) {
            $student = Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
            $report = SelfSponsoredPaymentHistory::find()->where([
                'ssph_std_relId' => $student->id,
            ])
                ->orderBy('id DESC')
                ->all();
        } else if (Yii::$app->user->can('Registrar')) {
            $report = SelfSponsoredPaymentHistory::find()
                ->andWhere(['ssph_registrarApproved' => NULL,])
                ->orderBy('id DESC')
                ->all();
        } else if (Yii::$app->user->can('CEP Clerk')) {
            $report = SelfSponsoredPaymentHistory::find()
                ->where(['ssph_approved' => NULL,])
                ->orderBy('id DESC')
                ->all();
        } elseif (Yii::$app->user->can('Admin')) {
            $report = SelfSponsoredPaymentHistory::find()
                ->where(['OR', ['ssph_registrarApproved' => NULL], [' <> ', 'ssph_approved', 1]])
                ->orderBy('id DESC')
                ->all();
        }

        $wreport = "";

        // var_dump($report);die();
        if ($report && count($report) > 0) {
            return $this->renderPartial(
                '_payment_report',
                [
                    'report' => $report,
                    'student' => $student,
                ]
            );
        } else {
            echo "<p class='bg-danger'>No payment request made yet.</p>";
        }
    }
    public function actionProcesspayment()
    {
        $this->__constructor();
        $ok = 1;
        $message = '';
        if (Yii::$app->user->can('Student')) {
            $student = Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);

            $nmnmn = NULL;
            $txn = isset($_POST['txn']) && !empty($_POST['txn']) ? strtoupper($_POST['txn']) : die('No Transaction (RECEIPT) Number provided.');
            $courseId = isset($_POST['courseId']) && !empty($_POST['courseId']) ? $_POST['courseId'] : NULL;
            $paymentReason = isset($_POST['paymentReason']) && !empty($_POST['paymentReason']) ? strtoupper($_POST['paymentReason']) : die('Please choose Payment reason...');

            if (!is_null($courseId)) {
                $ss = \backend\models\PaymentReason::findOne(['id' => $paymentReason, 'pr_desc' => 'Course Re-assessment']);
                if ($ss) {
                    $nmnmn = 1; //assume as request is approved from registrar head, for course re-assessment request only
                } else {
                    $ok = 0;
                    $message = 'Invalid Payment Reason, Please correct!';

                    echo json_encode([
                        'ok' => $ok,
                        'message' => $message,
                    ]);
                    die();
                }
            }
            // check if made for this semester.
            $exists = SelfSponsoredPaymentHistory::findOne([
                'ssph_txn_num' => $txn,
            ]);
            if ($exists) {
                // 'ssph_acadYear_relId'=>$this->acadYearId,
                //  'ssph_sem_relId' => $this->semId;
                //  'ssph_std_relId' = >$student->id;
                $ok = 0;
                if ($exists->ssph_std_relId == $student->id) {
                    $message = 'Invalid txn number, You already requested this txn number.';
                } else {
                    $message = 'Invalid txn number, already requested txn number by some one else.';
                }

                echo json_encode([
                    'ok' => $ok,
                    'message' => $message,
                ]);
                die();
            }

            if ($ok == 1) {
                $model = new SelfSponsoredPaymentHistory();
                $model->ssph_acadYear_relId = $this->acadYearId;
                $model->ssph_sem_relId = $this->semId;
                $model->ssph_registrarApproved = $nmnmn;
                $model->ssph_std_relId = $student->id;
                $model->ssph_course_relId = $courseId;
                $model->ssph_txn_num = $txn;
                $model->ssph_pymntRsn_relId = $paymentReason;

                if ($model->save()) {
                    $ok =  1;
                    $message = "Your payment approval request captured successfully, please provide your RECEPT hard copy for verification to complete processing, ASAP.";
                } else {
                    $ok = 0;
                    $message = 'Internal error occured while processing your request.';
                    print_r($model->errors);
                    die();
                }
            }
        } else {
            $ok = 1;
            $message =  "<p class='bg-danger'>You want to approve payment requests?</p>";
        }

        echo json_encode([
            'ok' => $ok,
            'message' => $message,
        ]);
        die();
    }

    public function actionChangeBack()
    {
        $reqId = isset($_POST['reqId']) && !empty($_POST['reqId']) ? intval($_POST['reqId']) : die('Invalid request');

        // check if the student is enrolled course with this payment history:

        $pHistory = SelfSponsoredPaymentHistory::findOne($reqId);

        if ($pHistory) {

            if (is_null($pHistory->ssph_registrarApproved) || $pHistory->ssph_registrarApproved == 0) {
                $res = Yii::$app->db->createCommand()
                    ->update(
                        'self_sponsored_payment_history',
                        [
                            'ssph_approved' => NULL,
                        ],
                        [
                            'id' => $pHistory->id
                        ]
                    )
                    ->execute();
                if ($res) {
                    echo "1";
                    die();
                } else {
                    die('Changing status Failed, UKNOWN_ERROR_OCCURED!');
                }
            } else {
                die('Request status already changed. Registrar approved the request?');
            }
        } else {
            die('Invalid Request, No sucn request exists!');
        }
    }

    public function actionGetApprovedPayments()
    {
        $this->__constructor();
        $report = NULL;
        $student = NULL;
        $reportRec = NULL;
        if (Yii::$app->user->can('Student')) {
            $student = Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
            $report = SelfSponsoredPaymentHistory::find()->where([
                'IS NOT',
                'ssph_approved',
                NULL,
            ])
                ->orderBy('id DESC')
                ->all();
        } else {
            $reportRec = SelfSponsoredPaymentHistory::find()
                ->where(['IS NOT', 'ssph_approved', NULL])
                ->andWhere(['ssph_acadYear_relId' => $this->acadYearId, 'ssph_sem_relId' => $this->semId])
                ->orderBy('id DESC')
                ->all();
        }

        // var_dump($report);die();
        $report = "";
        if ($reportRec && count($reportRec) > 0) {
            $report .= "<table id='report_table' class='table table-hover table-condensed table-xs table-bordered ' style='font-size:10pt;'>";
            $report .= "<caption><p class='bg-success'> <i class='bg-danger'>" . count($reportRec) . "</i> Payment(s).</p></caption>";
            $report .= "<thead style='background-color:#00c0ef;'>
                    <th>#</th>
                    <th>RegId</th>
                    <th>Full Name</th>
                    <th>Program</th>
                    <th>Pymnt Reason</th>
                    <th>Acad.Year</th>
                    <th>Sem.</th>
                    <th>Course</th>
                    <th>Txn #</th>
                    <th>Status(R)</th>
                    <th>Status(C)</th>
            ";
            if (Yii::$app->user->can('CEP Clerk')) {
                $report .= "<th>Option</th>";
            }
            $report .= "
            </thead><tbody>";
            $c = 0;
            foreach ($reportRec as $reports) {
                if (!Yii::$app->user->can('Student')) {
                    $student = Student::findOne(['id' => $reports['ssph_std_relId']]);
                }
                $usr = \backend\models\User::findOne(['id' => $student->std_user_relId]);
                $pp = \backend\models\PaymentReason::findOne(['id' => $reports['ssph_pymntRsn_relId']]);
                $upp = is_null($pp) ? '-' : $pp->pr_desc;
                $reqStatus = is_null($reports['ssph_approved']) ? 'Pending' : ($reports['ssph_approved'] == 1 ? 'Approved' : 'Rejected');
                $reqStatusReg = is_null($reports['ssph_registrarApproved']) ? 'Pending' : ($reports['ssph_registrarApproved'] == 1 ? 'Approved' : 'Rejected');
                $ccc = is_null($reports['ssph_course_relId']) ? 'N/A' : \backend\models\Course::findOne(['id' => $reports['ssph_course_relId']])->course_title;

                $title = "{$student->stdAdmissionRel->admission_name} - {$student->stdProgRel->program_name}";
                $prog = $student->stdAdmissionRel->admission_name[0];
                $prog .= $student->stdProgRel->program_name[0];

                $report .= "<tr class='tr'>
                <td>" . ++$c . "</td>
                <td>" . $student->std_regId . "</td>
                <td>" . $usr->first_name . ' ' . $usr->middle_name . ' ' . $usr->last_name . "</td>
                <td title='{$title}'><b>[{$prog}]</b></td>
                <td>" . ucfirst($upp) . "</td>
                <td>" . \backend\models\SchoolYear::findOne(['id' => $reports['ssph_acadYear_relId']])->sch_year_desc . "</td>
                <td>" . \backend\models\Semester::findOne(['id' => $reports['ssph_sem_relId']])->semester_name . "</td>
                <td>" . $ccc . "</td>
                <td>" . $reports['ssph_txn_num'] . "</td>
                <td>" . $reqStatusReg . "</td>
                <td>" . $reqStatus . "</td>";

                if (Yii::$app->user->can('CEP Clerk') && (is_null($reports->ssph_registrarApproved) || ($reports->ssph_registrarApproved == 0))) {
                    $report .= "<td><button class='btn btn-warning btn-xs' onclick='changeBack({$reports->id})'>Change</button></td>";
                }
                $report .= "</tr>";
            }
            $report .= "</tbody></table>";
            return $report;
        } else {
            echo "<p class='bg-danger'>No payment request made yet.</p>";
        }
    }
}
