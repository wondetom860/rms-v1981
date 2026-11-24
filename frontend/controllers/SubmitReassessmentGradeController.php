<?php

namespace frontend\controllers;

use yii;
use yii\helpers\ArrayHelper;
use backend\models\ReassessmentHistory;
use yii\base\ErrorException;
use \backend\models\ContinuousAssessment2;

use \common\components\CommonControllerFunctions as CCF;

class SubmitReassessmentGradeController extends \yii\web\Controller
{

	
public $acadYearId;
public $semId;
protected function __constructor(){

    $cs = CCF::currentTime();
    $this->acadYearId = $cs['Y'];
    $this->semId = $cs['S'];
}
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetapprovedreassessrequests(){
        $this->__constructor();
        $reqs = NULL;
        if(Yii::$app->user->can('DepartmentHead')){
            $reqs = ReassessmentHistory::find()
                    ->where([
                        'ras_acadYear_relId'=>$this->acadYearId,
                        'ras_sem_relId'=>$this->semId,
                        'ras_approved_by'=>Yii::$app->user->identity->id,
                    ])
                    ->andWhere(['IS NOT', 'ras_aft_grade_relId',NULL])
                    ->andWhere(['IS NOT', 'ras_afterMark',NULL])
                    ->all();
        }else if (Yii::$app->user->can('Registrar')) {
           $reqs = ReassessmentHistory::find()
                    ->where([
                        'ras_acadYear_relId'=>$this->acadYearId,
                        'ras_sem_relId'=>$this->semId,
                    ])
                    ->andWhere(['IS NOT','ras_registrarApprove_status',NULL])
                    ->all();
        }
        // \
        
            if ($reqs) {
                echo "<table class='table table-striped table-condensed table-xs table-bordered table-hover' style='font-size:10pt;'>";
                echo "<caption><p class='bg-success'> <i class='bg-success'>". count($reqs) ."</i> Course re-assessment Requests made in this semester.</p></caption>";
                echo "<thead style='background-color:#ace;'>
                <th>#</th>
                <th>RegId</th>
                <th>Full Name</th>
                <th>Course Title</th>
                <th>Registrar</th>
                <th>HOD</th>
                <th>bf. Grade</th>
                <th>af. Grade</th>
                </thead><tbody>";
                $count = 0;
            foreach ($reqs as $std) {
                $student = \backend\models\Student::findOne(['id'=>$std['ras_std_relId']]);
                $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
                $rap = is_null($std['ras_registrarApprove_status'])?'Pending':($std['ras_registrarApprove_status'] == 1?'Approved':'Rejected');
                $hop = is_null($std['ras_status'])?'Pending...':($std['ras_status'] == 1?'Approved':'Rejected');

                $afg = is_null($std['ras_aft_grade_relId'])?'-':\backend\models\GradeScale::findOne(['id'=>$std['ras_aft_grade_relId']])->gs_ltr_grade;
                //$grp = \backend\models\WGroupSetup::findOne(['id'=>$std['ah_addWithGroup_relId']]);

                //  <td>". \backend\models\WGroup::findOne(['id'=>$grp->id])->group_name ."</td>
                // <td>
                echo "<tr>
                    <td>". ++$count ."</td>
                    <td>". $student->std_regId ."</td>
                    <td>". $usr->first_name.' '. $usr->middle_name ."</td>
                    <td>". \backend\models\Course::findOne(['id'=>$std['ras_course_relId']])->course_title ."</td>
                    <td>". $rap."</td>
                    <td>". $hop."</td>";

                echo "<td>". \backend\models\GradeScale::findOne(['id'=>$std['ras_bef_grade_relId']])->gs_ltr_grade ."</td>
                    <td>". $afg ."</td>
                </tr>";

              
            }
            echo "</tbody></table>";
            }else{
                echo "<p class='bg-danger'>No course re-assess approved.</p>";
            }
    }

