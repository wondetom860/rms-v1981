<?php

namespace frontend\controllers;

use Yii;
use backend\models\TouchedGrades;

class ApproveGradeChangeController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index',['model' => new TouchedGrades()]);
    }


    // get-request-list
    public function actionGetRequestList(){
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('Select Acdemic Year');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('Select Semester');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Select Program');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Select Class Level');

        if (Yii::$app->user->can('Registrar') || 1) {
           $requests =  TouchedGrades::find()
           	->where([
           		'tg_acadYear_relId' => $acadYearId,
           		'tg_sem_relId' => $semId,
           		'tg_prog_relId' => $progId,
           		'tg_clslvl_relId' => $clslvlId
           	])
           	->all();
           $wreport = "";

            if ($requests && count($requests) > 0) {
                $wreport .= "<table class='table table-condensed table-striped table-bordered' style='font-size:9pt;'>";
                $wreport .= "<thead>   
                                <th>#</th>
                                <th>Reg. ID</th>
                                <th>Student Name</th>
                                <th>Course Detail</th>
                                <th>Before Grade</th>
                                <th>After Grade</th>
                                <th>Option</th>
                            </thead></tbody>";
                $count = 0;
                foreach ($requests as $tg) {
                    $userObj = $tg->tgSemregRel->semregStdRel->stdUserRel;
                    $courseObj = $tg->tgSrd2Rel->srd2CourseRel;
                    $bGradeObj = $tg->tgBeforeGradeRel ? $tg->tgBeforeGradeRel->gs_ltr_grade : '-';
                    // $bGradeObj = $tg->tgSrd2Rel->srd2GradeRel ? $tg->tgSrd2Rel->srd2GradeRel->gs_ltr_grade : '-';
                    $aGradeObj = $tg->tgAfterGradeRel ? $tg->tgAfterGradeRel->gs_ltr_grade : '-';
                    if ($tg->tg_approved_by == NULL) {
                        $button = "<button class='btn btn-primary btn-xs' onclick='ApproveRequest({$tg->id},1);return false;'>Approve</button>";
                    }else{
                        if ($tg->tg_operation_completed == NULL) {
                            $button = "<button class='btn btn-warning btn-xs' onclick='ApproveRequest({$tg->id},2);return false;'>Reject</button>";
                        }
                    }
                    
                    $wreport .= "<tr>
                                    <td>".++$count."</td>
                                    <td>{$tg->tgSrd2Rel->srd2SemregRel->semregStdRel->std_regId}</td>
                                    <td>{$userObj->first_name} {$userObj->middle_name} {$userObj->last_name}</td>
                                    <td>{$courseObj->course_code} => {$courseObj->course_title}</td>
                                    <td>{$bGradeObj}</td>
                                    <td>{$aGradeObj}</td>
                                    <td>{$button}</td>
                                </tr>";
                }
                $wreport .= "</tbody></table>";
                return $wreport;
            }else{
                return "<p class='bg-warning'>No Record</p>";
            }
        }
    }

}
