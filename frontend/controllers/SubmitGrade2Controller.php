<?php

namespace frontend\controllers;

use yii;
use yii\helpers\ArrayHelper;
use backend\models\CourseOffering2;
use backend\models\CourseOffering2Detail;
use frontend\models\SemesterRegistration2;
use frontend\models\SemesterRegistration2Detail;
use backend\models\User;
use backend\models\GradeScale;
use backend\models\Student;
use \common\components\CommonControllerFunctions as CCF;

class SubmitGrade2Controller extends \yii\web\Controller
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

    // Drop submitted student grade
    public function actionHfgfvbsfgshgfjsdfat(){
    	$courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])?$_POST['courseId']:die('Uknown Course');
    	$regDetailId = isset($_POST['regDetailId']) && !empty($_POST['regDetailId'])?$_POST['regDetailId']:die('Error with you data, please re-try again. ERROR_WT_2019');

    	$stdId = isset($_POST['stdId']) && !empty($_POST['stdId'])?$_POST['stdId']:die('Error UKNOWN STUDENT [ERROR_WT_2012]');

    	
    	$this->__constructor();

		$semregEntry = SemesterRegistration2::find()->where([
            'semreg_acadYear_relId'=>$this->acadYearId,
            'semreg_semester_relId'=>$this->semId,
            'semreg_std_relId'=>intval($stdId),
        ])->one();

    	if ($semregEntry) {
    		
    		$result = Yii::$app->db->createCommand()
    		->update(
    			'semester_registration_2_detail',
    			[
    				'srd2_grade_relId'=>NULL,
    			],
    			[
    				'srd2_semreg_relId'=>$semregEntry['id'],
    				'srd2_course_relId'=>intval($courseId),
    			])
    		->execute(); 

    		if ($result) {
    			//echo $this->checkGradeCompleted($semregEntry['id'],$courseId,NULL,intval($stdId));
                echo 1;
    		}else{
    			echo 'ERROR_WT_2020';
    		}
    	}else{
    		die('Error With your data: UKNOWN STUDENT');
    	}

	// echo json_encode([
	// 	'total'=>$total,
	// 	'success'=>$successCount,
	// 	'failed'=>$failCount,
	// ]);die();
    }

    private function Dfjgsjfgjgkldhkj(){
        $studentList = isset($_POST['studentList']) && !empty($_POST['studentList'])?$_POST['studentList']:die('Error in Student List [ERROR_WT_2012]');
        // $srd2s = isset($_POST['srd2s']) && !empty($_POST['srd2s'])?$_POST['srd2s']:die('Semester Regisration entry error - [ERROR_WT_2013]');

        // echo "<pre>";
        // var_dump($studentList);
        // die();


        $studentList = json_decode($studentList);

        if (!($studentList) && count($studentList) > 0) {
            die('Error with Student list - Empty List.');
        }

        // $this->__constructor();

        //$repTable = '';
        $successCount = 0;
        $failCount = 0;
        $total = 0;

        foreach ($studentList as $key => $value) {
            $grade = intval($value);
            $grade = $grade == 0 ? NULL : $grade;
            $result = Yii::$app->db->createCommand()
                ->update(
                    'semester_registration_2_detail',
                    [
                        'srd2_grade_relId'=>$grade,
                    ],
                    [
                        'id'=>intval($key),
                        'srd2_grade_relId'=>NULL
                    ])
                ->execute();

            if ($result) {
                $successCount++;
                //$this->checkGradeCompleted($semregEntry['id'],$courseId,$gsetId,intval($key));
            }else{
                $failCount ++;
            }
        }

        echo json_encode([
            'total'=>$total,
            'success'=>$successCount,
            'failed'=>$failCount,
        ]);die();
    }
    // Save students grades 
    public function actionDhfgfvbsfgshgfjsdfaty(){

        if (isset( $_POST['ad']) && !empty( $_POST['ad'])) {
            echo $this->dfjgsjfgjgkldhkj();
        }
    	$instId = isset($_POST['instId']) && !empty($_POST['instId'])?$_POST['instId']:die('Incomplete data [ERROR_WT_2011]');
    	$courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])?$_POST['courseId']:die('Uknown Course');
    	$gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId'])?$_POST['gsetId']:die('Uknown Group');

    	$studentList = isset($_POST['studentList']) && !empty($_POST['studentList'])?$_POST['studentList']:die('Error in Student List [ERROR_WT_2012]');

    	$studentList = json_decode($studentList);

    	if (!($studentList) && count($studentList) > 0) {
    		die('Error with Student list - Empty List.');
    	}
    	$this->__constructor();

    	//$repTable = '';
    	$successCount = 0;
    	$failCount = 0;
    	$total = count($studentList);

    	foreach ($studentList as $key => $value) {
    		$semregEntry = SemesterRegistration2::find()->where([
                'semreg_acadYear_relId'=>$this->acadYearId,
                'semreg_semester_relId'=>$this->semId,
                'semreg_std_relId'=>intval($key),
            ])->one();

	    	if ($semregEntry) {
	    		
	    		$result = Yii::$app->db->createCommand()
	    		->update(
	    			'semester_registration_2_detail',
	    			[
	    				'srd2_grade_relId'=>intval($value),
	    			],
	    			[
	    				'srd2_semreg_relId'=>$semregEntry['id'],
	    				'srd2_course_relId'=>intval($courseId),
	    				'srd2_group_relId'=>intval($gsetId),
	    				'srd2_grade_relId'=>NULL,
	    			])
	    		->execute();

	    		if ($result) {
	    			$successCount++;
	    			//$this->checkGradeCompleted($semregEntry['id'],$courseId,$gsetId,intval($key));
	    		}else{
	    			$failCount ++;
	    		}
	    	}
    	}

    	echo json_encode([
    		'total'=>$total,
    		'success'=>$successCount,
    		'failed'=>$failCount,
    	]);die();

    	// 
    }

    protected function checkGradeCompleted($semregEntryId,$courseId,$gsetId,$studentId){
    	$remGrade = SemesterRegistration2Detail::findOne([
    		'srd2_grade_relId'=>NULL,
    		'srd2_semreg_relId'=>$semregEntryId,
    	]);

    	if ($remGrade) {
    		$result = Yii::$app->db
	    		->createCommand()
	    		->update(
	    			'semester_registration_2',
	    			[
	    				'semreg_gradeCompleted'=>0
	    			],
	    			[
	    				'id'=>$semregEntryId,
	    			])
	    		->execute();
	    	return $result;
    	}else{
    		$result = Yii::$app->db
	    		->createCommand()
	    		->update(
	    			'semester_registration_2',
	    			[
	    				'semreg_gradeCompleted'=>1
	    			],
	    			[
	    				'id'=>$semregEntryId,
	    			])
	    		->execute();
	    	return $result;
    	}
    }

    public function actionSubmittedGradeReport(){
        $rep = "";
    	if(isset($_POST['instId']) && !empty($_POST['instId'])){

    		$this->__constructor(); //Initialize global vars.

    		$coffdetId = intval($_POST['instId']);
    		

    		$coff2DetId = CourseOffering2Detail::findOne(['id'=>$coffdetId]);
            $courseId = $coff2DetId->coff2det_course_relId;
            if ($coff2DetId) {
               $gsetId = CourseOffering2::findOne(['id'=>$coff2DetId->coff2det_coff2_relId])->coff2_group_relId;
            }else{
                echo "<p class='bg-warning'>Invalid Orrefing record.</p>";die();
            }

    		$stds2 = SemesterRegistration2::find()->where([
                'semreg_acadYear_relId'=>$this->acadYearId,
                'semreg_semester_relId'=>$this->semId,
            ]);
    		// Get inst groups from course offering detail.

    		if (!empty($stds2)) {
    			$stds2 = $stds2->all();

    			$gradeArray = '';
    			$grades = \backend\models\GradeScale::find()->all();

    			if ($grades && count($grades) > 0) {
    				$gradeArray .= "<option value=''> - </option>";
    				foreach ($grades as $grade) {
    					$gradeArray .= "<option value='".$grade['id']."'>". $grade['gs_ltr_grade'] ."</option>";
    				}
    			}
    		$course = \backend\models\Course::findOne(['id'=>$courseId]);
    		$rep .= "<table style='margin-top:5px;' class='table table-hover table-sm table-bordered table-condensed' style='font-size:9pt;'>";

    		$rep .= "<caption><p class='bg-success'> Course: <u><b>{$course->course_title}<br></b></u>\t</p></caption>";

            	$rep .= "<thead style='background-color:#ace;'>
                        <th style='border:1px solid black;'>#</th>
                        <th style='border:1px solid black;'>Reg.ID.</th>
                        <th style='border:1px solid black;'>Student Name</th>
                        <th style='border:1px solid black;'>Added</th>
                        <th style='border:1px solid black;'>Grade</th>
                        <th style='border:1px solid black;'>optn.</th>
                    </thead><tbody>";
            	$count = 0;
    			foreach ($stds2 as $std) {

    				$s = Student::findOne(['id'=>$std['semreg_std_relId']]);
    				$u = User::findOne(['id'=>$s->std_user_relId]);

                    $srd =SemesterRegistration2Detail::find()->where([
                        'srd2_semreg_relId'=>$std['id'],
                        'srd2_course_relId'=>$courseId,
                        'srd2_group_relId'=>$gsetId,
                    ])->andWhere(['IS NOT','srd2_grade_relId',NULL])->one();

                    if (empty($srd)) {
                        continue;
                    }
                    $gid = $srd->srd2_grade_relId;
                    $grade = is_null($gid)?'-':GradeScale::findOne(['id'=>$gid])->gs_ltr_grade;
                    $caddda = is_null($srd->srd2_coadd_flag)?'NO':'YES';
                    $drop = ($srd->srd2_gradeApproved == 0)?
                    	"<button class='btn btn-xs btn-warning' title='change grades' onclick='dropGrade({$srd->id},{$courseId},{$s->id})'>Change</button>":'';
    				$rep .= "<tr>
    				      <td style='border:1px solid black;'>". ++$count."</td>
    				      <td style='border:1px solid black;'>". $s->std_regId."</td>
    				      <td class='std_name' id='". $s->id ."' style='border:1px solid black;'>". $u->first_name.' '. $u->middle_name ."</td>
                          <td style='border:1px solid black; font-size:8pt;'>".$caddda."</td>
                          <td style='border:1px solid black; text-align:center;'>{$grade}</td>
                          <td style='border:1px solid black;'>{$drop}</td>
    				</tr>";
    			}

    			$count1 = $count==0?'NO Student ':($count == 1?$count.' Student ':$count.' Students ');
    			$rep .= "</tbody><tfoot style='background-color:#dd7;'>
    			<tr>
    				<td colspan=6><input type='hidden' value='{$gsetId}' id='_gsetId' />
    				<p class='bg-success'><i><b>". $count1 ."</b></i> found.</p>
    			</td>";

    			$rep .= "</tr></tfoot></table>";
                return $rep;
    		}else{
    			echo "<p class='bg-warning'>No student enrolled this course in this semester.</p>";
    		}
		}else{
			echo "<p>No data found.</p>";die();
		}
    }

    public function actionEnrstudlists2(){
        if(isset($_POST['instId']) && !empty($_POST['instId'])){

            $this->__constructor(); //Initialize global vars.

            $coffdetId = intval($_POST['instId']);
            // $courseId = $_POST['courseId'];

            $coff2DetId = CourseOffering2Detail::findOne(['id'=>$coffdetId]);
            $courseId = $coff2DetId->coff2det_course_relId;
            if ($coff2DetId) {
               $gsetId = CourseOffering2::findOne(['id'=>$coff2DetId->coff2det_coff2_relId])->coff2_group_relId;
            }else{
                echo "<p class='bg-warning'>Invalid Orrefing record.</p>";die();
            }

            $stds2 = SemesterRegistration2::find()->where([
                'semreg_acadYear_relId'=>$this->acadYearId,
                'semreg_semester_relId'=>$this->semId,
            ]);
            $gset = \backend\models\WGroupSetup::findOne(['id'=>$gsetId]);
            // Get inst groups from course offering detail.

            if (!empty($stds2)) {
                $stds2 = $stds2->all();

                // 
                $gradeArray = '';
                $grades = \backend\models\GradeScale::find()->where(['gs_forProgram'=>$gset->gs_prog_relId])->all();

                if ($grades && count($grades) > 0) {
                    $gradeArray .= "<option value=''> - </option>";
                    foreach ($grades as $grade) {
                        $gradeArray .= "<option value='".$grade['id']."'>". $grade['gs_ltr_grade'] ."</option>";
                    }
                }

                $fGradedCoursesGrade = [];
                $grades2 = \backend\models\GradeScale::find()->where(['gs_ltr_grade'=>['C','D','F','NG','WD','IC','DO']])->all();
                $wonderep = "";

                if ($grades2 && count($grades2) > 0) {
                    // $gradeArray .= "<option value=''> - </option>";
                    foreach ($grades2 as $grade) {
                        $fGradedCoursesGrade[] = $grade['id'];
                    }
                }                

                $course = \backend\models\Course::findOne(['id'=>$courseId]);
                $group = \backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId])->group_name;
                $dept = \backend\models\Department::findOne(['id'=>$gset->gs_dept_relId])->dept_name;
                $clslvl = \backend\models\ClassLevel::findOne(['id'=>$gset->gs_clslvl_relId])->clslvl_num;

                $spec = is_null($gset->gs_spec_relId)?'-':\backend\models\Specialization::findOne(['id'=>$gset->gs_spec_relId])->spec_name;

                $wonderep .= "<table style='margin-top:5px;' class='table table-hover table-sm table-bordered table-condensed' style='font-size:9pt;'>";

                $wonderep .= "<caption><p class='bg-success'> 
                    Course: <u><b>{$course->course_title}<br></b></u>\t
                    Department: <u><b>{$dept}<br></b></u>\t
                    Class Year: <u><b>{$clslvl}<br></b></u>\t
                    Specialization: <u><b>{$spec}<br></b></u>\t
                    Group: <u><b>{$group}<br></b></u>\t
                    </p></caption>";

                $gg = isset($_POST['ad']) ? 'Thesis Title' : 'Grade';

                $wonderep .= "<thead style='background-color:#ace;'>
                        <th style='border:1px solid black;'>#</th>
                        <th style='border:1px solid black;'>Reg. ID.</th>
                        <th style='border:1px solid black;'>Student Name</th>
                        <th style='border:1px solid black;'>Crs. Added</th>
                        <th style='border:1px solid black;'>{$gg}</th>
                    </thead><tbody>";
                $count = 0;
                $srd2s = [];
                foreach ($stds2 as $std) {

                    $s = Student::findOne(['id'=>$std['semreg_std_relId']]);
                    $u = User::findOne(['id'=>$s->std_user_relId]);

                    // check if the course F graded before,
                    if($this->CourseFGrdaed($courseId,$std['semreg_std_relId'])){
                        $gradeArray .= json_encode($fGradedCoursesGrade);
                    }

                    if (isset($_POST['ad'])) {
                        $srd =SemesterRegistration2Detail::findOne([
                            'srd2_semreg_relId'=>$std['id'],
                            'srd2_course_relId'=>$courseId,
                            'srd2_group_relId'=>$gsetId,
                        ]);
                    }else{
                        $srd =SemesterRegistration2Detail::findOne([
                            'srd2_semreg_relId'=>$std['id'],
                            'srd2_course_relId'=>$courseId,
                            'srd2_group_relId'=>$gsetId,
                            'srd2_grade_relId'=>NULL,
                        ]);
                    }

                    if (empty($srd)) {
                        continue;
                    }
                    $gid = $srd->srd2_grade_relId;
                    //$grade = is_null($gid)?'-':GradeScale::findOne(['id'=>$gid])->gs_ltr_grade;
                    $caddda = is_null($srd->srd2_coadd_flag)?'NO':'YES';
                    $srd2s[] = $srd->id;
                    $inp = isset($_POST['ad']) ? 
                        "<button class='btn btn-xs btn-primary' onclick='loadSubmittedTitles({$srd->srd2SemregRel->semreg_std_relId})'>View Title</button>":
                        "<select class='' id='". $srd->id ."_grade'>". $gradeArray ."";
                    $wonderep .= "<tr>
                          <td style='border:1px solid black;'>". ++$count."</td>
                          <td style='border:1px solid black;'>". $s->std_regId."</td>
                          <td class='std_name' id='". $srd->id ."' style='border:1px solid black;'>". $u->first_name.' '. $u->middle_name ."</td>
                          <td style='border:1px solid black;'>".$caddda."</td>
                          <td style='border:1px solid black;'>{$inp}</select>
                          </td>
                    </tr>";
                }

                $count1 = $count==0?'NO Student ':($count == 1?$count.' Student ':$count.' Students ');
                $wonderep .= "</tbody><tfoot style='background-color:#dd7;'>
                <tr>
                    <td colspan=4><input type='hidden' value='{$gsetId}' id='_gsetId' />
                    <p class='bg-success'><i><b>". $count1 ."</b></i> found.</p>
                </td>";

                if ( $count>0) {
                    $srd2s = json_encode($srd2s);
                    if (isset($_POST['ad'])) {
                        $wonderep .= "
                            <td colspan= 2>
                                <button class='btn btn-sm btn-success' title='Save grades' onclick='dhfgfvbsfgshgfjsdfatydasd()'>Save</button>
                            </td>";
                    }else{
                        $wonderep .= "
                            <td colspan= 2>
                                <button class='btn btn-sm btn-success' title='Save grades' onclick='dhfgfvbsfgshgfjsdfatydasdv()'>Save</button>
                            </td>";
                    }
                    
                }

                $wonderep .= "</tr></tfoot></table>";
                return $wonderep;
            }else{
                echo "<p class='bg-warning'>No student enrolled this course in this semester.</p>";
            }
        }else{
            echo "<p>No data found.</p>";die();
        }
    }

    public function actionEnrstudlists(){
        $wonderep = "";
    	if(isset($_POST['instId']) && !empty($_POST['instId'])){

    		$this->__constructor(); //Initialize global vars.

    		$coffdetId = intval($_POST['instId']);
    		// $courseId = $_POST['courseId'];

    		$coff2DetId = CourseOffering2Detail::findOne(['id'=>$coffdetId]);
            $courseId = $coff2DetId->coff2det_course_relId;
            if ($coff2DetId ) {
               $gsetId = CourseOffering2::findOne(['id'=>$coff2DetId->coff2det_coff2_relId])->coff2_group_relId;
            }else{
                echo "<p class='bg-warning'>Invalid Orrefing record.</p>";die();
            }

    		$stds2 = SemesterRegistration2::find()->where([
                'semreg_acadYear_relId'=>$this->acadYearId,
                'semreg_semester_relId'=>$this->semId,
            ]);
    		// Get inst groups from course offering detail.

            $gset = \backend\models\WGroupSetup::findOne(['id'=>$gsetId]);
    		if (!empty($stds2)) {
    			$stds2 = $stds2->all();

    			$gradeArray = '';
    			$grades = \backend\models\GradeScale::find()->where(['gs_forProgram'=>$gset->gs_prog_relId])->all();

    			if ($grades && count($grades) > 0) {
    				$gradeArray .= "<option value=''> - </option>";
    				foreach ($grades as $grade) {
    					$gradeArray .= "<option value='".$grade['id']."'>". $grade['gs_ltr_grade'] ."</option>";
    				}
    			}

                $fGradedCoursesGrade = [];
                $grades2 = \backend\models\GradeScale::find()->where(['gs_ltr_grade'=>['C','D','F','NG','WD','IC','DO']])->all();

                if ($grades2 && count($grades2) > 0) {
                    // $gradeArray .= "<option value=''> - </option>";
                    foreach ($grades2 as $grade) {
                        $fGradedCoursesGrade[] = $grade['id'];
                    }
                }                

    			$course = \backend\models\Course::findOne(['id'=>$courseId]);
    			$group = \backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId])->group_name;
    			$dept = \backend\models\Department::findOne(['id'=>$gset->gs_dept_relId])->dept_name;
    			$clslvl = \backend\models\ClassLevel::findOne(['id'=>$gset->gs_clslvl_relId])->clslvl_num;

    			$spec = is_null($gset->gs_spec_relId)?'-':\backend\models\Specialization::findOne(['id'=>$gset->gs_spec_relId])->spec_name;

	    		$wonderep .= "<table style='margin-top:5px;' class='table table-hover table-sm table-bordered table-condensed' style='font-size:9pt;'>";

	    		$wonderep .= "<caption><p class='bg-success'> 
	    			Course: <u><b>{$course->course_title}<br></b></u>\t
	    			Department: <u><b>{$dept}<br></b></u>\t
	    			Class Year: <u><b>{$clslvl}<br></b></u>\t
	    			Specialization: <u><b>{$spec}<br></b></u>\t
	    			Group: <u><b>{$group}<br></b></u>\t
	    			</p></caption>";

            	$wonderep .= "<thead style='background-color:#ace;'>
                        <th style='border:1px solid black;'>#</th>
                        <th style='border:1px solid black;'>Reg. ID.</th>
                        <th style='border:1px solid black;'>Student Name</th>
                        <th style='border:1px solid black;'>Crs. Added</th>
                        <th style='border:1px solid black;'>Grade</th>
                    </thead><tbody>";
            	$count = 0;
                $srd2s = [];
    			foreach ($stds2 as $std) {

    				$s = Student::findOne(['id'=>$std['semreg_std_relId']]);
    				$u = User::findOne(['id'=>$s->std_user_relId]);

                    // check if the course F graded before,
                    if($this->CourseFGrdaed($courseId,$std['semreg_std_relId'])){
                        $gradeArray = $fGradedCoursesGrade;
                    }




                    $srd =SemesterRegistration2Detail::findOne([
                        'srd2_semreg_relId'=>$std['id'],
                        'srd2_course_relId'=>$courseId,
                        'srd2_group_relId'=>$gsetId,
                        'srd2_grade_relId'=>NULL,
                    ]);

                    if (empty($srd)) {
                        continue;
                    }
                    $gid = $srd->srd2_grade_relId;
                    //$grade = is_null($gid)?'-':GradeScale::findOne(['id'=>$gid])->gs_ltr_grade;
                    $caddda = is_null($srd->srd2_coadd_flag)?'NO':'YES';
                    $srd2s[] = $srd->id;
    				$wonderep .= "<tr>
    				      <td style='border:1px solid black;'>". ++$count."</td>
    				      <td style='border:1px solid black;'>". $s->std_regId."</td>
    				      <td class='std_name' id='". $s->id ."' style='border:1px solid black;'>". $u->first_name.' '. $u->middle_name ."</td>
                          <td style='border:1px solid black;'>".$caddda."</td>
                          <td style='border:1px solid black;'><select class='' id='". $s->id ."_grade'>". $gradeArray ."</select>
                          </td>
    				</tr>";
    			}

    			$count1 = $count==0?'NO Student ':($count == 1?$count.' Student ':$count.' Students ');
    			$wonderep .= "</tbody><tfoot style='background-color:#dd7;'>
    			<tr>
    				<td colspan=4><input type='hidden' value='{$gsetId}' id='_gsetId' />
    				<p class='bg-success'><i><b>". $count1 ."</b></i> found.</p>
    			</td>";

    			if ( $count>0) {
                    $srd2s = json_encode($srd2s);
                    if (isset($_POST['ad'])) {
                        $wonderep .= "
                            <td colspan= 2>
                                <button class='btn btn-sm btn-success' title='Save grades' onclick='dhfgfvbsfgshgfjsdfatydasd({$srd2s})'>Save</button>
                            </td>";
                    }else{
                        $wonderep .= "
                            <td colspan= 2>
                                <button class='btn btn-sm btn-success' title='Save grades' onclick='dhfgfvbsfgshgfjsdfatydasdv()'>Save</button>
                            </td>";
                    }
    				
    			}

    			$wonderep .= "</tr></tfoot></table>";
                return $wonderep;
    		}else{
    			echo "<p class='bg-warning'>No student enrolled this course in this semester.</p>";die();
    		}
		}else{
			echo "<p>No data found.</p>";die();
		}
    }

    private function CourseFGrdaed($courseId,$stdId){
        // check if the student enrolled the course previously with F grade.

        return \frontend\models\SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->innerJoin('grade_scale','semester_registration_2_detail.srd2_grade_relId = grade_scale.id')
            ->where(['semreg_std_relId'=>$stdId])
            ->andWhere(['srd2_course_relId'=>$courseId])
            ->andWhere(['IS NOT','srd2_grade_relId',NULL])
            ->andWhere(['gs_ltr_grade'=>'F'])
            ->one();
    }
}
