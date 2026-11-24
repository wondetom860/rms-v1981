<?php

namespace frontend\controllers;

class CourseAlergicController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetCaStudents(){
    	$caStudents = \frontend\models\SemesterRegistration2Detail::getCaStudets();
		$rep = "";
    	if ($caStudents && count($caStudents) > 0) {
    		$rep .= "<table class='table table-striped table-condensed table-sm table-bordered'> ";
    		$rep .= "<thead style='background-color:#8D0;'>
    				<th>#</th>
    				<th>Reg. ID</th>
                    <th style='text-align:center;'>Program</th>
    				<th>Full Name</th>
    				<th>Course</th>
                    <th>Registration Info.</th>
    			</thead><tbody>";
    		$count	= 0;
    		foreach ($caStudents as $key) {
    			$std = $key->srd2SemregRel->semregStdRel;
    			$course = $key->srd2CourseRel;

                $title = $std->stdAdmissionRel->admission_name;
                $prog = $title[0];
                $title .= ' '.$std->stdProgRel->program_name;
                $prog .= ' '.$std->stdProgRel->program_name[0];
    			// $user = $std->stdUserRel;

    			$rep .= "<tr>
    				<td style='text-align:center;'>".++$count."</td>
    				<td>{$std->std_regId}</td>
                    <td style='text-align:center;' title='{$title}'><b>[{$prog}]</b></td>
    				<td>{$std->getFullName()}</td>
    				<td>{$course->course_code} => {$course->course_title}</td>
                    <td>{$key->srd2GroupRel->gsAcadYearRel->sch_year_desc} - {$key->srd2GroupRel->gsSemRel->semester_name} <span style='float:right; font-weight:bold;'>[&nbsp;&nbsp;{$key->srd2GradeRel->gs_ltr_grade}&nbsp;&nbsp;]</span>&nbsp;&nbsp;&nbsp;</td>
    				</tr>";
    		}
    		$rep .= "</tbody></table>";
			return $rep;
    	}else{
    		echo "<p class='bg-success'>No record found.</p>";
    	}
    }

}
