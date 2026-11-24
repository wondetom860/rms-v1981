<?php

namespace frontend\controllers;
use \common\components\CommonControllerFunctions AS CCF;
use yii;
use yii\filters\VerbFilter;

class ApproveGradeController extends \yii\web\Controller
{

    /**
     * @inheritdoc
     */
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

    public function actionSaveStatusChange(){
        
        $course = isset($_POST['course']) && !empty($_POST['course'])?$_POST['course']:die('Course Uknown');
        $group = isset($_POST['group']) && !empty($_POST['group'])?$_POST['group']:die('Group Uknown');
        $students = isset($_POST['students']) && !empty($_POST['students'])?$_POST['students']:die('Student List Uknown');

        $students = json_decode($students);

        if (Yii::$app->user->can('DepartmentHead') || Yii::$app->user->can('CEP_DEPT')) {
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

            $srd22 = \frontend\models\SemesterRegistration2Detail::find()
                ->innerJoin('semester_registration_2','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                ->where([
                    'srd2_course_relId'=>$course,
                    'srd2_gradeLocked'=>1,
                    'srd2_group_relId'=>$group,
                ])
                ->one();
            // if ($srd22 && count($srd22) > 0) {
            //     echo "Can NOT change Registrar approved Grade!";
            //     die();
            // }
            $result = Yii::$app->db->createCommand()
                ->update(
                    'semester_registration_2_detail',
                    [
                        'srd2_gradeApproved'=>1,
                        'srd2_gradeApproved_by'=>Yii::$app->user->identity->id,
                        'srd2_gradeApproved_at'=>date('Y-m-d h:i:s'),
                    ],
                    [
                        'srd2_semreg_relId'=>$semregs,
                        'srd2_course_relId'=>$course,
                        'srd2_group_relId'=>$group,
                    ]
                )->execute();
            if ($result) {
                CCF::getSemesterStatusBYWONDE($semreg);
                return 1;
            }else{
                print_r($result);die();
            }
        }else{
            echo "You can not perform this operation. It is only for HoDs.";die();
        }
    }
    public function actionApproveGradeSubmission(){
        // Initialize current acadYear and Curent semsetr.
        $this->__constructor();

        if (Yii::$app->user->can('DepartmentHead')) {
            
            // 1. Get his department
            $instructor = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);
            $dh = \backend\models\DepartmentHead::findOne([
                'dh_inst_relId'=>$instructor->id,
                'dh_status'=>1,
                'dh_dept_relId'=>$instructor->inst_dep_relId
                ,
            ]);

            if ($dh) {

                // get all courses that belong to this dept, that are offered in this semester, for students.
                // ->andWhere(['course_offering2_detail.coff2det_registrarApproved_by'=>NULL])
                // ->andWhere(['IS NOT','course_offering2_detail.coff2det_registrarApproved_by',NULL])

                // ->andWhere([
                //         'coff2_acadYear_relId'=>$this->acadYearId,
                //         'coff2_sem_relId'=>$this->semId,
                //     ])
                $offeredDeptCourses = \backend\models\CourseOffering2Detail::find()
                    ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                    ->innerJoin('course','course.id = course_offering2_detail.coff2det_course_relId')
                    ->where(['course.course_dept_relId'=>$instructor->inst_dep_relId])
                    ->with('coff2detCoff2Rel')
                    ->all();

                $deptCourseOfferedGroups = [];
                $coffdetKeys = [];
                if ($offeredDeptCourses && count($offeredDeptCourses) > 0) {
                    foreach ($offeredDeptCourses as $key) {
                        $deptCourseOfferedGroups[] = $key['coff2detCoff2Rel']['coff2_group_relId'];
                        $coffdetKeys[] = $key->id;
                    }

                    $deptCourseOfferedGroups = array_unique($deptCourseOfferedGroups);

                    $coffdetKeys = array_unique($coffdetKeys);
                    return  $this->displayForm($deptCourseOfferedGroups,$coffdetKeys);
                }else{
                    echo "<p class='bg-danger'>No record.</p>";die();
                }
            }else{
                echo "<p class='bg-danger'>Error with your account.please contact registrar staffs.</p>";die();
            }
        }else if(Yii::$app->user->can('CEP_DEPT')){
            $offeredDeptCourses = \backend\models\CourseOffering2Detail::find()
                    ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                    ->andWhere(['IS NOT','course_offering2_detail.coff2det_registrarApproved_by',NULL])
                    ->all();
            $deptCourseOfferedGroups = [];
            $coffdetKeys = [];
            if ($offeredDeptCourses && count($offeredDeptCourses) > 0) {
                foreach ($offeredDeptCourses as $key) {
                        $deptCourseOfferedGroups[] = $key['coff2detCoff2Rel']['coff2_group_relId'];
                        $coffdetKeys[] = $key->id;
                    }
                $deptCourseOfferedGroups = array_unique($deptCourseOfferedGroups);
                $coffdetKeys = array_unique($coffdetKeys);
                echo  $this->displayForm($deptCourseOfferedGroups,$coffdetKeys);
            }else{
                echo "No Grade Submitted.";
            }
        }else{
            throw new \yii\web\HttpException(404, 'The requested Item is not available.');
        }
    }

    private function displayForm($deptCourseOfferedGroups,$coffdetKeys){
        if (count($coffdetKeys) > 0) {
            // onChange='loadGroupCourses($(this).val())'
            $table = "<table class='table table-sm table-striped table-condensed table-hover' style='font-size:9pt;'>";
            $table .= "<thead style='background-color:#8D0'>";
            $table .= "<th style='border:1px solid black;'>#</th>";
            $table .= "<th style='border:1px solid black;'>Acad. Info</th>";
            $table .= "<th style='border:1px solid black;'>Instructor</th>";
            $table .= "<th style='border:1px solid black;'>Program</th>";
            $table .= "<th style='border:1px solid black;'>Dept.</th>";
            $table .= "<th style='border:1px solid black;'>Batch</th>";
            $table .= "<th style='border:1px solid black;'>Spec.</th>";
            $table .= "<th style='border:1px solid black;'>Group</th>";
            $table .= "<th style='border:1px solid black;'>Course</th>";
            $table .= "<th style='border:1px solid black;'>Option</th>";
            $table .= "</thead><tbody>";
            // $groups = "<select class='form-control' onChange='loadGroupCourses($(this).val())' title='Select Group' id='deptGroups'>";
            // $groups .= "<option values=''> - </option>";
            $count = 0;
            foreach ($coffdetKeys as $key) {
                $coffdet = \backend\models\CourseOffering2Detail::findOne($key);
                $gset = $coffdet->coff2detCoff2Rel->coff2GroupRel;
                if (!($gset)) {$coffdet->delete();continue;}
                $course = $coffdet->coff2detCourseRel;

                $srd2 = \frontend\models\SemesterRegistration2Detail::find()
                    ->where([
                        'srd2_group_relId'=>$gset->id,
                        'srd2_course_relId'=>$course->id,
                    ])
                    ->andWhere(['OR',['srd2_gradeApproved'=>0],['srd2_gradeApproved'=>NULL]])
                    ->andWhere(['IS NOT','srd2_grade_relId',NULL])
                    ->all();

                if ($srd2 && count($srd2) > 0) {

                }else{
                    continue;
                }
                $title = '';
                $inst = "TBA";
                $spec = '-';
                $btn = "<button class='btn btn-xs btn-primary' onclick='LoadCourseGrade({$course->id},{$gset->id})'>Show</button>";
                $sem = $gset->gsSemRel->semester_name;
                $acadYear = $gset->gsAcadYearRel->sch_year_desc;
                $adm =  \backend\models\Admission::findOne(['id'=>$gset['gs_adm_relId']])->admission_name;
                $prog =  \backend\models\Program::findOne(['id'=>$gset['gs_prog_relId']])->program_name;
                $dept = \backend\models\Department::findOne(['id'=>$gset['gs_dept_relId']])->dept_code;
                $clslvl = \backend\models\ClassLevel::findOne(['id'=>$gset['gs_clslvl_relId']])->clslvl_name;
                if (!is_null($gset['gs_spec_relId'])) {
                    $spec = $gset->gsSpecRel->spec_name;
                }

                $instObject = $coffdet->coff2detInstRel;
                $TAObject = $coffdet->coff2detTARel;

                if (!is_null($instObject)) {
                    $user = $instObject->instUserRel;
                    $inst = $user->first_name .' '. $user->middle_name;
                }
                // $course = $this->GetGroupCourses($gset->id);
                $table .= "<tr>
                            <td style='border:1px solid black;'>".++$count."</td>
                            <td style='border:1px solid black;'>{$acadYear} - {$sem}</td>
                            <td style='border:1px solid black;'>{$inst}</td>
                            <td style='border:1px solid black;'>{$adm} {$prog}</td>
                            <td style='border:1px solid black; text-align:center; font-weight:bold;'>{$dept}</td>
                            <td style='border:1px solid black; text-align:center;'>{$clslvl}</td>
                            <td style='border:1px solid black;'>{$spec}</td>
                            <td style='border:1px solid black; text-align:center;'>{$gset->gsGroupRel->group_name}</td>
                            <td style='border:1px solid black; '>{$course->course_code} => {$course->course_title}</td>
                            <td style='border:1px solid black;'>{$btn}(<b style='color:red;'>".count($srd2)."</b>)</td>
                        </tr>";
                
                // $groups .= "<option value='". $gset['id'] ."' title='". $title ."'>". \backend\models\WGroup::findOne(['id'=>$gset['gs_group_relId']])->group_name.' => '.$title ."</option>";
            }
            $table .= "</tbody></table>";
            // $groups .= "</select>";

            if ($count == 0) {
                return "<p class='bg-danger'><i>No Result.</i></p>";
            }
            return $table;
        }else{
            return 'No Grade Submitted.';
        }
    }
    protected function getCourseInstructorForGroup($gsetId, $courseId){
        return \backend\models\CourseOffering2Detail::find()
                ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                ->where(['coff2det_course_relId'=>$courseId])
                ->andWhere(['course_offering2.coff2_group_relId'=>$gsetId])
                ->One();
            // return $coff3det;     
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

        if (Yii::$app->user->can('DepartmentHead')) {
            // 1. Get his department
            $instructor = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);
        }else{
            echo "<p class-'bg-danger'>You are not allowed for this action.</p>";die();
        }
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

        //->andWhere(['NOT','srd2_grade_relId',NULL])
        $coff3det = \backend\models\CourseOffering2Detail::find()
            ->where(['coff2det_coff2_relId'=>$coff3->id])
            ->innerJoin('course','course.id = course_offering2_detail.coff2det_course_relId')
            ->andWhere(['course.course_dept_relId'=> $instructor->inst_dep_relId])
            ->all();

        if (!($coff3det && count($coff3det) > 0)) {
            echo "<p class='bg-danger'>No course offered for this group in this semester</p>";die();
        }else{

            // $gradedCourses = \backend\models\CourseOffering2Detail::find()->where(['coff2det_coff2_relId'=>$coff3->id]).innerJoin('semester_registration_2_detail');


            $groupCourses .= "<option value=''> - </option>";
            foreach ($coff3det as $co) {
                $course = \backend\models\Course::findOne(['id'=>$co['coff2det_course_relId']]);

                if (isset($co['coff2det_inst_relId'])) {
                    $instructor = \backend\models\Instructor::findOne(['id'=>$co['coff2det_inst_relId']]);

                    $user = \backend\models\User::findOne(['id'=>$instructor->inst_user_relId]);
                    $tit = 'INST => '.$user->first_name.' '. $user->middle_name;
                }else{
                    $tit = 'TBA.';
                }
                

                $groupCourses .= "<option value='". $course->id ."' title='".  $tit."'>". $course->course_title ."</option>";
            }
            $groupCourses .= "</select>";
            echo $groupCourses;
        }
    }

    public function actionGetCourseGradeForGroup(){

        $groupId = isset($_POST['group']) && !empty($_POST['group'])?intval($_POST['group']):die('ERROR: No Group Selected');
        $course = isset($_POST['course']) && !empty($_POST['course'])?intval($_POST['course']):die('ERROR: No course Selected');

        $gradeTable = '';

        $course = \backend\models\Course::findOne(['id'=>$course]);
        $gset = \backend\models\WGroupSetup::findOne(['id'=>$groupId]);
        $group = \backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId]);

        //var_dump($gset);die();


        $clslvl = \backend\models\ClassLevel::findOne(['id'=>$gset->gs_clslvl_relId]);
        $spec = is_null($gset->gs_spec_relId)?'-':\backend\models\Specialization::findOne(['id'=>$gset->gs_spec_relId])->spec_name;

        $srd2 = \backend\models\SemesterRegistration2Detail::find()
            ->where([
                'srd2_group_relId'=>$gset->id,
                'srd2_course_relId'=>$course->id,
            ])
            ->andWhere(['OR',['srd2_gradeApproved'=>0],['srd2_gradeApproved'=>NULL]])
            ->andWhere(['IS NOT','srd2_grade_relId',NULL])
            ->all();

        if (!($srd2 && count($srd2) > 0)) {
           echo "<p class='bg-danger'>No result</p>";die();
        }

        $courseInstr = $this->getCourseInstructorForGroup($gset->id,$course->id);

        if (!($courseInstr)) {
            die('No instructor assigned for this course');
        }

        $instUser = is_null($courseInstr->coff2det_inst_relId)?NULL:\backend\models\Instructor::findOne(['id'=>$courseInstr->coff2det_inst_relId])->inst_user_relId;

        $gradeTable .= "<table class='table table-striped table-condensed table-sm table-bordered' style='font-size:9pt;'>";
        $gradeTable .= "<caption><p class='bg-success'> Course: <u><b>{$course->course_title}<br></b></u>\tGroup: <u><b>{$group->group_name}</b></u>\tClass Level: <u><b>{$clslvl->clslvl_num}<br></b></u>\tSpecialization: <u><b>{$spec}</b></u>\t</p></caption>";
        $gradeTable .= "<thead style='background-color:#ace;'>
            <th>RegId</th>
            <th>Full Name</th>
            <th>Total Mark</th>
            <th>Grade</th>
            <th>Crs. Added</th>
            <th>Evltd. Inst</th>
            <th>Select <input title='Select All' type='checkbox' id='222'
            onChange=\"

                bb = $('#222').prop('checked');
                $.each($('.checkForApprove'),function(){
                $(this).prop('checked',bb)
                    .trigger('change');
                });

            \"/></th>
            </thead><tbody>";
            $count =0;
            // $('.checkForApprove').prop('checked',$(this).prop('checked'));$(this).trigger('change')
            foreach ($srd2 as $std) {

                // $student = \backend\models\Student::findOne(['id'=>\frontend\models\SemesterRegistration2::findOne(['id'=>$std['srd2_semreg_relId']])->semreg_std_relId]);
                $student = $std->srd2SemregRel->semregStdRel;
                $usr = $student->stdUserRel;

                // $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
                $semevdet = is_null($instUser)?TRUE:CCF::checkIfEvaluated($gset->id, $course->id,$instUser,$usr->id);
                $evaluated = 'NO'; //
                $cadddd = is_null($std['srd2_coadd_flag'])?'NO':'YES';

                if ($semevdet && !is_bool($semevdet)) {
                    $evaluated = $semevdet->semev2det_completed==1?'YES':'NO';
                }
                $grade = $std->srd2_grade_relId?$std->srd2GradeRel->gs_ltr_grade:'N/C';
                $gradeTable .= "<tr>
                    <td>". $student->std_regId ."</td>
                    <td>{$student->getFullName()}</td>
                    <td style='text-align:center;'>{$std->getTotalMark()}</td>
                    <td style='text-align:center;'>". $grade ."</td>
                    <td style='text-align:center;'>{$cadddd}</td>
                    <td style='text-align:center;'>{$evaluated}</td>
                    <td style='text-align:center;'> <input class='checkForApprove' type='checkbox' onChange='approvedGrade(".$student->id.")' />
                    </td>
                    </tr>";
                    $count++;
                }
            $gradeTable .= "<tr style='background-color:#dde'>
            <td colspan=3>{$count} record found.</td>
            <td colspan=4><button class='btn btn-sm btn-success' style='float:right;' onclick='saveOperation()' title='Approve for selected students.'>Approve</button>
            </td></tr>";
        $gradeTable .= "</tbody></table>";

        return $gradeTable;
    }


    public function actionDropGradeApproval(){
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

            $srd22 = \frontend\models\SemesterRegistration2Detail::find()
                ->innerJoin('semester_registration_2','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                ->where([
                    'srd2_course_relId'=>$course,
                    'srd2_gradeLocked'=>1,
                    'srd2_group_relId'=>$groupId,
                ])
                ->one();
            if ($srd22 && count($srd22) > 0) {
                echo "Can NOT change Registrar approved Grade!";
                die();
            }

            $result = Yii::$app->db->createCommand()
                ->update(
                    'semester_registration_2_detail',
                    [
                        'srd2_gradeApproved'=>NULL,
                        'srd2_gradeApproved_by'=>NULL,
                        'srd2_gradeApproved_at'=>NULL,
                    ],
                    [
                        'srd2_semreg_relId'=>$semregs,
                        'srd2_course_relId'=>$course,
                        'srd2_group_relId'=>$groupId,
                    ]
                )->execute();
            if ($result) {
                CCF::getSemesterStatusBYWONDE($semreg);
                return 1;
            }else{
                print_r($result);die();
            }
        }else{
            echo "You can not perform this operation. It is only for HoDs.";die();
        }

    }

    private function loadAllApprovedCourses($gsetId = NULL){

        $gradeTable = "";
        $srd2 = \frontend\models\SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'srd2_gradeApproved'=>1,
                'srd2_gradeApproved_by'=>Yii::$app->user->identity->id,
            ])
            ->andWhere(['semreg_acadYear_relId'=>$this->acadYearId,'semreg_semester_relId'=>$this->semId]);

        if (!is_null($gsetId)) {
            $srd2 = $srd2->andWhere(['srd2_group_relId'=>$gsetId]);
        }
        $srd2 = $srd2->all();


        if ($srd2  && count($srd2) > 0) {
            # code...
        }else{
            die("<p class='bg-danger'>No Approved Course found.<p>");
        }

        $gradeTable .= "<table class='table table-striped table-condensed table-sm table-bordered'>";
        $gradeTable .= "<caption><p class='bg-success'>Approved Course Grades</caption>";
        $gradeTable .= "<thead style='background-color:#dd6; text-align:center;'>
            <th>RegId</th>
            <th>Full Name</th>
            <th>Course Code</th>
            <th>Crs. Added</th>
            <th>Grade</th>
            <th>Action</th>
            </thead><tbody>";
        foreach ($srd2 as $std) {
            $student = \backend\models\Student::findOne($std->srd2SemregRel->semreg_std_relId);
            $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
            $cadddd = is_null($std['srd2_coadd_flag'])?'NO':'YES';
            if ($std->srd2_gradeLocked == 1) {
                continue;
            }
            $btn = $std->srd2_gradeLocked == 0?"<button class='btn btn-xs btn-warning' title='Drop Grade Approval for {$usr->first_name}, Grade change open.' onclick='dropGradeApproval({$std->srd2CourseRel->id},{$std->srd2GroupRel->id},". $student->id .")'>Change</button>":'<span style="color:red; font-size:7pt; font-style:italic;">Grade Locked</span>';
            $grade = is_null($std['srd2_grade_relId'])?'-':\backend\models\GradeScale::findOne(['id'=>$std['srd2_grade_relId']])->gs_ltr_grade;
            $gradeTable .= "<tr>
                <td>". $student->std_regId ."</td>
                <td>". $usr->first_name.' '. $usr->middle_name ."</td>
                <td style='text-align:center;'>{$std->srd2CourseRel->course_code}</td>
                <td style='font-size:7pt; text-align:center;'>{$cadddd}</td>
                <td style='text-align:center;'>". $grade ."</td>
                <td style='text-align:center;'>{$btn}</td>
                </tr>";
            }
        $gradeTable .= "</tbody></table>";

        return $gradeTable;
    }

    public function actionGetApprovedCourseGradeForGroup(){
        // Course
        // Group
        // Return with the check box for approval
        // Option for select all.

        $this->__constructor();
        $groupId = isset($_POST['group']) && !empty($_POST['group'])?$_POST['group']:NULL;
        $course = isset($_POST['course']) && !empty($_POST['course'])?$_POST['course']:NULL;

        if (is_null($groupId) || is_null($course)) {
            return $this->loadAllApprovedCourses();
        }
        $gradeTable = '';

        $course = \backend\models\Course::findOne(['id'=>$course]);
        $gset = \backend\models\WGroupSetup::findOne(['id'=>$groupId]);
        $group = \backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId]);

        //var_dump($gset);die();


        $clslvl = \backend\models\ClassLevel::findOne(['id'=>$gset->gs_clslvl_relId]);
        $spec = is_null($gset->gs_spec_relId)?'-':\backend\models\Specialization::findOne(['id'=>$gset->gs_spec_relId])->spec_name;

        $this->__constructor();
        // 'semreg_clslvl_relId'=>$clslvl->id,
        $semreg = \frontend\models\SemesterRegistration2::find()
            ->where([
                'semreg_acadYear_relId'=>$this->acadYearId,
                'semreg_semester_relId'=>$this->semId,
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
                'srd2_gradeApproved'=>1,
                'srd2_gradeApproved_by'=>Yii::$app->user->identity->id,
            ])
            ->all();

        if (!($srd2 && count($srd2) > 0)) {
           echo "<p class='bg-danger'>Approved studet grade list empty.</p>";die();
        }

        $gradeTable .= "<table class='table table-striped table-condensed table-sm table-bordered'>";
        $gradeTable .= "<caption><p class='bg-success'> Course: <u><b>{$course->course_title}<br></b></u>\tGroup: <u><b>{$group->group_name}</b></u>\tClass Level: <u><b>{$clslvl->clslvl_num}<br></b></u>\tSpecialization: <u><b>{$spec}</b></u>\t</p></caption>";
        $gradeTable .= "<thead style='background-color:#dd6; text-align:center;'>
            <th>RegId</th>
            <th>Full Name</th>
            <th>Grade</th>
            <th>Crs. Added</th>
            <th>Action</th>
            </thead><tbody>";
        foreach ($srd2 as $std) {
            $student = \backend\models\Student::findOne(['id'=>\frontend\models\SemesterRegistration2::findOne(['id'=>$std['srd2_semreg_relId']])->semreg_std_relId]);
            $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
            $cadddd = is_null($std['srd2_coadd_flag'])?'NO':'YES';
            $btn = $std->srd2_gradeLocked == 0?"<button class='btn btn-xs btn-warning' title='Drop Grade Approval for {$usr->first_name}, Grade change open.' onclick='dropGradeApproval(". $course->id .",". $gset->id .",". $student->id .")'>Change</button>":'<span style="color:red; font-size:7pt; font-style:italic;">Grade Locked</span>';
            $grade = is_null($std['srd2_grade_relId'])?'-':\backend\models\GradeScale::findOne(['id'=>$std['srd2_grade_relId']])->gs_ltr_grade;
            $gradeTable .= "<tr>
                <td>". $student->std_regId ."</td>
                <td>". $usr->first_name.' '. $usr->middle_name ."</td>
                <td style='text-align:center;'>". $grade ."</td>
                <td style='font-size:7pt; text-align:center;'>{$cadddd}</td>
                <td style='text-align:center;'>{$btn}</td>
                </tr>";
            }
        $gradeTable .= "</tbody></table>";

        return $gradeTable;
        }
}
