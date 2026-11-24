<?php

namespace frontend\controllers;
use yii;
use \backend\models\SemesterAdvisor;
use \frontend\models\SemesterRegistration2Detail;
use \backend\models\CourseOffering2Detail;

class SubmitProjectCourseGradeController extends \yii\web\Controller
{
    public function actionIndex()
    {
    	if (Yii::$app->user->can('DepartmentHead') || Yii::$app->user->can('Instructor')) {
    		$intId = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);
    		$saModel = new SemesterAdvisor();
    		$adObj = $saModel->isAdvisor($intId->id);
    		if($adObj && count($adObj) > 0){
    			return $this->render('index');
    		}else{
    			// Yii::$app->session->setFlash('type', "");
    			Yii::$app->session->setFlash('error', "You are Not Assigned as Semester Advisor in this Semester.");
    			return $this->render('index');
    		}
    	}else{
    		Yii::$app->session->setFlash('message', "Un-Privillege user.");
			return $this->render('index');
    	}
    }

    public function actionGetCourses(){
    	if (Yii::$app->user->can('DepartmentHead') || Yii::$app->user->can('Instructor')) {
    		$intId = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);

    		$saModel = new SemesterAdvisor();
    		$srd2Model = new SemesterRegistration2Detail();
    		$coffModel = new CourseOffering2Detail();
    		$adObj = $saModel->isAdvisor($intId->id);
    		$gsetIds = [];
    		if($adObj && count($adObj) > 0){
    			// echo "<br>Assigned Advisor -> YES<br>";
    			$gsets = [];
    			foreach ($adObj as $key) {
    				$gs = $saModel->getGsets($key);
    				if ($gs && count($gs) > 0) {
    					foreach ($gs as $WGroupSetupKey) {
    						$gsetIds[] = $WGroupSetupKey->id;
    					}
    				}
    			}
	    		$gsetIds = array_unique($gsetIds);

	    		$displayTable = "";
	    		$displayedIds = [];
	    		if ($gsetIds && count($gsetIds) > 0) {

	    			$offeredProjectCourses = $coffModel->getOfferedProjectCourses($gsetIds,null,$intId);
	    			if ($offeredProjectCourses && count($offeredProjectCourses) > 0) {
	    				$displayTable .= "<table class='table table-condensed table-hover table-striped table-bordered' style='font-size:9pt;'> ";
	    				$displayTable .= "<caption style='color:black; font-weight:bold; font-size:10pt;'>
	    									<center>
	    										Project Courses Enrolled in this Semester
	    									</center>
	    								</caption>";
	    				$displayTable .= "<thead style='background-color:#8D0;'>
							<th style='border:1px solid black;'>#</th>
							<th style='border:1px solid black;'>Course Code</th>
							<th style='border:1px solid black;'>Course Title</th>
							<th style='border:1px solid black;'>Group</th>
							<th style='border:1px solid black;'>Batch</th>
							<th style='border:1px solid black;'>Show</th>
						</thead><tbody>";
	    				$count = 0;
						foreach ($offeredProjectCourses as $coffDetKey) {

							$aKey = $coffDetKey->coff2detCoff2Rel->coff2GroupRel;
							if (in_array($coffDetKey->id, $displayedIds)) {
								continue;
							}else{
								$displayedIds[] = $coffDetKey->id;
							}
							$btn = "<button class='btn btn-primary btn-xs' title='Show Students' onclick='loadStudent({$coffDetKey->id})' >Students</button>";
							$displayTable .= "<tr>
								<td style='border:1px solid black;'>".++$count."</td>
								<td style='border:1px solid black;'>{$coffDetKey->coff2detCourseRel->course_code}</td>
								<td style='border:1px solid black;'>{$coffDetKey->coff2detCourseRel->course_title}</td>
								<td style='border:1px solid black;'>{$aKey->gsGroupRel->group_name}<b>({$aKey->gsAdmRel->admission_name[0]}{$aKey->gsProgRel->program_name[0]})</b></td>
								<td style='border:1px solid black;'>Year {$aKey->gsClslvlRel->clslvl_num}</td>
								<td style='border:1px solid black;'>{$btn}</td>
								</tr>";
						}
					$displayTable .= "</tbody></table>";
					die("{$displayTable}");
	    			}else{
	    				die("<p class='bg-danger'>No Project course Offered for batches.</p>");
	    			}
	    		}else{
	    			die("<p class='bg-danger'>No group setup found...</p>");
	    		}
    		}else{
    			Yii::$app->session->setFlash('message', "You are Not Assigned as Semester Advisor in this Semester.");
    			return;
    		}
    	}
    }

}
