<?php

namespace frontend\controllers;
use backend\models\WGroupSetup;
use \common\components\CommonControllerFunctions AS CCF;
use \frontend\models\SemesterRegistration2Detail;

class GraduationElegiblityReportController extends \yii\web\Controller
{
    public function actionIndex()
    {

        $model = new \backend\models\StudentSemesterStatusHistory();
        return $this->render('index',['model'=>$model]);
        // return $this->render('index');
    }

    public function actionGetAdStudentList(){
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])?intval($_POST['acadYearId']):die('acadYearId Error..');
        $semId = isset($_POST['semId']) && !empty($_POST['semId'])?intval($_POST['semId']):die('semId Error..');
        $admId = isset($_POST['admId']) && !empty($_POST['admId'])?intval($_POST['admId']):die('Uknown Admission');
        $progId = isset($_POST['progId']) && !empty($_POST['progId'])?intval($_POST['progId']):die('Uknown Program');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])?intval($_POST['clslvlId']):die('Uknown Class Year.');
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId'])?intval($_POST['deptId']):die('Uknown Department.');

        $gs = new WGroupSetup();
        $gsets = $gs->getGsets($admId,$progId,$deptId,$clslvlId,$acadYearId,$semId,$_POST['specId']);
        // getGsets($admId,$progId,$deptId,$clslvlId,$acadYear,$semId)

        $ext = false;
        if ($gsets && count($gsets) > 0) {

            // check if Graduating Class

            $clslvlObj = \backend\models\ClassLevel::findOne($clslvlId);
            $semObj = \backend\models\Semester::findOne($semId);
            $ayObj = \backend\models\SchoolYear::findOne($acadYearId);
            $progObj = \backend\models\Program::findOne($progId);
            $admObj = \backend\models\Admission::findOne($admId);
            $deptObj = \backend\models\Department::findOne($deptId);

            if (!($clslvlObj->clslvl_num == 5 || $clslvlObj->clslvl_num == 2)) {
                return "<p class='bg-danger'>Not Graduating Class!.</p>";
            }
            $spec = isset($_POST['specId']) && !empty($_POST['specId']) ? \backend\models\Specialization::findOne($_POST['specId'])->spec_name:'-';
            $ext = $admObj->admission_name == 'Extension'?true:false;

            $colCount = $clslvlObj->clslvl_num * 3 * ($ext?3:2);
            $cl = $clslvlObj->clslvl_num == 1?"1<sup>st</sup>":($clslvlObj->clslvl_num == 2? "2<sup>nd</sup>":($clslvlObj->clslvl_num == 3?"3<sup>rd</sup>":$clslvlObj->clslvl_num."<sup>th</sup>"));

            echo "<table class='table table-striped table-hover table-condensed table-sm' style='font-size:9pt;'>";
            echo "<caption style='font-weight: normal; color:black;'>
                <center>
                    ". strtoupper(Yii::$app->params['client']) .", ". strtoupper(Yii::$app->params['college']) ."<br>
                    OFFICE OF THE REGISTRAR<br>
                    <b><u>Student Graduation Eligiblity Status</u></b><br>
                    <b><u>{$spec}</u></b><br>
                    <b><u>Departmetn Of {$deptObj->dept_name}</u></b><br>
                    {$semObj->semester_name} Semester of Academic Year {$ayObj->sch_year_desc}<br>

                    Program : <u>{$admObj->admission_name} {$progObj->program_name} {$cl}Year </u>
                </center>
            </caption>";
            echo "<thead style='background-color: #8D0;'>";
            echo "<th style='border:1px solid black; vertical-align:middle;'>#</th>
                <th style='border:1px solid black; vertical-align:middle; text-align:left;'>Reg. NO.</th>
                <th style='border:1px solid black; vertical-align:middle; text-align:left;'>Full Name</th>
                <th style='border:1px solid black; vertical-align:middle; text-align:left;'>CGPA</th>
                <th style='border:1px solid black; vertical-align:middle; text-align:left;'>Eligible</th>
                <th style='border:1px solid black; vertical-align:middle; text-align:left;'>Special Mention</th>
                <th style='border:1px solid black; vertical-align:middle; text-align:left;'>Remark</th>
                </thead><tbody>";
            $count = 0;
            $semregObj = new SemesterRegistration2Detail();
            foreach ($gsets as $gset) {

                $stds = \backend\models\WGroupAssignment::find()->where(['gas_gset_relId'=>$gset->id])->all();
                $cGset = NULL;

                if ($stds && count($stds) > 0) {
                    foreach ($stds as $std) {
                        $student = $std->gasStdRel;
                        $stdRow = "";
                        $showRow = true;
                        if ($student && count($student) > 0) {
                            // $cGset = CCF::currentStudentGroup($student);
                            // $cGset = CCF::currentStudentGroup($student);
                           $cGset = CCF::getStudentGroupHistoryPublic($clslvlObj->id,$semObj->id,$student->id);
                        }else{
                            continue;
                        }
                        $Eligible = $semregObj->isEligible($student,$gset);
                        // 
                        $speMent = strtoupper($gset->gsProgRel->program_name) == 'MASTERS'?'':$this->getSpecialMention($Eligible[1],$Eligible[0]);
                        $stdRow .= "<tr onclick='adClas({$count})' id='row_{$count}'>";
                        $stdRow .= "<td style='border:1px solid black;'>".++$count."</td>";
                        $stdRow .= "<td style='border:1px solid black; text-align:left;'>{$student->std_regId}</td>
                            <td style='border:1px solid black; text-align:left;'>{$student->stdUserRel->first_name} {$student->stdUserRel->middle_name} {$student->stdUserRel->last_name} <b>({$student->stdDeptRel->dept_code})</b>
                            </td>
                            <td style='border:1px solid black; text-align:center;'><b>{$Eligible[0]}</b></td>
                            <td style='border:1px solid black; text-align:center;'>{$Eligible[1]}</td>
                            <td style='border:1px solid black; text-align:center;'>{$speMent}</td>
                            <td style='border:1px solid black; text-align:left;'>{$Eligible[2]}</td>
                            ";                      
                        echo $stdRow;
                        echo "</tr>";
                    }
                }
            }
            echo "</tbody></table>";
        }else{
            echo "No Result.";
        }
    }

    private function getSpecialMention($eleigible,$CGPA){

            if($eleigible == 'YES'){
                $gcSMControl = \backend\models\GcSpecialMention::find()
                    ->where(':CGPA between gsm_minCGPA AND gsm_maxCGPA', [':CGPA' => $CGPA] )
                    ->one();
                return ($gcSMControl && count($gcSMControl)> 0)? $gcSMControl->gsm_name:"";
            }
            return ' --- ';
    }
    private function getSemGrade($stdId,$clslvl,$sem,$cGset){
        $semObject = \backend\models\Semester::findOne(['semester_name'=>$sem]);
        $clslvlObj = \backend\models\ClassLevel::findOne(['clslvl_num'=>$clslvl]);
        $stdObject = \backend\models\Student::findOne($stdId);

        $semregObj = new SemesterRegistration2Detail();
        $semReport = $semregObj->getSemregRecord($stdId,$clslvlObj->id,$semObject->id,$cGset);


        if (is_array( $semReport )) {
            $semReport[0] = is_numeric($semReport[0])?sprintf("%0.2f",$semReport[0]):$semReport[0];
            $semReport[1] = is_numeric($semReport[1])?sprintf("%0.2f",$semReport[1]):$semReport[1];
            return $semReport;
        }
        return [NULL,NULL,NULL];
    }

}
