<?php

namespace frontend\controllers;
use yii;
use backend\models\ContinuousAssessment2;
use \common\components\CommonControllerFunctions as CCF;

class SubmittedGradesController extends \yii\web\Controller
{

    public $acadYearId;
    public $semId;

    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    public function actionIndex(){
        if (Yii::$app->user->can('DepartmentHead')) {
            return $this->render('index');
        }else if(Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')|| Yii::$app->user->can('clerk')|| \Yii::$app->user->can('AcademicDean')){
            $model = new \backend\models\StudentSemesterStatusHistory();
            return $this->render('index_registrar',['model'=>$model]);
        }else{
            echo "<p class='bg-danger'>You have no privilage for this action.</p>";die();
        }
    }

    // Load Groups assigned for the dept in the semesgetr.
    public function actionHoDeptGroups(){
        $gset = NULL;
        $this->__constructor();
        $acadYearId = isset($_POST['academicYear']) && !empty($_POST['academicYear']) ? intval($_POST['academicYear']) : $this->acadYearId;
        $semester = isset($_POST['semester']) && !empty($_POST['semester']) ? intval($_POST['semester']) : $this->acadYearId;
        if (Yii::$app->user->can('DepartmentHead')) {
            // Find all the groups assigned to His/Her dept in this sem.
            $instDept = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;
            $gsets = \backend\models\WGroupSetup::find()
                ->where(['gs_acadYear_relId'=>$acadYearId,'gs_sem_relId'=>$semester,'gs_dept_relId'=>$instDept])
                ->all();
        }else{
            $gsets = \backend\models\WGroupSetup::find()
                ->where(['gs_acadYear_relId'=>$acadYearId,'gs_sem_relId'=>$semester])
                ->all();
        }

        $option = "<select onchange='loadInsts($(this).val())' class='form-control'>";

        if ($gsets) {
            $option .= "<option value=''>NONE</option>";
            foreach ($gsets as $gset) {

                // $gset = \backend\models\WGroupSetup::findOne(['id'=>$gsetId]);
                $admission = \backend\models\Admission::findOne(['id'=>$gset->gs_adm_relId])->admission_name;
                $program = \backend\models\Program::findOne(['id'=>$gset->gs_prog_relId])->program_name;
                $dept = \backend\models\Department::findOne(['id'=>$gset->gs_dept_relId])->dept_name;
                $clslvl = \backend\models\ClassLevel::findOne(['id'=>$gset->gs_clslvl_relId])->clslvl_num;

                $clslvl = $clslvl==1?'1<sup>st</sup>':($clslvl == 2?'2<sup>nd</sup>':($clslvl == 3?'3<sup>rd</sup>':$clslvl.'<sup>th</sup>'));
                $gname = \backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId])->group_name;

                $title = $admission.'_'.$program.'_'.$dept.'_'.$clslvl.'_'.'Year.';
                $gname .= ' | '.$title;

                $option .= "<option title='{$gset->gsAcadYearRel->sch_year_desc}-{$gset->gsSemRel->semester_name}' value='{$gset->id}'>{$gname}</option>";

            }
            $option .= "</select>";
            return $option;
            
        }else{
            die("<p class='bg-danger'>You have no privilage for this action.</p>");
        }
    }

    // Load course Grade 
    public function actionLoadSubmittedGrades(){
        $this->__constructor();
        $courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])?intval($_POST['courseId']):die('Uknown Course');
        $coffId = isset($_POST['coffId']) && !empty($_POST['coffId'])?intval($_POST['coffId']):die('Uknown Instructor');
        $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId'])?intval($_POST['gsetId']):die('Uknown Group');

        $gset  = \backend\models\WGroupSetup::findOne($gsetId);
        $semreg = \frontend\models\SemesterRegistration2Detail::find()
            ->where(['srd2_group_relId'=>$gsetId,'srd2_course_relId'=>$courseId])
            ->innerJoin('semester_registration_2','semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
            ->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')
            ->innerJoin('user','user.id = student.std_user_relId')
            ->andWhere([
                'semreg_acadYear_relId'=>$gset->gs_acadYear_relId,
                'semreg_semester_relId'=>$gset->gs_sem_relId
            ])
            ->andWhere(['srd2_coff_relId'=>$coffId])
            ->with('srd2SemregRel')
            ->orderBy(['user.first_name' => SORT_ASC,'user.middle_name' => SORT_ASC])
            ->all();
        // echo count($semreg);
        $wt_report = "";
        if ($semreg) {

            // $gset = \backend\models\WGroupSetup::findOne(['id'=>$gsetId]);
            $admission = \backend\models\Admission::findOne(['id'=>$gset->gs_adm_relId])->admission_name;
            $program = \backend\models\Program::findOne(['id'=>$gset->gs_prog_relId])->program_name;
            $dept = \backend\models\Department::findOne(['id'=>$gset->gs_dept_relId])->dept_name;
            $clslvl = \backend\models\ClassLevel::findOne(['id'=>$gset->gs_clslvl_relId])->clslvl_num;

            $clslvl = $clslvl==1?'1<sup>st</sup>':($clslvl == 2?'2<sup>nd</sup>':($clslvl == 3?'3<sup>rd</sup>':$clslvl.'<sup>th</sup>'));
            $gname = \backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId])->group_name;
            $course = \backend\models\Course::findOne(['id'=>$courseId]);

            $wt_report .= "<table class='table table-sm table-hover table-striped table-condensed table-bordered' style='font-size:10pt;'>";
            $wt_report .= "<caption>
                Admission:<b><u>{$admission}</u></b>\t  &nbsp;
                Program:<b><u>{$program}</u></b><br>
                Department:<b><u>{$dept}</u></b>\t  
                Class year:<b><u>{$clslvl}</u></b><br>
                Group:<b><u>{$gname}</u></b>\t  
                Course Title:<b><u>{$course->course_title}</u></b><br>
                </caption>";
            $wt_report .= "<thead style='background-color:#ace;'>";
            $wt_report .= "<th>#</th>
                <th>Reg. ID.</th>
                <th>Student Name</th>
                <th style='text-align:right;'>Total(100%)</th>
                <th>Grade</th>
                </thead><tbody>";
                $count = 0;
            foreach ($semreg as $key) {
                $grade = is_null($key->srd2_grade_relId)?'-':\backend\models\GradeScale::findOne(['id'=>$key->srd2_grade_relId])->gs_ltr_grade;
                $totalMark = ContinuousAssessment2::getSum($key->srd2SemregRel->semregStdRel->id,$courseId,$key->srd2_semreg_relId, $key->srd2_coff_relId);
                $user = $key->srd2SemregRel->semregStdRel->stdUserRel;
                $wt_report .= "<tr>
                    <td>".++$count."</td>
                    <td>{$user['userStdRel']['std_regId']}</td>
                    <td>{$user->getFullName()}</td>
                    <td style='text-align:right;font-weight:bold'>". sprintf("%.02f",$totalMark) ."</td>
                    <td style='text-align:center; font-weight:bold'>{$grade}</td>
                    </tr>";
                // echo "{$key['srd2SemregRel']['semreg_std_relId']}";
            }
        return $wt_report;
        }else{
            die("<p class='bg-danger'>No student enrolled this course in this group.</p>");
        }
    } 

    // Get Instructors assigned for the group
    public function actionLoadGroupInstructors(){
        if (1!=1) {

        }else if (\Yii::$app->user->can('DepartmentHead') || \Yii::$app->user->can('Registrar')|| \Yii::$app->user->can('clerk')|| \Yii::$app->user->can('Admin')|| \Yii::$app->user->can('Commandant')|| \Yii::$app->user->can('PGCoordinator')|| \Yii::$app->user->can('AcademicDean')) {
            // Get selection spefication.
            $groupId = isset($_POST['groupId']) && !empty($_POST['groupId'])?intval($_POST['groupId']):die('Uknown Group');

            $gset = \backend\models\WGroupSetup::findOne(['id'=>$groupId]);

            if ($gset) {
                $t = "";
                // get insts assigned for this group.
                $this->__constructor();
                $assignedInsts = \backend\models\CourseOffering2Detail::find()
                    ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                    ->where([
                        'coff2_acadYear_relId'=>$gset->gs_acadYear_relId,
                        'coff2_sem_relId'=>$gset->gs_sem_relId,
                        'coff2_group_relId'=>$gset->id
                    ])
                    ->andWhere(['IS NOT','coff2det_inst_relId',NULL])
                    ->all();
                if ($assignedInsts) {
                    $t .= "<table class='table table-sm table-hover table-striped table-condensed table-bordered' style='font-size:9pt;'>";
                    $t .= "<thead style='background-color:#ace;'>";
                    $t .= "<th>#</th>
                        <th>Instructor Name</th>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Option</th>
                        </thead><tbody>";
                        $count = 0;
                    foreach ($assignedInsts as $key) {
                        
                        $fullname = $key->coff2detInstRel?$key->coff2detInstRel->getFullName() : '<span class="bg-warning">UKNOWN_INST.</span>';
                        $course = \backend\models\Course::findOne(['id'=>$key->coff2det_course_relId]);

                        $btn = '';
                        if (isset($_POST['markProgress'])) {
                            $btn = "<button class='btn btn-xs btn-success' onclick='showGrades({$key->id},{$gset->id},{$course->id},{$gset->gs_acadYear_relId},{$gset->gs_sem_relId})' title='Show Mark Progress'>Show</button>";
                        }else{
                            $btn = "<button class='btn btn-xs btn-success' onclick='showGrades({$key->coff2det_coff2_relId},{$gset->id},{$course->id},{$gset->gs_acadYear_relId},{$gset->gs_sem_relId})' title='Show grade'>Show</button>";
                        }
                        $t .= "<tr>
                                <td>".++$count."</td>
                                <td title='{$key->coff2det_inst_relId}'>{$fullname}</td>
                                <td>{$course->course_code}</td>
                                <td>{$course->course_title}</td>
                                <td>{$btn}</td>
                            </tr>";
                        // $t .= "<p class='bg-success'>{$user->first_name} {$user->middle_name}  =>  {$course->course_code} | {$course->course_title}</p>";
                    }
                    $t .= "</tbody></table>";
                }else{
                    $t .= "<p class='bg-danger'>No Course offered for this Group / Inst. Assignment is NOT completed.</p>";
                }

            }else{
                die("<p class='bg-danger'>Such group not foung.</p>");
            }
        }else{
            die("<p class='bg-danger'>Error Access.</p>");
        }

        return $t;
    }


    private function prepareDetail($srd2DetObj,$coffDetObj){
        $table = "";
        if ($srd2DetObj) {
            $table.= "<table class= 'table table-condensed table-striped table-hover table-xs' style='font-size:9pt; background-color:#ace; border:1px solid black;'>";
            $table.= "<caption>
                    <center>
                        Course : {$coffDetObj->coff2detCourseRel->course_title}[{$coffDetObj->coff2detCourseRel->course_code}]
                        <br>Group : <b>{$srd2DetObj[0]->srd2GroupRel->gsGroupRel->group_name}</b>
                        <br>Inst. Name : <b>{$coffDetObj->coff2detInstRel->instUserRel->first_name} {$coffDetObj->coff2detInstRel->instUserRel->middle_name[0]}</b>
                    </center>
                </caption>";
            $table.= "<thead style='background-color:#8Do;'>
                        <th>#</th>
                        <th>Reg. ID</th>
                        <th>Full Name</th>
                        <th>Added</th>
                        <th>Grade</th>
                        <th>Approval</th>
                        <th>Option</th>
                    </thead><tbody>
                ";
                $count = 0;
            $course = $coffDetObj->coff2detCourseRel;
            foreach ($srd2DetObj as $SRDkey) {
                $cadd = is_null($SRDkey->srd2_coadd_flag)?'NO':'YES';
                $ktt = is_null($SRDkey->srd2_grade_relId) ? '-' : $SRDkey->srd2GradeRel->gs_ltr_grade;
                $btn = "";
                $app = $SRDkey->srd2_gradeApproved ? 'Accepted':'NOT YET';
                if (Yii::$app->user->can('Registrar') && $SRDkey->srd2_gradeApproved) {
                    if ($SRDkey->srd2_gradeLocked == 1) {
                        $btn = "<button class='btn btn-xs btn-danger' onclick='changeStatusSingle({$SRDkey->id},{$course->id},{$course->id},0)' title='Un-Lock grade'>Un-Lock</button>";
                    }else{
                        $btn = "<button class='btn btn-xs btn-warning' onclick='changeStatusSingle({$SRDkey->id},{$course->id},1)' title='Lock grade'>Lock</button>";
                    }
                }
                $table.= "
                    <tr>
                        <td>".++$count."</td>
                        <td>{$SRDkey->srd2SemregRel->semregStdRel->std_regId}</td>
                        <td>{$SRDkey->srd2SemregRel->semregStdRel->stdUserRel->first_name} {$SRDkey->srd2SemregRel->semregStdRel->stdUserRel->middle_name}</td>
                        <td>{$cadd}</td>
                        <td>{$ktt}</td>
                        <td>{$app}</td>
                        <td>{$btn}</td>
                    </tr>";
            }
            $table.= "</tboby></table>";
            return $table;
        }
    }

    public function actionLoadGroupInstructorsDeptPrint(){
        if(Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin') || Yii::$app->user->can('clerk')) {
            $params = isset($_GET['params']) && !empty($_GET['params'])?(string)$_GET['params']:die('No Record found');
            $params = json_decode($params);
            $adm = isset($params->adm) && !empty($params->adm)?intval($params->adm):die('Uknown Admission.');
            $prog = isset($params->prog) && !empty($params->prog)?intval($params->prog):die('Uknown Program.');
            $dept = isset($params->dept) && !empty($params->dept)?intval($params->dept):die('Uknown Department.');
            $acadYear = isset($params->acadYear) && !empty($params->acadYear)?intval($params->acadYear):die('Uknown Academic Year.');
            $sem = isset($params->sem) && !empty($params->sem)?intval($params->sem):die('Uknown Semester.');

            $coffEntry = \backend\models\CourseOffering2::find()
                ->innerJoin('w_group_setup','w_group_setup.id = course_offering2.coff2_group_relId')
                ->where([
                    'gs_acadYear_relId'=>$acadYear,
                    'gs_sem_relId'=>$sem,
                    'gs_adm_relId'=>$adm,
                    'gs_prog_relId'=>$prog,
                ])
                ->andWhere([
                    'coff2_acadYear_relId'=>$acadYear,
                    'coff2_sem_relId'=>$sem,
                ])
                ->all();
            $coffEntryArray = [];
            if ($coffEntry) {
                foreach ($coffEntry as $key) {
                    $coffEntryArray[] = $key->id;
                }
            }

            $department1 = \backend\models\Department::findOne($dept)->dept_name;
            $semester1 = \backend\models\Semester::findOne($sem)->semester_name;
            $acadYear1 = \backend\models\SchoolYear::findOne($acadYear)->sch_year_desc;
            $admission1 = \backend\models\Admission::findOne($adm)->admission_name;
            $program1 = \backend\models\Program::findOne($prog)->program_name;

            // get the department couses offered in that semester.
            $assignedInsts = \backend\models\CourseOffering2Detail::find()
                ->innerJoin('course','course.id = course_offering2_detail.coff2det_course_relId')
                ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                ->where([
                    'course.course_dept_relId'=>$dept,
                ])
                ->andWhere([
                    'coff2det_coff2_relId'=>$coffEntryArray,
                    'coff2_acadYear_relId'=>$acadYear,
                    'coff2_sem_relId'=>$sem,
                ])
                ->andWhere(['IS NOT','coff2det_inst_relId',NULL])
                ->orderBy('coff2det_inst_relId')
                ->all();
                $wt_report = "";
                if ($assignedInsts) {
                    $wt_report .= "<table cellpadding='1' cellspacing='0' border='1' class='table table-sm table-hover table-striped table-condensed table-bordered' style='font-size:9pt;' id='reportTable'>";
                    $wt_report .= "<caption style='color:black; font-weight:normal;'>
                            <p style='text-align:center;'>
                                ".strtoupper(\Yii::$app->params['college'])."<br>
                                OFFICE OF REGISTRAR <br>
                                <b><u>List of Grade Submision Summery</u></b><br>
                                <b><u>Department of {$department1}</u></b><br>
                                <u><i>{$semester1}</i></u> Semester of Academic Year <u><i>{$acadYear1}</i></u><br>
                                Program : <i><u>{$admission1} {$program1}</u></i><br>
                                ".date('r')."
                            </p>
                        </caption>";
                    $wt_report .= "<thead style='background-color:#ace;' id='reportTable_th'>";
                    $wt_report .= "<th style='border:1px solid black;'>#</th>
                        <th style='border:1px solid black;'>Instructor Name</th>
                        <th style='border:1px solid black;'>Course Code</th>
                        <th style='border:1px solid black;'>Course Title</th>
                        <th style='border:1px solid black;'>Group</th>
                        <th style='border:1px solid black;'>Class Level</th>

                        <th style='border:1px solid black;'>Total</th>
                        <th style='border:1px solid black;'>Pending(G)</th>
                        <th style='border:1px solid black;'>Pending(NC)</th>
                        <th style='border:1px solid black;'>Accepted</th>
                        <th style='border:1px solid black;'>Rejected</th>

                        </thead><tbody>";
                        $count = 0;
                    foreach ($assignedInsts as $key) {
                        $user = \backend\models\User::find()
                            ->innerJoin('instructor','user.id = instructor.inst_user_relId')
                            ->where(['instructor.id'=>$key->coff2det_inst_relId])
                            ->one();

                        $course = \backend\models\Course::findOne(['id'=>$key->coff2det_course_relId]);

                        $total = \frontend\models\SemesterRegistration2Detail::find()->where(['srd2_coff_relId'=>$key->coff2det_coff2_relId,'srd2_course_relId'=>$course->id])->count();

                        if ($total == 0) {
                            continue;
                        }

                        $total1 = \frontend\models\SemesterRegistration2Detail::find()
                            ->innerJoin('semester_registration_2','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                            ->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')
                            ->innerJoin('user','user.id = student.std_user_relId')
                            ->where(['srd2_coff_relId'=>$key->coff2det_coff2_relId,'srd2_course_relId'=>$course->id])
                            ->orderBy('user.first_name')
                            ->all();

                        $detail = $this->prepareDetail($total1,$key);

                        $pendingg = \frontend\models\SemesterRegistration2Detail::find()
                            ->where([
                                'srd2_coff_relId'=>$key->coff2det_coff2_relId,
                                'srd2_course_relId'=>$course->id,
                                'srd2_gradeApproved'=>NULL
                            ])
                            ->andWhere(['IS NOT','srd2_grade_relId',NULL])
                            ->count();
                        $pendingnc = \frontend\models\SemesterRegistration2Detail::find()
                            ->where([
                                'srd2_coff_relId'=>$key->coff2det_coff2_relId,
                                'srd2_course_relId'=>$course->id,
                                'srd2_gradeApproved'=>NULL,
                                'srd2_grade_relId'=>NULL
                            ])
                            ->count();
                        $pendingg = $pendingg == 0?'-':$pendingg;
                        $pendingnc = $pendingnc == 0?'-':$pendingnc;

                        $approved = \frontend\models\SemesterRegistration2Detail::find()->where(['srd2_coff_relId'=>$key->coff2det_coff2_relId,'srd2_course_relId'=>$course->id, 'srd2_gradeApproved'=>1])->count();

                        $approved = $approved==0?'-':$approved;

                        $rejected = \frontend\models\SemesterRegistration2Detail::find()->where(['srd2_coff_relId'=>$key->coff2det_coff2_relId,'srd2_course_relId'=>$course->id, 'srd2_gradeApproved'=>0])->count();
                        $rejected = $rejected==0?'-':$rejected;


                        $btn = '';
                        $gset = \backend\models\WGroupSetup::findOne($key->coff2detCoff2Rel->coff2_group_relId);
                        $gname = \backend\models\WGroup::findOne($gset->gs_group_relId)->group_name;
                        $dept = \backend\models\Department::findOne($gset->gs_dept_relId)->dept_code;
                        $cl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId)->clslvl_num;

                        $cl = $cl==1?'1<sup>st</sup>':($cl == 2?'2<sup>nd</sup>':($cl == 3?'3<sup>rd</sup>':$cl.'<sup>th</sup>'));
                        $cl .= 'Year';
                        $wt_report .= "<tr>
                                <td style='border:1px solid black;'>".++$count."</td>
                                <td style='border:1px solid black;'>{$user->first_name} {$user->middle_name}</td>
                                <td style='border:1px solid black;'>{$course->course_code}</td>
                                <td style='border:1px solid black;'>{$course->course_title}</td>
                                <td style='border:1px solid black;'>{$gname} (<b>{$dept}</b>)</td>
                                <td style='text-align:center; border:1px solid black;'>{$cl}</td>
                                <td style='text-align:center; border:1px solid black;'>{$total}</td>
                                <td style='text-align:center; border:1px solid black;'>{$pendingg}</td>
                                <td style='text-align:center; border:1px solid black;'>{$pendingnc}</td>
                                <td style='text-align:center; border:1px solid black;'>{$approved}</td>
                                <td style='text-align:center; border:1px solid black;'>{$rejected}</td>
                            </tr>";
                    }
                    $wt_report .= "</tbody></table>";
                    return $wt_report;
                }else{
                    echo "<p class='bg-danger'>No Instructor Assigned to courses from this department.</p>";
                }
        }else{
            return "Invalid Access!";
        }
    }
    // Get Instructors assigned for the group - FOR SUBMITTED GRADE SUMMERY CASE.
    public function actionLoadGroupInstructorsDept(){
        if(Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin') || Yii::$app->user->can('clerk')) {
            // Get selection spefication.
            $adm = isset($_POST['adm']) && !empty($_POST['adm'])?intval($_POST['adm']):die('Uknown Admission');
            $prog = isset($_POST['prog']) && !empty($_POST['prog'])?intval($_POST['prog']):die('Uknown Program');
            $dept = isset($_POST['dept']) && !empty($_POST['dept'])?intval($_POST['dept']):die('Uknown Department');
            // $clslvl = isset($_POST['clslvl']) && !empty($_POST['clslvl'])?intval($_POST['clslvl']):die('Uknown Class Level');

            $acadYear = isset($_POST['acadYear']) && !empty($_POST['acadYear'])?intval($_POST['acadYear']):die('Uknown Academic Year');
            $sem = isset($_POST['sem']) && !empty($_POST['sem'])?intval($_POST['sem']):die('Uknown Seemster');
            
            // get course offering entry.
            $coffEntry = \backend\models\CourseOffering2::find()
                ->innerJoin('w_group_setup','w_group_setup.id = course_offering2.coff2_group_relId')
                ->where([
                    'gs_acadYear_relId'=>$acadYear,
                    'gs_sem_relId'=>$sem,
                    'gs_adm_relId'=>$adm,
                    'gs_prog_relId'=>$prog,
                ])
                ->andWhere([
                    'coff2_acadYear_relId'=>$acadYear,
                    'coff2_sem_relId'=>$sem,
                ])
                ->all();
            $coffEntryArray = [];
            if ($coffEntry) {
                foreach ($coffEntry as $key) {
                    $coffEntryArray[] = $key->id;
                }
            }

            $department1 = \backend\models\Department::findOne($dept)->dept_name;
            $semester1 = \backend\models\Semester::findOne($sem)->semester_name;
            $acadYear1 = \backend\models\SchoolYear::findOne($acadYear)->sch_year_desc;
            $admission1 = \backend\models\Admission::findOne($adm)->admission_name;
            $program1 = \backend\models\Program::findOne($prog)->program_name;

            // get the department couses offered in that semester.
            $assignedInsts = \backend\models\CourseOffering2Detail::find()
                ->innerJoin('course','course.id = course_offering2_detail.coff2det_course_relId')
                ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                ->where([
                    'course.course_dept_relId'=>$dept,
                ])
                ->andWhere([
                    'coff2det_coff2_relId'=>$coffEntryArray,
                    'coff2_acadYear_relId'=>$acadYear,
                    'coff2_sem_relId'=>$sem,
                ])
                ->andWhere(['IS NOT','coff2det_inst_relId',NULL])
                ->orderBy('coff2det_inst_relId')
                ->all();
                $wt_report = "";
                if ($assignedInsts) {
                    $wt_report .= "<table class='table table-sm table-hover table-striped table-condensed table-bordered' style='font-size:9pt;' id='reportTable'>";
                    $wt_report .= "<caption style='color:black; font-weight:normal;'>
                            <p style='text-align:center;'>
                                ".strtoupper(\Yii::$app->params['college'])."<br>
                                OFFICE OF THE REGISTRAR <br>
                                <b><u>List of Grade Submision Summery</u></b><br>
                                <b><u>Department of {$department1}</u></b><br>
                                <u><i>{$semester1}</i></u> Semester of Academic Year <u><i>{$acadYear1}</i></u><br>
                                Program : <i><u>{$admission1} {$program1}</u></i><br>
                                ".date('r')."
                            </p>
                        </caption>";
                    $wt_report .= "<thead style='background-color:#ace;' id='reportTable_th'>";
                    $wt_report .= "<th style='border:1px solid black;'>#</th>
                        <th style='border:1px solid black;'>Instructor Name</th>
                        <th style='border:1px solid black;'>Course Code</th>
                        <th style='border:1px solid black;'>Course Title</th>
                        <th style='border:1px solid black;'>Group</th>
                        <th style='border:1px solid black;'>C. Level</th>

                        <th style='border:1px solid black;'>Total</th>
                        <th style='border:1px solid black;'>Pending(G)</th>
                        <th style='border:1px solid black;'>Pending(NC)</th>
                        <th style='border:1px solid black;'>Accepted</th>
                        <th style='border:1px solid black;'>Rejected</th>

                        <th style='border:1px solid black;' class='non-print'>Option</th>
                        </thead><tbody>";
                        $count = 0;
                    foreach ($assignedInsts as $key) {
                        $user = \backend\models\User::find()
                            ->innerJoin('instructor','user.id = instructor.inst_user_relId')
                            ->where(['instructor.id'=>$key->coff2det_inst_relId])
                            ->one();

                        $course = \backend\models\Course::findOne(['id'=>$key->coff2det_course_relId]);

                        $total = \frontend\models\SemesterRegistration2Detail::find()->where(['srd2_coff_relId'=>$key->coff2det_coff2_relId,'srd2_course_relId'=>$course->id])->count();

                        if ($total == 0) {
                            continue;
                        }

                        $total1 = \frontend\models\SemesterRegistration2Detail::find()
                            ->innerJoin('semester_registration_2','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                            ->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')
                            ->innerJoin('user','user.id = student.std_user_relId')
                            ->where(['srd2_coff_relId'=>$key->coff2det_coff2_relId,'srd2_course_relId'=>$course->id])
                            ->orderBy('user.first_name')
                            ->all();

                        $detail = $this->prepareDetail($total1,$key);

                        $pendingg = \frontend\models\SemesterRegistration2Detail::find()
                            ->where([
                                'srd2_coff_relId'=>$key->coff2det_coff2_relId,
                                'srd2_course_relId'=>$course->id,
                                'srd2_gradeApproved'=>NULL
                            ])
                            ->andWhere(['IS NOT','srd2_grade_relId',NULL])
                            ->count();
                        $pendingnc = \frontend\models\SemesterRegistration2Detail::find()
                            ->where([
                                'srd2_coff_relId'=>$key->coff2det_coff2_relId,
                                'srd2_course_relId'=>$course->id,
                                'srd2_gradeApproved'=>NULL,
                                'srd2_grade_relId'=>NULL
                            ])
                            ->count();
                        $pendingg = $pendingg == 0?'-':$pendingg;
                        $pendingnc = $pendingnc == 0?'-':$pendingnc;

                        $approved = \frontend\models\SemesterRegistration2Detail::find()->where(['srd2_coff_relId'=>$key->coff2det_coff2_relId,'srd2_course_relId'=>$course->id, 'srd2_gradeApproved'=>1])->count();

                        $approved = $approved==0?'-':$approved;

                        $rejected = \frontend\models\SemesterRegistration2Detail::find()->where(['srd2_coff_relId'=>$key->coff2det_coff2_relId,'srd2_course_relId'=>$course->id, 'srd2_gradeApproved'=>0])->count();
                        $rejected = $rejected==0?'-':$rejected;


                        $btn = '';
                        $gset = \backend\models\WGroupSetup::findOne($key->coff2detCoff2Rel->coff2_group_relId);
                        $gname = \backend\models\WGroup::findOne($gset->gs_group_relId)->group_name;
                        $dept = \backend\models\Department::findOne($gset->gs_dept_relId)->dept_code;
                        $cl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId)->clslvl_num;

                        $cl = $cl==1?'1<sup>st</sup>':($cl == 2?'2<sup>nd</sup>':($cl == 3?'3<sup>rd</sup>':$cl.'<sup>th</sup>'));
                        $cl .= 'Year';
                        $btn33 = "<button class='btn btn-xs btn-primary' onclick='showList({$key->coff2det_coff2_relId},{$course->id},1)' title='Show Student List'>Show</button>";
                        if (isset($_POST['markProgress'])) {
                            $btn = "<button class='btn btn-xs btn-success' onclick='showGrades({$key->id},{$key->coff2detCoff2Rel->coff2_group_relId},{$course->id})' title='Show Mark Progress'>Show</button>";
                        }else{
                            $srd3 = \frontend\models\SemesterRegistration2Detail::find()->where(['srd2_coff_relId'=>$key->coff2det_coff2_relId,'srd2_course_relId'=>$course->id])->andWhere(['srd2_gradeApproved'=>NULL])->one();

                            if ($srd3) {
                                $btn = "<span style='font-size:7pt; color:red; font-style:italic;'>HoD approval NOT completed.</span>";
                                // $btn33 = "";
                            }else{
                                $srd2 = \frontend\models\SemesterRegistration2Detail::find()->where(['srd2_coff_relId'=>$key->coff2det_coff2_relId,'srd2_course_relId'=>$course->id,'srd2_gradeLocked'=>0])->one();
                                if ($srd2) {
                                    $btn = "<button class='btn btn-xs btn-warning' onclick='changeStatus({$key->coff2det_coff2_relId},{$course->id},1)' title='Lock grade'>Lock</button>";
                                }else{
                                    $btn = "<button class='btn btn-xs btn-danger' onclick='changeStatus({$key->coff2det_coff2_relId},{$course->id},0)' title='Un - Lock grade'>UnLock</button>";
                                }
                            }
                            if(!Yii::$app->user->can('Registrar')){$btn = '';}
                        }
                        $wt_report .= "<tr>
                                <td style='border:1px solid black;'>".++$count."</td>
                                <td style='border:1px solid black;'>{$user->first_name} {$user->middle_name}</td>
                                <td style='border:1px solid black;'>{$course->course_code}</td>
                                <td style='border:1px solid black;'>{$course->course_title}</td>
                                <td style='border:1px solid black;'>{$gname} (<b>{$dept}</b>)</td>
                                <td style='text-align:center; border:1px solid black;'>{$cl}</td>
                                <td style='text-align:center; border:1px solid black;'>{$total}</td>
                                <td style='text-align:center; border:1px solid black;'>{$pendingg}</td>
                                <td style='text-align:center; border:1px solid black;'>{$pendingnc}</td>
                                <td style='text-align:center; border:1px solid black;'>{$approved}</td>
                                <td style='text-align:center; border:1px solid black;'>{$rejected}</td>
                                <td style='text-align:center; border:1px solid black;' class='non-print'>{$btn} | {$btn33}</td>
                            </tr>";
                        $wt_report .= "<tr style='display:none'>
                            <td colspan = 2 ></td>
                            <td colspan = 8 id='{$key->coff2det_coff2_relId}_{$course->id}'>{$detail}</td>
                            <td></td>
                            </tr>";
                        // echo "<p class='bg-success'>{$user->first_name} {$user->middle_name}  =>  {$course->course_code} | {$course->course_title}</p>";
                    }
                    $wt_report .= "</tbody></table>";
                    return $wt_report;
                }else{
                    echo "<p class='bg-danger'>No Instructor Assigned to courses from this department.</p>";
                }

            // }else{
            //     die("<p class='bg-danger'>Such group not foung.</p>");
            // }
        }else{
            die("<p class='bg-danger'>Error Access.</p>");
        }
    }

}

