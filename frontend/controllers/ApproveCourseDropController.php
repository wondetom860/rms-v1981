<?php

namespace frontend\controllers;

use yii;
use yii\helpers\ArrayHelper;
use backend\models\CourseDropHistory;
use \common\components\CommonControllerFunctions as CCF;
use Exception;

class ApproveCourseDropController extends \yii\web\Controller
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

    public function actionGetapproveddroprequests()
    {
        $this->__constructor();
        // if(Yii::$app->user->can('DepartmentHead')){}
        $resp = "";
        $reqs = CourseDropHistory::find()
            ->where([
                'cdh_acadYear_relId' => $this->acadYearId,
                'cdh_sem_relId' => $this->semId,
                'cdh_approved_by' => Yii::$app->user->identity->id,
            ])
            ->andWhere([
                'OR',
                'cdh_approved = 1',
                'cdh_approved = 0'
            ])
            ->all();
        if ($reqs) {
            $resp .= "<table class='table table-striped table-condensed table-xs table-bordered'>";
            $resp .= "<caption><p class='bg-success'> <i class='bg-danger'>" . count($reqs) . "</i> Requests.</p></caption>";
            $resp .= "<thead>
                <th>RegId</th>
                <th>Full Name</th>
                <th>Dropped Course</th>
                <th colspan>Status</th>
                </thead><tbody>";
            foreach ($reqs as $std) {
                $student = \backend\models\Student::findOne(['id' => $std['cdh_std_relId']]);
                // $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
                $status = is_null($std->cdh_approved) ? 'Pending' : ($std->cdh_approved == 0 ? 'Rejected' : 'Accepted');
                //$status = is_null($course->cdh_status)?'Pending':'Replied';
                $resp .= "<tr>
                <td>" . $student->std_regId . "</td>
                <td>{$student->getFullName()}</td>
                <td>{$std->cdhCourseRel->course_title}</td>
                <td>" . $status . "</td>
             
                </tr>";
            }
            $resp .= "</tbody></table>";
        } else {
            echo "<p class='bg-danger'>No course approved.</p>";
            return;
        }
        echo $resp;
        return;
    }

    public function actionChangerequeststatus()
    {

        $actionId = isset($_POST['actionId']) && !empty($_POST['actionId']) ? $_POST['actionId'] : die('Uknown Action');
        $reqId = isset($_POST['reqId']) && !empty($_POST['reqId']) ? $_POST['reqId'] : die('Uknown Request');

        $actionId = $actionId == 1 ? $actionId : 0;

        $request  = CourseDropHistory::find()->where([
            'id' => $reqId,
        ])->one();
        $this->__constructor();

        if ($request) {
            $transaction = Yii::$app->db->beginTransaction();
            if ($actionId == 1) { //remove course if only the action is approve.
                try {
                    $srd2 = \backend\models\SemesterRegistration2Detail::findOne($request->cdh_srd2_relId);
                    $cc = str_replace(" ", "-", $request->cdhCourseRel->course_code);
                    $regId = str_replace("/", "", $request->cdhStdRel->std_regId);
                    $file_name = "cd_" . $regId . "_" . $cc . "_" . date("Y-m-d");

                    $result = Yii::$app->db->createCommand()
                        ->update(
                            'course_drop_history', //add_history
                            [
                                'cdh_status' => 1,
                                'cdh_approved' => $actionId,
                                'cdh_approved_by' => Yii::$app->user->identity->id,
                                'cdh_created_at' => date('Y-m-d h:i:s'),
                                'cdh_restore_path' => $file_name,
                            ],
                            'id=' . $reqId
                        )
                        ->execute();

                    // store the serialized file.
                    $filehandler = fopen("reports/removed_files/" . $file_name, 'wb');
                    fwrite($filehandler, serialize($srd2));
                    fclose($filehandler);

                    $delete = Yii::$app->db->createCommand()
                        ->delete(
                            'semester_registration_2_detail',
                            [
                                'id' => $request->cdh_srd2_relId,
                            ]
                        )
                        ->execute();
                    if ($delete) {
                        $transaction->commit();
                        return 1;
                    } else {
                        return "Failed to approve the request.";
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                    return "Failed to approve the request." . $e;
                }
            } else {
                // restore the course registration entry:
                try {
                    // read and unserialize the file to restore.
                    $srd2 = unserialize(file_get_contents("reports/removed_files/" . $request->cdh_restore_path));

                    if ($srd2->save()) {
                        $result = Yii::$app->db->createCommand()
                            ->update(
                                'course_drop_history', //add_history
                                [
                                    'cdh_status' => NULL,
                                    'cdh_approved' => $actionId,
                                    'cdh_approved_by' => Yii::$app->user->identity->id,
                                    'cdh_created_at' => date('Y-m-d h:i:s'),
                                ],
                                'id=' . $reqId
                            )
                            ->execute();
                        if ($result) {
                            $transaction->commit();
                            return 1;
                        } else {
                            $transaction->rollBack();
                            return "Failed to update status.";
                        }
                    } else {
                        $transaction->rollBack();
                        var_dump($srd2->getErrors());
                        return "Failed to update status." . json_encode($srd2->getErrors());
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                    return "Failed to update status.";
                }
            }
        } else {
            return "No such add request found!";
        }
    }

    public function actionGetdroprequests()
    {
        $this->__constructor();

        $instDept = NULL;

        $modelSemAdvObj = new \backend\models\SemesterAdvisor();
        $modelCourseDropObj = new CourseDropHistory();

        if (Yii::$app->user->can('Instructor') || Yii::$app->user->can('DepartmentHead')) {

            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);

            $batchCourseDropRequestsArray = [];
            $adObj = $modelSemAdvObj->isAdvisor($inst->id);
            if ($adObj && count($adObj) > 0) {
                echo "<p>You are assigned as semester advisor for</p>";
                foreach ($adObj as $key) {
                    echo "<p class='bg-info'>{$key->saDeptRel->dept_code} - Year-{$key->saClslvlRel->clslvl_num}</p>";
                    // featch these course drop requests of baches  which this advisor is assigned for.
                    $reqs = $modelCourseDropObj->getBatchRequests($key);
                    if ($reqs && count($reqs) > 0) {
                        foreach ($reqs as $CDRkey) {
                            $batchCourseDropRequestsArray[] = $CDRkey->id;
                        }
                    } else {
                        echo "<p class='bg-danger'>No course Droup request Found.</p>";
                        // die();
                    }
                }

                $batchCourseDropRequestsArray = array_unique($batchCourseDropRequestsArray);

                if ($batchCourseDropRequestsArray && count($batchCourseDropRequestsArray) > 0) {
                    $requests = $modelCourseDropObj->find()->where(['id' => $batchCourseDropRequestsArray])->all();
                    if ($requests && count($requests) > 0) {
                        $table = "<table class='table table-striped table-hover table-condensed' style='font-size:9pt;'>";
                        $table .= "<caption><center></center>Course Drop Requests</caption>";
                        $table .= "<thead style='background-color:#8D0;'>
                                    <th style='border:1px solid black;'>#</th>
                                    <th style='border:1px solid black;'>Reg.ID</th>
                                    <th style='border:1px solid black;'>Full Name</th>
                                    <th style='border:1px solid black;'>Program</th>
                                    <th style='border:1px solid black;'>Course Detail</th>
                                    <th style='border:1px solid black;'>Option</th>
                                </thead>";
                        $count = 0;
                        foreach ($requests as $CDHkey) {
                            $std = $CDHkey->cdhStdRel;
                            $course = $CDHkey->cdhCourseRel;
                            // $CDHkey->cdh
                            $opt  = "";
                            if (is_null($CDHkey->cdh_approved)) {
                                $opt = "<select title='select action' onchange='changeStatus({$CDHkey->id},$(this).val())'>
                                                <option value=' '> </option>
                                                <option value='1'>Approve</option>
                                                <option value='0'>Disapprove</option>
                                            </select>";
                            }
                            $table .= "<tr>
                                        <td style='border:1px solid black;'>" . ++$count . "</td>
                                        <td style='border:1px solid black;'>{$std->std_regId}</td>
                                        <td style='border:1px solid black;'>{$std->getFullName()}</td>
                                        <td style='border:1px solid black;'><b>{$std->stdAdmissionRel->admission_name[0]}-{$std->stdProgRel->program_name[0]}</b>{$CDHkey->cdhClslvlRel->getClslvlDisp()}</td>
                                        <td style='border:1px solid black;' title='{$course->course_title}'>{$course->course_code} - {$course->course_crdthr}</td>
                                        <td style='border:1px solid black;'>{$opt}</td>
                                    </tr>";
                        }
                        $table .= "</table>";
                        die($table);
                    }
                } else {
                    die('No request.');
                }
            } else {
                die("<p class='bg-danger'>You are not assigned as semester advisor.</p>");
            }
        } else {
            die("INVALID_ACCESS!");
        }

        // die();
        // $reqs = CourseDropHistory::find()
        //     ->where([
        //         'cdh_acadYear_relId' => $this->acadYearId,
        //         'cdh_sem_relId' => $this->semId,
        //         'cdh_approved' => NULL,
        //         'cdh_status' => NULL,
        //     ])
        //     ->innerJoin('student', 'student.id = course_drop_history.cdh_std_relId')
        //     ->andWhere(['student.std_dept_relId' => $instDept])
        //     ->all();
        // if ($reqs) {
        //     echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
        //     echo "<caption><p class='bg-success'> <i class='bg-danger'>" . count($reqs) . "</i> Requests.</p></caption>";
        //     echo "<thead>
        //         <th>RegId</th>
        //         <th>Full Name</th>
        //         <th>Dropping courses.</th>
        //          <th colspan = 2>Action</th>
        //         </thead><tbody>";
        //     foreach ($reqs as $std) {
        //         $student = \backend\models\Student::findOne(['id' => $std['cdh_std_relId']]);
        //         $usr = \backend\models\User::findOne(['id' => $student->std_user_relId]);
        //         echo "<tr>
        //         <td>" . $student->std_regId . "</td>
        //         <td>" . $usr->first_name . ' ' . $usr->middle_name . "</td>
        //         <td>" . \backend\models\Course::findOne(['id' => $std['cdh_course_relId']])->course_title . "</td>
              
        //         <td>
        //         <select title='select action' onchange='changeStatus({$std->id},$(this).val())'>
        //             <option value=' '> </option>
        //             <option value='1'>Approve</option>
        //             <option value='0'>Disapprove</option>
        //         </select></td>
        //         </tr>";
        //     }
        //     echo "</tbody></table>";
        // } else {
        //     echo "<p class='bg-danger'><i>No result</i></p>";
        // }
        // // echo "<pre>";
        // // var_dump($reqs);die();
        // // echo "<pre>";
    }
}
