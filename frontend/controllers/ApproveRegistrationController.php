<?php

namespace frontend\controllers;
use \common\components\CommonControllerFunctions AS CCF;
use yii;
use yii\filters\VerbFilter;

class ApproveRegistrationController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
               'class' => \yii\filters\AccessControl::class,
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::class,
               ],
               'rules' => [
                   [
                       'actions' => [],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['@',],
                   ],
               ]
           ]
        ];
    }

	public $acadYearId = null;
    public $semId = null;

    // set values for current date sem indicators for the system time.
    protected function __constructor(){

        $this->acadYearId = \backend\models\SchoolYear::findOne(['sch_year_desc'=>Yii::$app->params['C']['Y']])->id;
        $this->semId = \backend\models\Semester::findOne(['semester_name'=>Yii::$app->params['C']['S']])->id;
    }


    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSaveStatusChange(){
        
        $course = isset($_POST['course']) && !empty($_POST['course'])?$_POST['course']:die('Course Uknown');
        $group = isset($_POST['group']) && !empty($_POST['group'])?$_POST['group']:die('Group Uknown');
        $students = isset($_POST['students']) && !empty($_POST['students'])?$_POST['students']:die('Student List Uknown');

        $students = json_decode($students);

        if (Yii::$app->user->can('DepartmentHead')) {
            $this->__constructor();
            // Get entry.
            $semreg = \frontend\models\SemesterRegistration2::find()
                ->where([
                    'semreg_acadYear_relId'=>$this->acadYearId,
                    'semreg_semester_relId'=>$this->semId,
                ])->andWhere([
                    'semreg_std_relId'=>$students,
                ])
                ->all();

                $semregs = [];

                if ($semreg && count($semreg) > 0) {
                    foreach ($semreg as $sr) {
                        array_push($semregs, $sr['id']);
                    }
                }

            $result = Yii::$app->db->createCommand()
                ->update(
                    'semester_registration_2_detail',
                    [
                        'srd2_enrollApproved'=>1,
                        'srd2_enrollApproved_by'=>Yii::$app->user->identity->id,
                        'srd2_enrolApprovrd_at'=>date('Y-m-d h:i:s'),
                    ],
                    [
                        'srd2_semreg_relId'=>$semregs,
                        'srd2_course_relId'=>$course,
                        'srd2_group_relId'=>$group,
                    ]
                )->execute();
            if ($result) {
                echo 1;die();
            }else{
                print_r($result);die();
            }
        }else{
            echo "You can not perform this operation. It is only for HoDs.";die();
        }
    }
    public function actionApproveGradeSubmission(){
    	if (Yii::$app->user->can('DepartmentHead')) {
    		
    		// 1. Get his department
    		$instructor = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);

    		$groups = "<select class='form-control' onChange='loadGroupCourses($(this).val())' title='Select Group' id='deptGroups'>";
    		$dh = \backend\models\DepartmentHead::findOne([
    			'dh_inst_relId'=>$instructor->id,
    			'dh_status'=>1,
    			'dh_dept_relId'=>$instructor->inst_dep_relId
    			,
    		]);

    		if ($dh) {
    			
    			// Initialize current acadYear and Curent semsetr.
    			$this->__constructor();

    			// 2.Get groups assigned to his department per batch
    			$gsets = \backend\models\WGroupSetup::find()
    				->where([
    					'gs_acadYear_relId'=>$this->acadYearId,
    					'gs_sem_relId'=>$this->semId,
    					'gs_dept_relId'=>$dh->dh_dept_relId,
    					])
    				->all();

    				if ($gsets && count($gsets) > 0) {
    					$groups .= "<option values=''> - </option>";
    					foreach ($gsets as $gset) {
                             $title = '';
                            $title .=  \backend\models\Admission::findOne(['id'=>$gset['gs_adm_relId']])->admission_name;
                            $title .= '_'. \backend\models\Program::findOne(['id'=>$gset['gs_prog_relId']])->program_name;
                            $title .= '_'. \backend\models\ClassLevel::findOne(['id'=>$gset['gs_clslvl_relId']])->clslvl_name;
                            if (!is_null($gset['gs_spec_relId'])) {
                                $title .= '_'.\backend\models\Specialization::findOne(['id'=>$gset['gs_spec_relId']])->spec_name;
                            }
                            
    						$groups .= "<option value='". $gset['id'] ."' title='". $title ."'>". \backend\models\WGroup::findOne(['id'=>$gset['gs_group_relId']])->group_name.' => '.$title ."</option>";
    					}
                        $groups .= "</select>";

                        echo $groups;
    				}else{
    					echo "<p class='bg-danger'>No Group Setup made for your dept yet, please wait a moment or contact registrar staffs.</p>";die();
    				}
    			// 3. 

    		}else{
    			echo "<p class='bg-danger'>Error with your account.please contact registrar staffs.</p>";die();
    		}
    	}else{
    		throw new \yii\web\HttpException(404, 'The requested Item is not available.');
    	}
    }

    protected function getCourseInstructorForGroup($gsetId, $courseId){
        $this->__constructor();
        $coff3 = \backend\models\CourseOffering2::find()
                    ->filterWhere([
                        'course_offering2.coff2_acadYear_relId'=>$this->acadYearId,
                    ])
                    ->andFilterWhere([
                        'course_offering2.coff2_sem_relId'=>$this->semId
                    ])
                    ->andFilterWhere([
                        'course_offering2.coff2_group_relId'=> $gsetId
                    ])
                    ->one();
        if ($coff3 && count($coff3) > 0) {
            $coff3det = \backend\models\CourseOffering2Detail::find()->where(['coff2det_coff2_relId'=>$coff3->id,'coff2det_course_relId'=>$courseId])->One();
            return $coff3det;
        }
        return NULL;        
    }

    public function actionGetGroupCourses(){
        // Group
        $groupCourses = "<select class='form-control' id='groupCourses' onChange='LoadCourseGrade($(this).val())'>";
        $groupId = isset($_POST['group']) && !empty($_POST['group'])?$_POST['group']:die('ERROR: No Group Selected');
        $gsetId = \backend\models\WGroupSetup::findOne(['id'=>$groupId]);

        if (!($gsetId)) {
            die('Uknown Group.');
        }
        $this->__constructor();
        $coff3 = \backend\models\CourseOffering2::find()
                    ->filterWhere([
                        'course_offering2.coff2_acadYear_relId'=>$this->acadYearId,
                    ])
                    ->andFilterWhere([
                        'course_offering2.coff2_sem_relId'=>$this->semId
                    ])
                    ->andFilterWhere([
                        'course_offering2.coff2_group_relId'=> $groupId
                    ])
                    ->one();
        if (!($coff3 && count($coff3) > 0)) {
            die('No course offering Entry found this semester');
        }
        $coff3det = \backend\models\CourseOffering2Detail::find()->where(['coff2det_coff2_relId'=>$coff3->id])->all();
        if (!($coff3det && count($coff3det) > 0)) {
            echo "<p class='bg-danger'>No course offered for this group in this semester</p>";die();
        }else{
            $groupCourses .= "<option value=''> - </option>";
            foreach ($coff3det as $co) {
                $course = \backend\models\Course::findOne(['id'=>$co['coff2det_course_relId']]);

                $instructor = \backend\models\Instructor::findOne(['id'=>$co['coff2det_inst_relId']]);

                $user = \backend\models\User::findOne(['id'=>$instructor->inst_user_relId]);
                $tit = 'INST => '.$user->first_name.' '. $user->middle_name;

                $groupCourses .= "<option value='". $course->id ."' title='".  $tit."'>". $course->course_title ."</option>";
            }
            $groupCourses .= "</select>";
            echo $groupCourses;
        }
    }

    public function actionGetCourseGradeForGroup(){

        $groupId = isset($_POST['group']) && !empty($_POST['group'])?$_POST['group']:die('ERROR: No Group Selected');
        $course = isset($_POST['course']) && !empty($_POST['course'])?$_POST['course']:die('ERROR: No course Selected');

        $gradeTable = '';

        $course = \backend\models\Course::findOne(['id'=>$course]);
        $gset = \backend\models\WGroupSetup::findOne(['id'=>$groupId]);
        $group = \backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId]);

        //var_dump($gset);die();


        $clslvl = \backend\models\ClassLevel::findOne(['id'=>$gset->gs_clslvl_relId]);
        $spec = is_null($gset->gs_spec_relId)?'-':\backend\models\Specialization::findOne(['id'=>$gset->gs_spec_relId])->spec_name;

        $this->__constructor();
        $semreg = \frontend\models\SemesterRegistration2::find()
            ->where([
                'semreg_acadYear_relId'=>$this->acadYearId,
                'semreg_semester_relId'=>$this->semId,
                'semreg_clslvl_relId'=>$clslvl->id,
            ])->all();
        $semregEntries = [];
        if (!($semreg && count($semreg) > 0)) {
           echo "<p class='bg-danger'>No semster registration entry found. ERROR:WT_20018</p>";die();
        }

        foreach ($semreg as $value) {
            array_push($semregEntries, $value['id']);
        }

        $srd2 = \frontend\models\SemesterRegistration2Detail::find()
            ->where([
                'srd2_semreg_relId'=>$semregEntries,
                'srd2_group_relId'=>$gset->id,
                'srd2_course_relId'=>$course->id,
                'srd2_enrollApproved'=>0,
            ])
            ->all();
        $courseInstr = $this->getCourseInstructorForGroup($gset->id,$course->id);

        if (!($courseInstr && count($courseInstr)> 0)) {
            die('No instructor assigned for this course');
        }

        $instUser = \backend\models\Instructor::findOne(['id'=>$courseInstr->coff2det_inst_relId])->inst_user_relId;

        if (!($srd2 && count($srd2) > 0)) {
           echo "<p class='bg-danger'>No un-approved semster registration detail found. ERROR:WT_20017</p>";die();
        }

        $gradeTable .= "<table class='table table-striped table-condensed table-sm table-bordered' style='font-size:10pt;'>";
        $gradeTable .= "<caption><p class='bg-success'> Course: <u><b>{$course->course_title}<br></b></u>\tGroup: <u><b>{$group->group_name}</b></u>\tClass Level: <u><b>{$clslvl->clslvl_num}<br></b></u>\tSpecialization: <u><b>{$spec}</b></u>\t</p></caption>";
        $gradeTable .= "<thead style='background-color:#ace;'>
            <th>RegId</th>
            <th>Full Name</th>
            <th>Grade</th>
            <th>Crs. Added</th>
            <th>Select <input title='Select All' type='checkbox' id='222'
            onChange=\"

                bb = $('#222').prop('checked');
                $.each($('.checkForApprove'),function(){
                $(this).prop('checked',bb)
                    .trigger('change');
                });

            \"/></th>
            </thead><tbody>";
            $count = 0;
            // $('.checkForApprove').prop('checked',$(this).prop('checked'));$(this).trigger('change')
        foreach ($srd2 as $std) {

            $student = \backend\models\Student::findOne(['id'=>\frontend\models\SemesterRegistration2::findOne(['id'=>$std['srd2_semreg_relId']])->semreg_std_relId]);
            $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
            // $semevdet = CCF::checkIfEvaluated($gset->id, $course->id,$instUser,$usr->id);
            // $evaluated = 'NO'; //
            $cadddd = is_null($std['srd2_coadd_flag'])?'NO':'YES';

            // if ($semevdet && count($semevdet) > 0) {
            //     $evaluated = $semevdet->semev2det_completed==1?'YES':'NO';
            // }
            $grade = is_null($std['srd2_grade_relId'])?'-':\backend\models\GradeScale::findOne(['id'=>$std['srd2_grade_relId']])->gs_ltr_grade;
            $gradeTable .= "<tr>
                <td>". $student->std_regId ."</td>
                <td>". $usr->first_name.' '. $usr->middle_name ."</td>
                <td style='text-align:center;'>". $grade ."</td>
                <td style='text-align:center;'>{$cadddd}</td>
                <td style='text-align:center;'> <input class='checkForApprove' type='checkbox' onChange='approvedGrade(".$student->id.")' />
                </td>
                </tr>";
                $count++;
            }
            $gradeTable .= "<tr style='background-color:#dde'>
            <td colspan=3><i>{$count} un-approved enrollments. </i></td>
            <td colspan=3><button class='btn btn-sm btn-success' style='float:right;' onclick='saveOperation()' title='Approve for selected students.'>Approve</button>
            </td></tr>";
    $gradeTable .= "</tbody></table>";

    echo $gradeTable;
    }

    public function actionDropApproval(){
    	$groupId = isset($_POST['group']) && !empty($_POST['group'])?$_POST['group']:die('ERROR: No Group Selected');
        $course = isset($_POST['course']) && !empty($_POST['course'])?$_POST['course']:die('ERROR: No course Selected');
        $student = isset($_POST['student']) && !empty($_POST['student'])?$_POST['student']:die('ERROR: No Student Selected');

        if (Yii::$app->user->can('DepartmentHead')) {
            $this->__constructor();
            // Get entry.
            $semreg = \frontend\models\SemesterRegistration2::find()
                ->where([
                    'semreg_acadYear_relId'=>$this->acadYearId,
                    'semreg_semester_relId'=>$this->semId,
                ])->andWhere([
                    'semreg_std_relId'=>$student,
                ])
                ->all();

                $semregs = [];

                if ($semreg && count($semreg) > 0) {
                    foreach ($semreg as $sr) {
                        array_push($semregs, $sr['id']);
                    }
                }

            $result = Yii::$app->db->createCommand()
                ->update(
                    'semester_registration_2_detail',
                    [
                        'srd2_enrollApproved'=>0,
                        'srd2_enrollApproved_by'=>NULL,
                        'srd2_enrolApprovrd_at'=>NULL,
                    ],
                    [
                        'srd2_semreg_relId'=>$semregs,
                        'srd2_course_relId'=>$course,
                        'srd2_group_relId'=>$groupId,
                    ]
                )->execute();
            if ($result) {
                echo 1;die();
            }else{
                print_r($result);die();
            }
        }else{
            echo "You can not perform this operation. It is only for HoDs.";die();
        }

    }
    public function actionGetApprovedCourseGradeForGroup(){
        // Course
        // Group
        // Return with the check box for approval
        // Option for select all.

        $groupId = isset($_POST['group']) && !empty($_POST['group'])?$_POST['group']:die('ERROR: No Group Selected');
        $course = isset($_POST['course']) && !empty($_POST['course'])?$_POST['course']:die('ERROR: No course Selected');

        $gradeTable = '';

        $course = \backend\models\Course::findOne(['id'=>$course]);
        $gset = \backend\models\WGroupSetup::findOne(['id'=>$groupId]);
        $group = \backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId]);

        //var_dump($gset);die();


        $clslvl = \backend\models\ClassLevel::findOne(['id'=>$gset->gs_clslvl_relId]);
        $spec = is_null($gset->gs_spec_relId)?'-':\backend\models\Specialization::findOne(['id'=>$gset->gs_spec_relId])->spec_name;

        $this->__constructor();
        $semreg = \frontend\models\SemesterRegistration2::find()
            ->where([
                'semreg_acadYear_relId'=>$this->acadYearId,
                'semreg_semester_relId'=>$this->semId,
                'semreg_clslvl_relId'=>$clslvl->id,
            ])->all();
        $semregEntries = [];
        if (!($semreg && count($semreg) > 0)) {
           echo "<p class='bg-danger'>No Student enrolled this course in this semester</p>";die();
        }

        foreach ($semreg as $value) {
            array_push($semregEntries, $value['id']);
        }

        $srd2 = \frontend\models\SemesterRegistration2Detail::find()
            ->where([
                'srd2_semreg_relId'=>$semregEntries,
                'srd2_group_relId'=>$gset->id,
                'srd2_course_relId'=>$course->id,
                'srd2_enrollApproved'=>1,
                'srd2_enrollApproved_by'=>Yii::$app->user->identity->id,
            ])
            ->all();

        if (!($srd2 && count($srd2) > 0)) {
           echo "<p class='bg-danger'>Approved studet course-enrollment list empty.</p>";die();
        }

        $gradeTable .= "<table class='table table-striped table-condensed table-sm table-bordered'>";
        $gradeTable .= "<caption><p class='bg-success'> Course: <u><b>{$course->course_title}<br></b></u>\tGroup: <u><b>{$group->group_name}</b></u>\tClass Level: <u><b>{$clslvl->clslvl_num}<br></b></u>\tSpecialization: <u><b>{$spec}</b></u>\t</p></caption>";
        $gradeTable .= "<thead style='background-color:#dd8;'>
            <th>RegId</th>
            <th>Full Name</th>
            <th>Grade</th>
            <th>Action</th>
            </thead><tbody>";
            $count = 0;
        foreach ($srd2 as $std) {
            $student = \backend\models\Student::findOne(['id'=>\frontend\models\SemesterRegistration2::findOne(['id'=>$std['srd2_semreg_relId']])->semreg_std_relId]);
            $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
            $grade = is_null($std['srd2_grade_relId'])?'-':\backend\models\GradeScale::findOne(['id'=>$std['srd2_grade_relId']])->gs_ltr_grade;
            $gradeTable .= "<tr>
                <td>". $student->std_regId ."</td>
                <td>". $usr->first_name.' '. $usr->middle_name ."</td>
                <td>". $grade ."</td>
                <td style='text-align:center;'> 
                	<button class='btn btn-xs btn-warning' title='Drop Approval for {$usr->first_name}' onclick='dropApproval(". $course->id .",". $gset->id .",". $student->id .")'>Drop</button>
                </td>
                </tr>";
                $count++;
            }
            $gradeTable .= "<tr><td colspan=5 style='background-color:#ace'>{$count} enrollments approved.</td></tr>";
    $gradeTable .= "</tbody></table>";

    echo $gradeTable;
    }
}
