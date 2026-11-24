<?php

namespace frontend\controllers;
use yii;
use \common\components\CommonControllerFunctions as CCF;

class MyGradesController extends \yii\web\Controller
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

    // checks whether the student is assigned to a group , returns gset object on success, || error message 
    protected function checkGroupAssigned($std){
        $this->__constructor();
        $gas = \backend\models\WGroupAssignment::find()->innerJoinWith(
                'gasGsetRel',
                    'w_group_setup.id = w_group_assignment.gas_gs_relId',
                    'w_group_setup.gs_acadYear_relId = '.$this->acadYearId,
                    'w_group_setup.gs_sem_relId = '.$this->semId
                )
                ->andWhere([
                    'gas_std_relId'=>$std->id
                ])
                ->One();

            if (is_null($gas)) {
                die('You are not assigned to a group in this semester, please contact the registrar staffs.');
            }else{
                return $gas->gasGsetRel;
            }
            // $gset = \backend\models\WGroupSetup::findOne(['id'=>$gas->gas_gset_relId]);
            // return $gset;
    }

    public function actionGetSemesterGrades(){
    	$this->__constructor();
    	$student = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);
        $student2 = $student;
        // CCF::currentStudentGroup($student);
    	$semster = isset($_POST['semester']) && !empty($_POST['semester'])?intval($_POST['semester']):(Yii::$app->params['C']['S'] == 'I'?1:(Yii::$app->params['C']['S'] == 'II'?2:'Summer'));
    	$clslvl = isset($_POST['clslvl']) && !empty($_POST['clslvl'])?$_POST['clslvl']:1;

        // 'clslvl_admission_relId'=>$student->std_admission_relId
        $clslvlObj = \backend\models\ClassLevel::findOne([
            'clslvl_num'=>$clslvl,
            'clslvl_program_relId'=>$student->std_prog_relId,
        ]);

        $semesterName = $semster == 1?'I':($semster == 2?'II':'Summer');
        $semsterObj = \backend\models\Semester::findOne([
            'semester_name'=>$semesterName
        ]);

        $gset = CCF::getStudentGroupHistoryPublic($clslvlObj->id,$semsterObj->id,$student->id);
        // 
        if ($gset && count($gset) > 0) {
        }else{
            return "Uknown Result.";
            // die('Error with your group assignment. please contact registrar staff if this is error message');
        }

        // var_dump($clslvlObj,$semsterObj,$student->id);die();

    	$gradeHistory = CCF::getSemesterGrade($clslvlObj->id,$semsterObj->id,$student->id);

        // echo "<pre>";
        // var_dump($gradeHistory);
        // die();

        $clslvlNum = $clslvlObj->clslvl_num;
        // $semesterN = $semsterObj;
        $semesterNum = $semsterObj->semester_name == 'I'?1:($semsterObj->semester_name == 'II'?2:$semsterObj->semester_name);

        $gradeTable = "";

        $admission  = $gset->gsAdmRel->admission_name;
        $program  = $gset->gsProgRel->program_name;
        if (is_string($gradeHistory)) {// Course Grade completion errror for the selected class level.
            echo json_encode([
                'clslvl'=>$clslvlNum,
                'semester'=>$gset->gsSemRel->semester_name,
                'gradeTable'=>$gradeHistory,
            ]);
            die();
            // die("<pre>{$gradeHistory}</pre>");
        }


        $gradeTable .= "<table style='font-size:9pt;' class='table table-hover table-sm table-bordered table-condensed'>";
        $gradeTable .= "<caption>
                            <p class='bg-success'> 
                            Class Level: <u><b>{$clslvlNum}</b></u>\tSemester: <u><b>{$semster}</b></u>\t<br>
                            Admision: <u><b>{$admission}</b></u>\tProgram: <u><b>{$program}</b></u>\t
                            </p>
                        </caption>";
        $gradeTable .= "<thead style='background-color:#ace;'>
                <th>#</th>
                <th>Course Code</th><th> Course Title</th>
                <th>CrdtHr</th>
                <th>Grade</th>
                <th>Grd. Pt.</th>
                <th>Crs. Added</th>
                <th>Apprvd</th>
            </thead><tbody>";
        $count = 0;
        $evaltdAll = TRUE;
        $evalueeId2 = NULL;
        $evalueeId = NULL;
        $approved = TRUE;
        foreach ($gradeHistory[0] as $std) {

            $gid = $std['srd2_grade_relId'];
            $grade = is_null($gid)?'-':\backend\models\GradeScale::findOne(['id'=>$gid]);
            $caddda = is_null($std['srd2_coadd_flag'])?'NO':'YES';

            $course = \backend\models\Course::findOne(['id'=>$std['srd2_course_relId']]);
            // 
            $coff3 = NULL;
            $grrrp = NULL;
            // if the course added, take group of addedWith not the student group setup.
            if (!is_null($std['srd2_coadd_flag'])) { //indicates the course is added.
               $addHistory = \backend\models\AddHistory::findOne(['id'=>$std['srd2_coadd_flag']]);
               $addWithGroup = $addHistory->ah_addWithGroup_relId;
               // $evtd = CCF::checkIfEvaluated($addWithGroup,$course->id,$evalueeId,Yii::$app->user->identity->id);
               $coff3 = CCF::getCourseInstructorForGroup($addWithGroup, $course->id);
               $grrrp = $addWithGroup;
            }else{
                 $coff3 = CCF::getCourseInstructorForGroup($gset->id, $course->id);
                 $grrrp = $gset->id;
            }
            // If not evaluated any of the instructors, make $gradeHistory[1] = NULL, no need of calculating.

            // var_dump(is_object($grade));die();


            $ltrgrd = '';
            $grpt = 0;
            if (is_object($grade) && count($grade) > 0) {
                $ltrgrd = $grade->gs_ltr_grade;
                $grpt = $grade->gs_gradePoint * $course->course_crdthr;
            }else{
                $ltrgrd = $grade;
                $grpt = 0;
            }

            // if (is_null($coff3['coff2det_inst_relId']) || !is_null($coff3['coff2det_registrarApproved_by'])) {
            //     return true;
            // }

            if ($coff3 && count($coff3) > 0) {
                if (
                    is_null($coff3['coff2det_inst_relId']) || 
                    !is_null($coff3['coff2det_registrarApproved_by']) || 
                    $coff3->coff2detCourseRel->course_crdthr == 0 ||
                    $coff3->coff2detCocatRel->cocat_courseProject == 1
                ) {
                   $evalueeId = NULL;
                }else{
                    $evalueeId = \backend\models\Instructor::findOne(['id'=>$coff3['coff2det_inst_relId']])->inst_user_relId;
                    $evalueeId2 = is_null($coff3['coff2det_TA_relId'])?NULL:
                        (!is_null($coff3['coff2det_registrarApproved_by']) || strtoupper($std->srd2SemregRel->semregStdRel->stdProgRel->program_name) == 'MASTERS' ? NULL:
                                \backend\models\Instructor::findOne(['id'=>$coff3['coff2det_TA_relId']])->inst_user_relId);
                }
                
                
               // $evtd = is_null($evalueeId)?TRUE :CCF::checkIfEvaluated($grrrp,$course->id,$evalueeId,Yii::$app->user->identity->id);
               // $evtd2 = is_null($evalueeId2)?TRUE:CCF::checkIfEvaluated($grrrp,$course->id,$evalueeId2,Yii::$app->user->identity->id);
                if (!$std->evaluatedCourseInstructors()) {
                    $ltrgrd = 'NE';
                    $grpt = 0;
                    $evaltdAll = FALSE;
                }else{
                    // $evaltdAll = TRUE;
                    // student has evaluated and check for if the HoD approved the grade submission
                    if ($std['srd2_gradeApproved'] == 1) {
                        // Indicates the grade is approved.
                        // $approved = TRUE;
                    }else{
                        $ltrgrd = 'NA';
                        $grpt = 0;
                        $approved = FALSE;
                    }
                }
            }else{
                $gradeHistory[1] = 
                    "<p class='bg-danger'>
                        <b class='bg-warning'><i>SE -></i></b> Semester evaluation error. please check it.<br>
                    </p>
                        ";
            }

            $trr = is_null($std['srd2_gradeApproved'])?'0':$std['srd2_gradeApproved'];

            $gradeTable .= "<tr>
                  <td>". ++$count."</td>
                  <td style='text-align:center;'>". $course->course_code . '</td>
                  <td>'.$course->course_title ."</td>
                  <td style='text-align:center;'>". $course->course_crdthr ."</td>
                  <td style='text-align:center;'>". $ltrgrd ."</td>
                  <td style='text-align:center;'>". $grpt ."</td>
                  <td style='text-align:center;'>".$caddda."</td>
                  <td style='text-align:center;'>".$trr."</td>
            </tr>";
        }

        $srd2Model = new \frontend\models\SemesterRegistration2Detail();
        // <input type='hidden' value='{$gsetId}' id='_gsetId' />
        $count1 = $count==0?'NO course ':($count == 1?$count.' course ':$count.' courses ');
        $gradeTable .= "</tbody><tfoot style='background-color:#ddf;'>
                <tr>
                <td colspan =8>
                ";
        //var_dump($evaltdAll,$approved);
        if ($evaltdAll && $approved) {
            
        }else{

            $gradeHistory[1] = 
                    "<p class='bg-danger'>
                        <b class='bg-warning'><i>NE -></i></b> Instructor not evaluated yet.<br>
                        <b class='bg-warning'><i>NA -></i></b> Course grade not approved yet.<br>
                    </p>
                        ";
        }
        if (is_array($gradeHistory[1])) {
            //round($SEM_TOTAL_GP,2,PHP_ROUND_HALF_UP)
            // $prevStatus = CCF::getStudentSemesterStatus($gpa);
            
            $SGPA = round($gradeHistory[1][0][0],2,PHP_ROUND_HALF_UP);
            $CGPA = round($gradeHistory[1][2][0],2,PHP_ROUND_HALF_UP);
            $semesterStatusObject = $gradeHistory[1][2][3][0];

            $semesterStatusObject = NULL;

            // isset($gradeHistory[1][1][4]) && count($gradeHistory[1][1][4]) > 0
            if (isset($gset) && count($gset) > 0) {
                if (isset($gset) && count($gset) > 0) {
                    $satatus1 = $srd2Model->getSemregRecord($student->id,$gset->gs_clslvl_relId,$gset->gs_sem_relId,$gset,NULL)[2];
                    $satatus2 = $srd2Model->getSemregRecordPrev($student->id,$gset,NULL)[2];
                }else{
                    $satatus1 = 'GROUP ERROR';
                }
            }

            $rrrr = is_numeric($gradeHistory[1][1][0])?round($gradeHistory[1][1][0],2,PHP_ROUND_HALF_UP):$gradeHistory[1][1][0];
            $eeee = is_numeric($gradeHistory[1][2][0])?sprintf("%0.2f",round($gradeHistory[1][2][0],2,PHP_ROUND_HALF_UP)):(sprintf("%0.2f",round(($gradeHistory[1][2][1]/$gradeHistory[1][2][2]),2,PHP_ROUND_HALF_UP)));
            $bbb = is_numeric($gradeHistory[1][1][1])?sprintf("%0.2f",round($gradeHistory[1][1][1],2,PHP_ROUND_HALF_UP)):$gradeHistory[1][1][1];
            $cer = is_numeric($gradeHistory[1][0][1])?sprintf("%0.2f",round($gradeHistory[1][0][1],2,PHP_ROUND_HALF_UP)):$gradeHistory[1][0][1];
            // $CGPA = $CGPA == 0?'-':sprintf("%0.2f",$CGPA);//

            $gradeTable .= "<table class='table table-sm table-condensed'>
                            <thead>
                                <th> </th>
                                <th>Crdthr</th>
                                <th>Grade point</th>
                                <th>Grade point Avrg.</th>
                                </thead><tbody style='text-align:center;'>
                            <tr>
                                <td>Previous Total / Commulative</td>
                                <td>".$gradeHistory[1][1][2] ."</td>
                                <td>". $bbb ."</td>
                                <td>". $rrrr ."</td>
                            </tr>
                            <tr>
                                <td>This Semester</td>
                                <td>". $gradeHistory[1][0][2] ."</td>
                                <td>". $cer ."</td>
                                <td>". sprintf("%0.2f",round($gradeHistory[1][0][0],2,PHP_ROUND_HALF_UP)) ."</td>
                            </tr>
                            <tr>
                                <td>Current Total/Commulative</td>
                                <td>". $gradeHistory[1][2][2] ."</td>
                                <td>". sprintf("%0.2f",round($gradeHistory[1][2][1],2,PHP_ROUND_HALF_UP)) ."</td>
                                <td>". $eeee."</td>
                            </tr>
                            <tr>
                            <td colspan = 3>Prev. Status: <u><b>{$satatus2}</b></u></td>
                            <td colspan = 3>This Sem. Status: <u><b>{$satatus1}</b></u></td>
                            </tr>
                            <tr><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td></td><td></td></tr>
                        </tbody>
                        <tfoot style='text-align:center; margin-top:60px;'>
                            <td colspan = 3>Registrar</td>
                            <td colspan = 3>Date:</td>
                            
                        </tfoot>
                        </table>";
        }else{
            $gradeTable .= "{$gradeHistory[1]}";
        }

        $gradeTable .= "</td></tr></tfoot></table>";

        echo json_encode([
            'clslvl'=>$gset->gsClslvlRel->clslvl_num,
            'semester'=>$gset->gsSemRel->semester_name,
            'gradeTable'=>$gradeTable,
        ]);
        //var_dump();
        die();
    }
}