    public function actionChangereassessmentstatus(){

    	$reqId = isset($_POST['reqId']) && !empty($_POST['reqId'])?$_POST['reqId']:die('Uknown Request');
    	// $actionId = isset($_POST['actionId']) && !empty($_POST['actionId'])?$_POST['actionId']:die('Uknown Coursae');
        $afterMark = isset($_POST['afterMark']) && !empty($_POST['afterMark'])?$_POST['afterMark']:die('Updated mark submission Error. Please correct it first.');

    	// $gradeId = isset($_POST['gradeId']) && !empty($_POST['gradeId'])?$_POST['gradeId']:die('Uknown Grade');

    	// $actionId = $actionId == 1?$actionId:0;

    	$reqId = intval($reqId);
    	// $actionId = intval($actionId);
        // $actionId = $actionId == 2?0:$actionId; //Because it is grade in this caase, no need of changing it..

        // The gradeId is approval status for the request if the current yser is registrar

        //          and if the user is HoD, it is the grade changed, after re-assessing..
    	//var_dump(['Student ID: '=>$stdId,'CourseId: '=>$courseId,'Grade id: '=>$gradeId]);die();

        $request = NULL;
    	if (Yii::$app->user->can('DepartmentHead')) {
            $request  = ReassessmentHistory::findOne([
            'id'=>$reqId,
            'ras_aft_grade_relId'=>NULL,
            'ras_afterMark'=>NULL,
            'ras_status'=>1,
            'ras_registrarApprove_status'=>1,
            ]);

        }else if(Yii::$app->user->can('Registrar')){
           $request = NULL;
        }
        //var_dump($request);die();

    	if ($request && count($request) > 0) {

            $transaction = Yii::$app->db->beginTransaction();
            try{
            $result = NULL;
            if (Yii::$app->user->can('Registrar')) {

                echo "You have completed your job. Thanks (::)";die();

            }else if (Yii::$app->user->can('DepartmentHead')) {
                $result = Yii::$app->db->createCommand()
                        ->update('reassessment_history',
                            [
                                'ras_afterMark'=>$afterMark,
                                'ras_updated_by'=>Yii::$app->user->identity->id,
                                'ras_updated_at'=>date('Y-m-d h:i:s'),
                             ],

                            'id='.$reqId
                            )  
                        ->execute(); 
                if ($result) {
                    // ca_markAssignment_relId
                    // ca_catagorySetup_relId
                    $contAss = ContinuousAssessment2::find()
                        ->where(['ca_std_relId'=>$request->ras_std_relId])
                        ->innerJoin('instance_mark_assignment','instance_mark_assignment.id = continuous_assessment2.ca_markAssignment_relId')
                        ->andWhere(['instance_mark_assignment.ima_catagorySetup_relId'=>$request->ras_contAssCatag_relId])
                        ->one();

                    $modelO = new ContinuousAssessment2();
                    $gradeUpdate = $modelO->updateFinalExamMark($contAss,$afterMark,$request->ras_course_relId);
                    
                     if ($gradeUpdate && count($gradeUpdate) > 0) {
                            $result = Yii::$app->db->createCommand()
                                ->update('reassessment_history',
                                    [
                                        'ras_aft_grade_relId'=>$gradeUpdate->id,
                                        'ras_updated_by'=>Yii::$app->user->identity->id,
                                        'ras_updated_at'=>date('Y-m-d h:i:s'),
                                     ],

                                    'id='.$reqId
                                    )  
                                ->execute(); 
                        if ($result) {                                    
                            $transaction->commit();
                            echo 1;
                        }else{
                            throw new ErrorException("Grade updating failed. Please try again.", 1);
                        }
                    }else{
                        throw new ErrorException("Grade updating failed. Please try again.", 1);
                    }
            	}else{
            		throw new ErrorException("Error on updating.", 1);
            	}
            }else{
                throw new ErrorException("Invalid User type", 1);
            }

            }catch(ErrorException $e){
                $transaction->rollBack();
                echo "<p class='bg-danger'>".$e->getMessage()."</p>";
                die();
            }
    	}else{
    		echo "Check if the request is approved.?";die();
    	}

    	// echo json_encode(['action Id: '=>$actionId,'Req. Id: '=>$reqId]);die();
    }

