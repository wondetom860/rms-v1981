<?php

namespace frontend\controllers;

use yii;
use yii\helpers\ArrayHelper;
use backend\models\ReassessmentHistory;
use yii\base\ErrorException;

use \common\components\CommonControllerFunctions as CCF;

class ApproveReassessmentRequestController extends \yii\web\Controller
{


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
        return $this->render('index');
    }

    public function actionGetapprovedreassessrequests()
    {
        $this->__constructor();
        $reqs = NULL;
        if (Yii::$app->user->can('DepartmentHead')) {
            $reqs = ReassessmentHistory::find()
                ->where([
                    'ras_acadYear_relId' => $this->acadYearId,
                    'ras_sem_relId' => $this->semId,
                    'ras_approved_by' => Yii::$app->user->identity->id,
                ])->andWhere(['OR', 'ras_status=0', 'ras_status=1'])
                ->andWhere(['IS NOT', 'ras_registrarApprove_status', NULL])
                ->all();
        } else if (Yii::$app->user->can('Registrar')) {
            $reqs = ReassessmentHistory::find()
                ->where([
                    'ras_acadYear_relId' => $this->acadYearId,
                    'ras_sem_relId' => $this->semId,
                ])
                ->andWhere(['IS NOT', 'ras_registrarApprove_status', NULL])
                ->all();
        }
        // \

        if ($reqs) {
            $resp = "";
            $resp .= "<table class='table table-striped table-condensed table-xs table-bordered table-hover' style='font-size:10pt;'>";
            $resp .= "<caption><p class='bg-success'> <i class='bg-success'>" . count($reqs) . "</i> Course re-assessment Requests made in this semester.</p></caption>";
            $resp .= "<thead style='background-color:#ace;'>
                <th>#</th>
                <th>RegId</th>
                <th>Full Name</th>
                <th>Course Title</th>
                <th>Registrar</th>
                <th>Final Exam</th>
                <th>af. Mark</th>
                </thead><tbody>";
            $count = 0;
            foreach ($reqs as $std) {
                $catagory = \backend\models\ContAssessCatagory2::findOne(['id' => $std->ras_contAssCatag_relId]);
                $student = \backend\models\Student::findOne(['id' => $std['ras_std_relId']]);
                $usr = \backend\models\User::findOne(['id' => $student->std_user_relId]);
                $rap = is_null($std['ras_registrarApprove_status']) ? 'Pending' : ($std['ras_registrarApprove_status'] == 1 ? 'Approved' : 'Rejected');

                // $afg = is_null($std['ras_afterMark'])?'-':$std['ras_afterMark'];
                //$grp = \backend\models\WGroupSetup::findOne(['id'=>$std['ah_addWithGroup_relId']]);

                //  <td>". \backend\models\WGroup::findOne(['id'=>$grp->id])->group_name ."</td>
                // <td>
                $resp .= "<tr>
                    <td>" . ++$count . "</td>
                    <td>" . $student->std_regId . "</td>
                    <td>" . $usr->first_name . ' ' . $usr->middle_name . "</td>
                    <td>" . \backend\models\Course::findOne(['id' => $std['ras_course_relId']])->course_title . "</td>
                    <td>" . $rap . "</td>";

                $resp .= "<td style='text-align:center;'>{$std->ras_prevMark}/{$catagory->cat_maxPoint}</td>
                    <td>{$std['ras_afterMark']}</td>
                </tr>";
            }
            $resp .= "</tbody></table>";
            return $resp;
        } else {
            echo "<p class='bg-danger'>No course re-assess approved.</p>";
        }
    }



    public function actionChangereassessmentstatus()
    {

        $reqId = isset($_POST['reqId']) && !empty($_POST['reqId']) ? $_POST['reqId'] : die('Uknown Request');
        $actionId = isset($_POST['actionId']) && !empty($_POST['actionId']) ? $_POST['actionId'] : die('Uknown Coursae');

        // $gradeId = isset($_POST['gradeId']) && !empty($_POST['gradeId'])?$_POST['gradeId']:die('Uknown Grade');

        // $actionId = $actionId == 1?$actionId:0;

        $reqId = intval($reqId);
        $actionId = intval($actionId);
        $actionId = $actionId == 2 ? 0 : $actionId; //Change back to db-known state.

        // The gradeId is approval status for the request if the current yser is registrar

        //          and if the user is HoD, it is the grade changed, after re-assessing..
        //var_dump(['Student ID: '=>$stdId,'CourseId: '=>$courseId,'Grade id: '=>$gradeId]);die();

        $request = NULL;
        if (Yii::$app->user->can('DepartmentHead')) {
            $request  = ReassessmentHistory::findOne([
                'id' => $reqId,
                'ras_aft_grade_relId' => NULL,
                'ras_registrarApprove_status' => 1,
            ]);
        } else if (Yii::$app->user->can('Registrar')) {
            $request  = ReassessmentHistory::findOne([
                'id' => $reqId,
                'ras_aft_grade_relId' => NULL,
                'ras_registrarApprove_status' => NULL,
            ]);
        }
        //var_dump($request);die();

        if ($request) {

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $result = NULL;
                if (Yii::$app->user->can('Registrar')) {

                    // echo "Updating...";die();

                    if ($actionId == 1) { //for approval
                        $result = Yii::$app->db->createCommand()
                            ->update(
                                'reassessment_history',
                                [
                                    'ras_registrarApprove_status' => 1,
                                    'ras_registrarApprove_by' => Yii::$app->user->identity->id,
                                    'ras_registrarApprove_at' => date('Y-m-d h:i:s'),
                                ],

                                'id=' . $reqId
                            )->execute();
                        $result = $result == 1 ? TRUE : FALSE;
                    } else if ($actionId == 0) { // reject approval
                        // return back the grade, the request is rejected.
                        $result =  CCF::returnBackGrade($reqId);
                    }

                    if (is_bool($result) && $result) {
                        $transaction->commit();
                        echo 1;
                    } else {
                        $transaction->rollBack();
                        throw new ErrorException("Request approval Failed", 1);
                    }
                } else if (Yii::$app->user->can('DepartmentHead')) {
                    $result = Yii::$app->db->createCommand()
                        ->update(
                            'reassessment_history',
                            [
                                'ras_status' => $actionId,
                                'ras_approved_by' => Yii::$app->user->identity->id,
                                'ras_app_date' => date('Y-m-d h:i:s'),
                            ],

                            'id=' . $reqId
                        )
                        ->execute();
                    if ($result) {

                        // update course grade in the srd2 hence the course instrutor can update the grade.
                        $srd2 = \frontend\models\SemesterRegistration2Detail::findOne(['id' => $request->ras_srd2_relId]);

                        if ($srd2) {

                            $result2 = Yii::$app->db->createCommand()
                                ->update(
                                    'semester_registration_2_detail',
                                    [
                                        'srd2_requested_reassessment' => 1,
                                    ],

                                    'id=' . $request->ras_srd2_relId
                                )
                                ->execute();
                        }

                        $transaction->commit();
                        echo 1;
                    } else {
                        $transaction->rollBack();
                        throw new ErrorException("Request approval Failed", 1);
                    }
                } else {
                    throw new ErrorException("Invalid User type", 1);
                }
            } catch (ErrorException $e) {
                $transaction->rollBack();
                echo $e;
                die();
            }


            // 	$this->__constructor();

            // 	$result = Yii::$app->db->createCommand()
            // 			->update('reassessment_history',
            //                    [
            //                        'ras_status'=>1,
            //                        'ras_approved_by'=>Yii::$app->user->identity->id,
            //                        'ras_app_date'=>date('Y-m-d h:i:s'),
            //                        'ras_aft_grade_relId'=>$gradeId,
            //                     ],

            //                    'id='.$reqId
            //                    )  
            // 			->execute(); 
            // 	if ($result) {
            //            $result2 = Yii::$app->db->createCommand()
            //                ->update('semester_registration_2_detail',
            //                    [
            //                        'srd2_grade_relId'=>$gradeId,
            //                     ],

            //                    'id='.$request->ras_srd2_relId
            //                    )  
            //                ->execute(); 

            //                if ($result2) {

            //                    // update SGPA and CGPA

            //                   echo 1;
            //                }else{
            //                    print_r($result2->errors);
            //                }

            // }else{
            // 	// echo 'Failed to update grade. please try again';
            // 	print_r($result->errors);
            // }
            // die();
        } else {
            echo "No such Reassessment request request found!, may it changed?";
            die();
        }

        // echo json_encode(['action Id: '=>$actionId,'Req. Id: '=>$reqId]);die();
    }

    // Get course re-assessment requests made in this semester.
    public function actionGetreassessrequests()
    {
        $this->__constructor();
        $reqs = NULL;
        if (Yii::$app->user->can('DepartmentHead')) {

            $ghInst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
            $instDept = $ghInst->inst_dep_relId;

            // 'ras_acadYear_relId' => $this->acadYearId,
            // 'ras_sem_relId' => $this->semId,
            $reqs = ReassessmentHistory::find()
                ->where([
                    'ras_status' => NULL,
                ])
                ->innerJoin('course', 'reassessment_history.ras_course_relId = course.id')
                ->andWhere(['course.course_dept_relId' => $instDept])
                ->all();
        } else if (Yii::$app->user->can('Registrar')) {
            // 'ras_acadYear_relId' => $this->acadYearId,
            //         'ras_sem_relId' => $this->semId,
            $reqs = ReassessmentHistory::find()
                ->where([
                    'ras_registrarApprove_status' => NULL,
                ])
                ->all();
        }


        if ($reqs && count($reqs) > 0) {
            $reqss = '';
            $reqss .= "<table class='table table-striped table-condensed table-xs table-bordered table-hover'>";

            $reqss .= "<thead style='background-color:#ace;'>
            <th>RegId</th>
            <th>Full Name</th>
            <th>Course Title</th>
            <th colspan = 2 style='text-align:center;'>option</th>
            </thead><tbody>";
            $option = '';
            if (Yii::$app->user->can('DepartmentHead')) {

                // $gradeDropDown ="<option value=''> - </option>";
                // $grades = \backend\models\GradeScale::find()->all();
                // if ($grades) {
                //     foreach ($grades as $grade) {
                //          $gradeDropDown .= "<option value='". $grade['id'] ."'>". $grade['gs_ltr_grade']."</option>";
                //     }
                // }
                $option = "<option value=''> - </option><option value='1'>APPROVE</option>";
            } else if (Yii::$app->user->can('Registrar')) {
                $gradeDropDown = "<option value=''> - </option><option value='1'>APPROVE</option><option value='0'>REJECT</option>";
                $option = $gradeDropDown;
            }
            $count = 0;
            foreach ($reqs as $std) {
                $student = \backend\models\Student::findOne(['id' => $std['ras_std_relId']]);
                $usr = \backend\models\User::findOne(['id' => $student->std_user_relId]);
                $cc = \backend\models\Course::findOne(['id' => $std['ras_course_relId']]);

                // if () {
                if (Yii::$app->user->can('DepartmentHead') && $std['ras_registrarApprove_status'] == NULL) {
                    $option = "<option value='' title='Request not approved yet by the registrar.'> - </option>";
                }

                $reqss .= "<tr>
                <td>" . $student->std_regId . "</td>
                <td>" . $usr->first_name . ' ' . $usr->middle_name . "</td>
                <td>" . $cc->course_title . "</td>
                <td>
                    <select title='select action' onchange='saveGrade(" . $std['id'] . ",$(this).val())'>
                    " . $option . "
                    </select>
                </td>
                </tr>";
                ++$count;
            }
            $reqss .= "</tbody>";
            $reqss .= "<tfoot><p class='bg-success'> <i class='bg-danger'>" . $count . "</i> Requests.</p></tfoot>";
            $reqss .= "</table>";
            return $reqss;
        } else {
            die("<p class='bg-danger'>No request.</p>");
        }
    }
}
