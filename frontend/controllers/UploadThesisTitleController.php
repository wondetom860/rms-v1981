<?php

namespace frontend\controllers;
use yii;
use \backend\models\SemesterAdvisor;
use \frontend\models\SemesterRegistration2Detail;
use \backend\models\CourseOffering2Detail;
use yii\helpers\Html;

class UploadThesisTitleController extends \yii\web\Controller
{
    public function actionIndex(){
    	if (Yii::$app->user->can('Instructor')) {
    		$intId = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);
    		$saModel = new SemesterAdvisor();
    		$adObj = $saModel->isAdvisor($intId->id);
    		$srd2Model = new SemesterRegistration2Detail();
	    	$coffModel = new CourseOffering2Detail();
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

	    			$offeredProjectCourses = $coffModel->getOfferedProjectCourses($gsetIds);//1 for filtering Thesis Phase II
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
					// die("{$displayTable}");
	    			}else{
	    				$displayTable = "<p class='bg-danger'>Thesis NOT enrolled</p>";
	    			}
	    		}else{
	    			$displayTable = "<p class='bg-danger'>No group setup found...</p>";
	    		}

	    		return $this->render('index_cordinator',['response'=>$displayTable]);
    		}else{
    			Yii::$app->session->setFlash('message', "You are Not Assigned as Semester Advisor in this Semester.");
    			return $this->render('index');
    		}
    	}else if(Yii::$app->user->can('DepartmentHead')){
    		return $this->redirect('/thesis-title/');
    	}else{
    		if (Yii::$app->user->can('Student')) {

    			$student = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);
    			// check if student enrolled Thesis
		    	$modelSrd2 = new SemesterRegistration2Detail();
		    	$thesisEnrolleds = $modelSrd2->enrolledThesis2($student->id);

		    	// if ($thesisEnrolled && count($thesisEnrolled) > 0) {
		    	// 	// show Thesis titles submitted by the student
	    		// 	$modelThesisTitle = new \backend\models\ThesisTitle();
			    // 	$titles = $modelThesisTitle->find()->where(['tt_std_relId'=>$student->id])->all();
			    // 	if (!($titles && count($titles) > 0)) {
			    // 		$titles = 'Title Not submitted yet.';
			    // 	}
			    // 	$srd2Id = $thesisEnrolled->id;
		    	// }else{
		    	// 	$srd2Id = NULL;
		    	// 	$titles = "Not Enrolled Thesis";
		    	// }
    			

		    	return $this->render('index_titles_student',['projectSrd2s' => $thesisEnrolleds ]);
    		}
    		Yii::$app->session->setFlash('message', "Un-Privillege user.");
			return $this->render('index');
    	}
    }

    public function actionSubmittedThesisTitles(){
    	$stdId = isset($_POST['stdId']) && !empty($_POST['stdId']) ? intval($_POST['stdId']):die("Select Student!");

    	// get Thesis titles submitted by this student
    	$modelThesisTitle = new \backend\models\ThesisTitle();
    	$titles = $modelThesisTitle->find()->where(['tt_std_relId'=>$stdId])->all();

    	return $this->renderPartial('index_titles',['titles'=>$titles]);
    }

    public function actionUploadTitle(){
    	$title = isset($_POST['title']) && !empty($_POST['title'])?(string)$_POST['title']:die("Title Empity");
    	$srd2Id = isset($_POST['srd2Id']) && !empty($_POST['srd2Id'])?intval($_POST['srd2Id']):die("Thesis Semester registration entry error!");

    	if (Yii::$app->user->can('Student')) {
    		$student = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);

    		$transaction = Yii::$app->db->beginTransaction();
    		try {
    			$reg = $this->foramtReg($student);
	    		$fn = $reg.str_pad(rand(0,10000), 4).'.txt';
	    		$filename = "uploads/thesisTitles/".$fn;

	    		$filehandler = fopen($filename, 'wb');

	    		$content = Html::encode($title);

	    		// going safe
	    		$modelThesisTitle = new \backend\models\ThesisTitle();

				$modelThesisTitle->tt_std_relId = $student->id;
				$modelThesisTitle->tt_title = $fn;
				$modelThesisTitle->tt_srd2_relId = $srd2Id;
				$modelThesisTitle->tt_graded = NULL;

				if ($modelThesisTitle->save()) {
					fwrite($filehandler, $content);
					fclose($filehandler);
					$transaction->commit();
					return 1;
				}

    		} catch (\ErrorException $e) {
    			$transaction->rollBack();
    			echo "{$e->getMessage()}";
    			die();
    		}
    	}
    }

    private function foramtReg($student){
    	return str_ireplace('/', '_',$student->std_regId);
    }

}