    // Get course re-assessment requests made in this semester.
    public function actionGetreassessrequests(){
    	$this->__constructor();
        $reqs = NULL;
        if(Yii::$app->user->can('DepartmentHead')){

            $ghInst = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);
            $instDept = $ghInst->inst_dep_relId;


            $reqs = ReassessmentHistory::find()
                ->where([
                    'ras_acadYear_relId'=>$this->acadYearId,
                    'ras_sem_relId'=>$this->semId,
                    'ras_aft_grade_relId'=>NULL,
                    'ras_status'=>1,
                ])
                ->andWhere(['ras_afterMark'=>NULL])
                ->innerJoin('course','reassessment_history.ras_course_relId = course.id')
                ->andWhere(['course.course_dept_relId'=>$instDept])
                ->all();
        }else if(Yii::$app->user->can('Registrar')){
             // $reqs = ReassessmentHistory::find()
             //    ->where([
             //        'ras_acadYear_relId'=>$this->acadYearId,
             //        'ras_sem_relId'=>$this->semId,
             //        'ras_registrarApprove_status'=>NULL,
             //    ])
             //    ->all();
        }
    	

        if ($reqs && count($reqs) > 0) {
            echo "<table class='table table-striped table-condensed table-xs table-bordered table-hover' style='font-size:9pt;'>";
            echo "<caption>Final exam mark obtained by the student, You are expected to put the mark corrected after re-assessment. </caption>";
            echo "<thead style='background-color:#ace;'>
            <th>RegId</th>
            <th>Full Name</th>
            <th>Course Code</th>
            <th>Course Title</th>
            <th>Prev. Mark</th>
            <th colspan=2 style='text-align:center;'>New Mark</th>
            </thead><tbody>";
            $option = '';
            if (Yii::$app->user->can('DepartmentHead')) {

            //     $gradeDropDown ="<option value=''> - </option>";
            //     $grades = \backend\models\GradeScale::find()->where(['<>','gs_gradePoint',0])->all();
            //     if ($grades) {
            //         foreach ($grades as $grade) {
            //              $gradeDropDown .= "<option value='". $grade['id'] ."'>". $grade['gs_ltr_grade']."</option>";
            //         }
            //     }
            // $option = $gradeDropDown;

            }else if (Yii::$app->user->can('Registrar')) {
                $gradeDropDown ="<option value=''> - </option><option value='1'>APPROVE</option>";
                 $option = $gradeDropDown;
            }
            $count = 0;
            foreach ($reqs as $std) {
                $student = \backend\models\Student::findOne(['id'=>$std['ras_std_relId']]);
                $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
                $cc = \backend\models\Course::findOne(['id'=>$std['ras_course_relId']]);

                // if () {
                $option2 = '-';
                if (Yii::$app->user->can('DepartmentHead') && $std['ras_registrarApprove_status'] == NULL) {
                    $option2 = "<option value='' title='Request not approved yet by the registrar.'> - </option>";
                }else if(Yii::$app->user->can('DepartmentHead')){
                    $maxPoint = 0;
                    // get maximum possible point for final exam from course conitious assessment catagory setup table.
                    if (!is_null($std->ras_contAssCatag_relId)) {
                        $maxPoint = \backend\models\ContAssessCatagory2::findOne(['id'=>$std->ras_contAssCatag_relId]);
                        if ( $maxPoint  && count( $maxPoint ) > 0) {
                             $maxPoint =  $maxPoint->cat_maxPoint;
                        }
                    }
                    $option2 = "<input type='Number' id='{$std->id}_ti' step='0.01' max='{$maxPoint}' place-holder='mark after check.'/>";
                }
                    // <select title='select action' onchange='saveGrade(".$std['id'].",$(this).val())'>
                    // ".$option."
                    // </select>

                echo "<tr>
                <td>". $student->std_regId ."</td>
                <td>". $usr->first_name.' '. $usr->middle_name ."</td>
                <td>". $cc->course_code ."</td>
                <td>". $cc->course_title ."</td>
                <td style='text-align:center;'>{$std->ras_prevMark}/{$maxPoint}</td>
                <td>{$option2}</td>
                <td><button class='btn btn-xs btn-success' onclick='saveGrade($std->id)'>Save</button></td>
                </tr>";
                // ++$count;
            }
            echo "</tbody>";
            // echo "<tfoot><p class='bg-success'> <i class='bg-danger'>". $count ."</i> Requests.</p></tfoot>";
            echo "</table>";die();
        }else{
            die("<p class='bg-danger'>No request.</p>");
        }
    }
}

