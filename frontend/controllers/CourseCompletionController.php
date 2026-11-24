<?php

namespace frontend\controllers;

use backend\models\SemesterRegistration2;
use common\components\CommonControllerFunctions as CCF;
use frontend\models\SemesterRegistration2Detail;
use yii;
use yii\filters\VerbFilter;

class CourseCompletionController extends \yii\web\Controller {

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
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }
    public $acadYearId;
    public $semId;

    protected function __constructor() {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    public function actionIndex() {
        if (Yii::$app->user->can('Student')) {
            return $this->render('index_student');
        }
        $model = new \backend\models\StudentSemesterStatusHistory();
        return $this->render('index', ['model' => $model]);
    }

    // Show list of students under saearch.
    public function actionReport() {
        // initialize current acdaYera and Semester

        if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('clerk') || Yii::$app->user->can('Admin')|| Yii::$app->user->can('DepartmentHead') || Yii::$app->user->can('Instructor')) {
            
            $this->__constructor();

            $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('acadYearId Error..');
            $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('semId Error..');
            $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Uknown Admission');
            $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Uknown Program');
            $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Uknown Class Year.');
            $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : die('Uknown Group.');


            $gset = '';

            $gset = \backend\models\WGroupSetup::findOne([
                        'id' => $gsetId,
                        'gs_acadYear_relId' => $acadYearId,
                        'gs_sem_relId' => $semId,
                        'gs_adm_relId' => $admId,
                        'gs_prog_relId' => $progId,
                        'gs_clslvl_relId' => $clslvlId,
            ]);

            if (!($gset)) {
                die('Invalid Group Setup for your selection.');
            }

            $students2 = \backend\models\WGroupAssignment::find()
                ->innerJoin('student','student.id=w_group_assignment.gas_std_relId')
                ->innerJoin('user','user.id=student.std_user_relId')
                ->where(['gas_gset_relId' => $gset->id])
                ->orderBy(['user.first_name' => SORT_ASC,'user.middle_name' => SORT_ASC])
                ->all();

            // var_dump($students);die();
            $statusTable = '';

            if ($students2) {
                $statusTable .= "<table id='reportTable' class='table table-bordered' style='font-size:9pt;'>";

                $admission1 = \backend\models\Admission::findOne(['id' => $admId]);
                $program1 = \backend\models\Program::findOne(['id' => $progId]);
                $clslvl1 = \backend\models\ClassLevel::findOne(['id' => $clslvlId]);
                $acadYear1 = \backend\models\SchoolYear::findOne(['id' => $acadYearId]);
                $semester1 = \backend\models\Semester::findOne(['id' => $semId]);

                $statusTable .= "<caption style='text-align:center;'>             
                </caption>";
                $statusTable .= "<thead style='background-color:#ace;'>
                    <th>RegId</th>
                    <th>Student Name</th>
                    <th>Option</th>
                    </thead><tbody>";
                $count = 0;
                foreach ($students2 as $student) {
                    $student = \backend\models\Student::findOne(['id' => $student['gas_std_relId']]);
                    $usr = \backend\models\User::findOne(['id' => $student->std_user_relId]);

                    $dept = \backend\models\Department::findOne(['id' => $gset->gs_dept_relId]);
                    $spec = isset($gset->gs_spec_relId) ? \backend\models\Specialization::findOne(['id' => $gset->gs_spec_relId])->spec_name : '-';


                    $statusTable .= "<tr>
                    <td>{$student->std_regId}</td>
                    <td>{$student->getFullName()}</td>
                    <td style='text-align:center;' class='non-print'> 
                        <input class='btn btn-success btn-xs' value='Show' type='button' onClick=\"
                            showGrade({$gset->id},{$student->id});
                        \" />
                    </td>
                    </tr>";
                    $count++;
                }

                $statusTable .= "</tbody></table>";

                return $statusTable;
            } else {
                die('No student enrolled course in this semester.');
            }
        } else if (Yii::$app->user->can('Student')) {
	       return "<p class='bg-warning'>Grade Submission NOT completed Yet!</p>";
            $student = \backend\models\Student::find()->where(['std_user_relId' => Yii::$app->user->identity->id])->one();
            $stdUser = \backend\models\User::findOne(Yii::$app->user->identity->id);
            //->andWhere(['IS NOT','semester_registration_2_detail.srd2_grade_relId',NULL])
            $semregs = SemesterRegistration2Detail::find()
                    ->innerJoin('semester_registration_2', 'semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
                    ->where(['semester_registration_2.semreg_std_relId' => $student->id])
                    ->all();

            $gradeTable = "";
            $coursesEnrolled = $this->separateMajor($semregs, $student); //  [0=>majorCourses,1=>commonCourses]

            if ($coursesEnrolled) {
                # code...
            } else {
                echo "<p class='bg-danger'>No course enrolled yet or there is error with the course setting for your class. Please contact registrar ofice if this id error.</p>";
                die();
            }

            // $gset = CCF::currentStudentGroup($student);
            // if ($gset && count($gset)> 0) {
            //     # code...
            // }else{

            // }
            $admission1 = \backend\models\Admission::findOne($student->std_admission_relId);
            $program1 = \backend\models\Program::findOne($student->std_prog_relId);
            $clslvl1 = \backend\models\ClassLevel::findOne($student->std_clslvl_relId);
            $dept  = \backend\models\Department::findOne($student->std_dept_relId);
            $spec = is_null($student->std_spec_relId)?'-':\backend\models\Specialization::findOne($student->std_spec_relId)->spec_name;
            $gradeTable .= "<div class='container-fluid' style='text-align:center; margin-top:10px;' >";
            $gradeTable .= '
                            <p class="bg-default"><h5><b>'.strtoupper(Yii::$app->params['client']).', '.strtoupper(Yii::$app->params['college']).'<br>OFFICE OF THE REGISTRAR</b></h5></p>
                                <b><u style="text-align:center;">Course Completion Check</u></b><br>
                        </div>';

            $gradeTable .= "<table class='table table-hover table-sm table-condensed table-bordered'>";
            // style='border:1px solid black; padding:1px; margin:5px;'
            // <b>Group:</b><u><i>{$grp->group_name}</u></i><br>
            //                         <b>Semester:</b><u><i>{$semesterN->semester_name}</u></i><br>
            //                         <b>Academic Year:</b> <u><i>{$acadYear1->sch_year_desc}</u></i><br>
            $gradeTable .= "
                    <caption style='text-align:center;'>                    
                        <p class='bg-default'> 
                            <b>Full Name:</b><u><i>{$stdUser->first_name} {$stdUser->middle_name}</u></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <b>Reg.No.:</b> <u><i>{$student->std_regId}</u></i><br>
                            <b>Program:</b><u><i>{$program1->program_name}</u></i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <b>Admission:</b><u><i>{$admission1->admission_name}</u></i>
                        </p>
                        <p>
                            <b>Department :</b><u><i>{$dept->dept_name}</u></i>&nbsp;&nbsp;&nbsp;
                            <b>Specialization :</b><u><i>{$spec}</u></i>
                        </p>
                    </caption>";
            $gradeTable .= "<thead style='background-color:#ace;'>
                        <th>#</th>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Acad. Year</th>
                        <th>Sem.</th>
                        <th>CrdtHr</th>
                        <th>Grade</th>
                        <th>Grd. Pt.</th>
                    </thead><tbody>";
            // <th>Crs. Added</th>
            $count = 0;
            $crdtSum = 0;
            $gptSum = 0;
            $gradeTable .= "<tr style='background-color:#CD0;'><td colspan=8 style='text-align:center; font-weight:bolder;'>Major Courses</td></tr>";
            foreach ($semregs as $key) {

                // if (!in_array($key->srd2_course_relId, $coursesEnrolled[0])) {
                //     continue;
                // }
                $course = \backend\models\Course::findOne(['id' => $key->srd2_course_relId]);

                $ltrGrade = '-';
                $grpoint = 0;
                $pCourseCrdtSum = 0;
                $grade = isset($key->srd2_grade_relId) ? \backend\models\GradeScale::findOne(['id' => $key->srd2_grade_relId]) : '-';
                if (is_object($grade) && $grade && count($grade) > 0) {
                    $ltrGrade = $grade->gs_ltr_grade;
                    $grpoint = $course->course_crdthr * $grade->gs_gradePoint;
                    $gptSum += $grpoint;
                    $pCourseCrdtSum += $grade->gs_gradePoint == 0?$course->course_crdthr:0;
                }

                $crdtSum += $course->course_crdthr;

                // $coadd = isset($key->srd2_coadd_flag)?'Yes':'NO';
                $sem = \backend\models\Semester::findOne(['id' => $key['srd2SemregRel']['semreg_semester_relId']])->semester_name;
                $acad = \backend\models\SchoolYear::findOne(['id' => $key['srd2SemregRel']['semreg_acadYear_relId']])->sch_year_desc;



                $gradeTable .= "<tr>
                                    <td>" . ++$count . "</td>
                                    <td>{$course->course_code}</td>
                                    <td>{$course->course_title}</td>
                                    <td style='text-align:center;'>{$acad}</td>
                                    <td style='text-align:center;'>{$sem}</td>
                                    <td style='text-align:center;'>{$course->course_crdthr}</td>
                                    <td style='text-align:center;'>{$ltrGrade}</td>
                                    <td style='text-align:right;'>{$grpoint}</td>
                                </tr>";
                // <td style='text-align:center;'>{$coadd}</td>
            }
            $cs1 = $crdtSum;
            $grpt1 = $gptSum;

            $mgpa = $crdtSum == 0 ? '-' : sprintf("%0.2f",round($gptSum / ($crdtSum-$pCourseCrdtSum), 2, PHP_ROUND_HALF_UP));

            $gradeTable .= "<tr></tr>
                        <tr style='font-weight: bold; text-align:center;'>
                            <td colspan=5 style='text-align:right;'>Sub Total</td>
                            <td style='text-align:center;'>{$crdtSum}</td>
                            <td colspan=2 style='text-align:right;'>{$gptSum}</td>
                        </tr>
                        ";

            if (!(strtoupper($program1->program_name) == 'MASTERS')) {
                $gradeTable .= "<tr style='font-weight: bold; text-align:center;'>
                            <td colspan=5 style='text-align:right;'>Major GPA</td>
                            <td colspan=3 style='text-align:left;'>{$mgpa}</td>
                        </tr>";
                $gradeTable .= "<tr style='background-color:#CD0;'><td colspan=8 style='text-align:center; font-weight:bolder;'>Common and Related Courses</td></tr>";
                foreach ($semregs as $key) {

                    if (!in_array($key->srd2_course_relId, $coursesEnrolled[1])) {
                        continue;
                    }
                    $course = \backend\models\Course::findOne(['id' => $key->srd2_course_relId]);

                    $ltrGrade = '-';
                    $grpoint = 0;
                    $grade = isset($key->srd2_grade_relId) ? \backend\models\GradeScale::findOne(['id' => $key->srd2_grade_relId]) : '-';
                    if (is_object($grade) && $grade && count($grade) > 0) {
                        $ltrGrade = $grade->gs_ltr_grade;
                        $grpoint = $course->course_crdthr * $grade->gs_gradePoint;
                        $gptSum += $grpoint;
                        $pCourseCrdtSum += $grade->gs_gradePoint == 0?$course->course_crdthr:0;
                    }

                    $sem = \backend\models\Semester::findOne(['id' => $key['srd2SemregRel']['semreg_semester_relId']])->semester_name;
                    $acad = \backend\models\SchoolYear::findOne(['id' => $key['srd2SemregRel']['semreg_acadYear_relId']])->sch_year_desc;

                    $gradeTable .= "<tr>
                                        <td>" . ++$count . "</td>
                                        <td>{$course->course_code}</td>
                                        <td>{$course->course_title}</td>
                                        <td style='text-align:center;'>{$acad}</td>
                                        <td style='text-align:center;'>{$sem}</td>
                                        <td style='text-align:center;'>{$course->course_crdthr}</td>
                                        <td style='text-align:center;'>{$ltrGrade}</td>
                                        <td style='text-align:right;'>{$grpoint}</td>
                                    </tr>";

                    // /<td style='text-align:center;'>{$coadd}</td>
                    $crdtSum += $course->course_crdthr;
                }

                $gradeTable .= "<tr></tr>
                            <tr style='font-weight: bold; text-align:center;'>
                                <td colspan=5 style='text-align:right;'>Sub Total</td><td style='text-align:center;'>" . ($crdtSum - $cs1) . "</td>
                                <td colspan=3 style='text-align:right;'>" . ($gptSum - $grpt1) . "</td>
                            </tr>
                            <tr style='font-weight: bold; text-align:center;'>
                                <td colspan=5 style='text-align:right;'>Grand Total</td><td style='text-align:center;'>" . ($crdtSum) . "</td>
                                <td colspan=3 style='text-align:right;'>" . ($gptSum) . "</td>
                            </tr>
                            ";   
            }

            $cgpa = $crdtSum == 0 ? '-' : sprintf("%0.2f",round($gptSum / ($crdtSum-$pCourseCrdtSum), 2, PHP_ROUND_HALF_UP));
            $gradeTable .= "<tr></tr>
                            <tr style='font-weight: bold; text-align:center;'>
                                <td colspan=5 style='text-align:right;'>CGPA</td>
                                <td colspan=3 style='text-align:center; text-decoration:underline;'>{$cgpa}</td>
                            </tr>
                            ";

            $gradeTable .= "</tbody></table>";

            return $gradeTable;
        }else if(Yii::$app->user->can('Instructor')){

        } else {
            echo "<p class='bg-danger'>You have no privilage for this action.</p>";
            die();
        }
    }

    public function actionReportTable4(){
        $stdId = isset($_GET['studentId']) && !empty($_GET['studentId']) ? intval($_GET['studentId']) : die('Invalid Student info...');
        // $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : die('Uknown Group.');

        $semregs = SemesterRegistration2::find()
            ->andWhere(['semreg_std_relId' => $stdId])
            ->orderBy(['semreg_acadYear_relId' => SORT_ASC,'semreg_semester_relId' => SORT_ASC])
            ->all();
        $this->layout = 'printLayout';
        // return $this->renderPartial('__report/course-completion-report',['semregModel' => $semregs]);
        return $this->render('__report/course-completion-report',['semregModel' => $semregs]);

    } 

    public function actionReportTable3(){
        $stdId = isset($_POST['studentId']) && !empty($_POST['studentId']) ? intval($_POST['studentId']) : die('Invalid Student info...');
        $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : die('Uknown Group.');

        $semregs = SemesterRegistration2::find()
            ->where(['semreg_std_relId' => $stdId])
            ->orderBy(['semreg_acadYear_relId' => SORT_ASC,'semreg_semester_relId' => SORT_ASC])
            ->all();
        $this->layout = 'printLayout';
        // return $this->renderPartial('__report/course-completion-report',['semregModel' => $semregs]);
        return $this->render('__report/course-completion-report',['semregModel' => $semregs]);

    } 

    public function actionReportTable2(){
        $stdId = isset($_POST['studentId']) && !empty($_POST['studentId']) ? intval($_POST['studentId']) : die('Invalid Student info...');
        $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : die('Uknown Group.');

        $gset = \backend\models\WGroupSetup::findOne(['id' => intval($gsetId)]);

        
        $student = \backend\models\Student::findOne(['id' => intval($stdId)]);

        $acadYear1 = \backend\models\SchoolYear::findOne(['id' => $gset->gs_acadYear_relId]);

        $clslvlNum = \backend\models\ClassLevel::findOne(['id' => intval($gset->gs_clslvl_relId)])->clslvl_num;
        $semesterN = \backend\models\Semester::findOne(['id' => intval($gset->gs_sem_relId)]);
        $semesterNum = $semesterN->semester_name == 'I' ? 1 : ($semesterN->semester_name == 'II' ? 2 : $semesterN->semester_name);
        $admission1 = \backend\models\Admission::findOne(['id' => $gset->gs_adm_relId]);
        $program1 = \backend\models\Program::findOne(['id' => $gset->gs_prog_relId]);
        $dept = \backend\models\Department::findOne(['id' => $gset->gs_dept_relId]);
        $spec = is_null($gset->gs_spec_relId) ? '-' : \backend\models\Specialization::findOne(['id' => $gset->gs_spec_relId])->spec_name;
        $grp = \backend\models\WGroup::findOne(['id' => $gset->gs_group_relId]);
        $gradeTable = "";

        $stdUser = \backend\models\User::findOne(['id' => $student->std_user_relId]);


        $clslvlNum = $clslvlNum == 1 ? '1<sup>st</sup>' : ($clslvlNum == 2 ? '2<sup>nd</sup>' : ($clslvlNum == 3 ? '3<sup>rd</sup>' : $clslvlNum . '<sup>th</sup>'));

        $modelCatalog = new \backend\models\CourseCatalog();
        $srd2Model = new \frontend\models\SemesterRegistration2Detail();

        $cataCourses = $modelCatalog->getCatalogCourse3($gset,$student);

        if ($cataCourses) {

            if (strtoupper($gset->gsProgRel->program_name) != 'MASTERS') {
                return $this->actionReportTablePartial($cataCourses,$gset,$student);
            }


            $gradeTable .= "<div class='container-fluid' style='text-align:center; margin-top:10px;' >";
            $gradeTable .= '
                        <p class="bg-default"><h5><b>'.strtoupper(Yii::$app->params['client']).', '.strtoupper(Yii::$app->params['college']).'<br>OFFICE OF THE REGISTRAR</b></h5></p>
                            <b><u style="text-align:center;">Course Completion Check</u></b><br>
                    </div>';

            $gradeTable .= "<table class='table table-hover table-sm table-condensed table-bordered'>";
            $gradeTable .= "
                            <caption style='text-align:center; font-weight:normal; color:#000'>                    
                                <p class='bg-default'> 
                                    <b>Full Name:</b><u><i>{$student->getFullName()}</u></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <b>Reg.No.:</b> <u><i>{$student->std_regId}</u></i><br>
                                    <b>Program:</b><u><i>{$program1->program_name}</u></i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <b>Admission:</b><u><i>{$admission1->admission_name}</u></i>
                                </p>
                                <p>
                                    <b>Department :</b><u><i>{$dept->dept_name}</u></i>&nbsp;&nbsp;&nbsp;
                                    <b>Specialization :</b><u><i>{$spec}</u></i>
                                </p>
                            </caption>";
            $gradeTable .= "<thead style='background-color:#ace;'>
                    <th>#</th>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Acad. Year</th>
                    <th>Sem.</th>
                    <th>CrdtHr</th>
                    <th>Grade</th>
                    <th>Grd. Pt.</th>
                </thead><tbody>";
            $count = 0;
            $gPointSum  =0;
            $crdtSum = 0;
            $projectCourseCrdtSum = 0;
            foreach ($cataCourses as $CCkey) {

                $course = $CCkey->cocatCourseRel;
                $srd2 = $srd2Model->getSemregInfo($student,$course);

                $lGrade = '-';
                $ayear = '';
                $sem = '';
                $rptd = '';
                $gPoint = 0;
                if ($srd2 && count($srd2) > 0) {//enrolled course
                    // if($srd2->srd2CoffRel->coff2det_cocat_relId != $CCkey->id){
                    //     continue;
                    // }
                    $lGrade = $srd2->srd2_grade_relId?$srd2->srd2GradeRel->gs_ltr_grade:'-';
                    $gPoint = $srd2->srd2_grade_relId?($srd2->srd2GradeRel->gs_gradePoint * $course->course_crdthr):0;
                    $ayear = $srd2->srd2SemregRel->semregAcadYearRel->sch_year_desc;
                    $sem = $srd2->srd2SemregRel->semregSemesterRel->semester_name;
                    $rptd = $srd2->srd2_corptd_flag == 1?'*':'';
                }else{
                    if ($CCkey->cocat_courseelectives){
                        continue;
                    }
                }

                //consider project courses only
                if ($CCkey->cocat_courseProject == 1) {
                    if ($srd2) {
                        if ($srd2->srd2GradeRel && $srd2->srd2GradeRel->gs_gradePoint != 0) {
                            # code...
                        }else{
                            $projectCourseCrdtSum += $course->course_crdthr;
                        }
                    }else{
                        $projectCourseCrdtSum += $course->course_crdthr;
                    }
                }

                $eelctive = $CCkey->cocat_courseelectives ? '(<b>Elective</b>)':'';
                $gPointSum  += $gPoint;
                $gPoint = sprintf("%0.2f",$gPoint);
                $crdtSum += $course->course_crdthr;

                $gradeTable .= "<tr>   
                                <td>".++$count."</td>
                                <td>{$course->course_code}</td>
                                <td>{$course->course_title}{$eelctive}{$rptd}</td>
                                <td>{$ayear}</td>
                                <td>{$sem}</td>
                                <td style='text-align:center;'>{$course->course_crdthr}</td>
                                <td style='text-align:center;'>{$lGrade}</td>
                                <td style='text-align:right;'>{$gPoint}</td>
                            </tr>";
                
            }

            // var_dump($projectCourseCrdtSum);die();

            $cgpa = $crdtSum == 0?0:($gPointSum / ($crdtSum - $projectCourseCrdtSum));


            $gradeTable .= "<tr>
                            <td colspan = 2></td>
                            <td colspan = 2>Total</td>
                            <td colspan = 2 style='text-align:right; font-weight:bold;'>{$crdtSum}</td>
                            <td colspan = 2 style='text-align:right; font-weight:bold;'>{$gPointSum}</td>
                        </tr>";
            $gradeTable .= "<tr>
                            <td colspan = 2></td>
                            <td colspan = 2></td>
                            <td colspan = 2 style='text-align:center; font-weight:bold;'>CGPA</td>
                            <td colspan = 2 style='text-align:center; font-weight:bold;'><u>". sprintf("%0.2f",(round(($cgpa),2,PHP_ROUND_HALF_UP))) ."</u></td>
                        </tr>";
            $gradeTable .= "</tbody></table>";
            echo "{$gradeTable}";
            die();
        }else{
            die("No result.");
        }
    }
    // show students' completion list
    public function actionReportTablePartial($cataCourses,$gset,$student) {
        // List all the courses with their obtained grade that are entrolled by the student.

        $gradeTable = "";

        $clslvlNum = \backend\models\ClassLevel::findOne(['id' => intval($gset->gs_clslvl_relId)])->clslvl_num;
        $semesterN = \backend\models\Semester::findOne(['id' => intval($gset->gs_sem_relId)]);
        $semesterNum = $semesterN->semester_name == 'I' ? 1 : ($semesterN->semester_name == 'II' ? 2 : $semesterN->semester_name);
        $admission1 = \backend\models\Admission::findOne(['id' => $gset->gs_adm_relId]);
        $program1 = \backend\models\Program::findOne(['id' => $gset->gs_prog_relId]);
        $dept = \backend\models\Department::findOne(['id' => $gset->gs_dept_relId]);
        $spec = is_null($gset->gs_spec_relId) ? '-' : \backend\models\Specialization::findOne(['id' => $gset->gs_spec_relId])->spec_name;
        $grp = \backend\models\WGroup::findOne(['id' => $gset->gs_group_relId]);
        $gradeTable = "";

        $stdUser = \backend\models\User::findOne(['id' => $student->std_user_relId]);


        $clslvlNum = $clslvlNum == 1 ? '1<sup>st</sup>' : ($clslvlNum == 2 ? '2<sup>nd</sup>' : ($clslvlNum == 3 ? '3<sup>rd</sup>' : $clslvlNum . '<sup>th</sup>'));

        $gradeTable .= "<div class='container-fluid' style='text-align:center; margin-top:10px;' >";
            $gradeTable .= '
                        <p class="bg-default"><h5><b>'.strtoupper(Yii::$app->params['client']).', '.strtoupper(Yii::$app->params['college']).'<br>OFFICE OF THE REGISTRAR</b></h5></p>
                            <b><u style="text-align:center;">Course Completion Check</u></b><br>
                    </div>';

            $gradeTable .= "<table class='table table-hover table-sm table-condensed table-bordered'>";
            // style='border:1px solid black; padding:1px; margin:5px;'
            // <b>Group:</b><u><i>{$grp->group_name}</u></i><br>
            //                         <b>Semester:</b><u><i>{$semesterN->semester_name}</u></i><br>
            //                         <b>Academic Year:</b> <u><i>{$acadYear1->sch_year_desc}</u></i><br>
            $gradeTable .= "
                            <caption style='text-align:center; font-weight:normal; color:#000'>                    
                                <p class='bg-default'> 
                                    <b>Full Name:</b><u><i>{$student->getFullName()}</u></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <b>Reg.No.:</b> <u><i>{$student->std_regId}</u></i><br>
                                    <b>Program:</b><u><i>{$program1->program_name}</u></i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <b>Admission:</b><u><i>{$admission1->admission_name}</u></i>
                                </p>
                                <p>
                                    <b>Department :</b><u><i>{$dept->dept_name}</u></i>&nbsp;&nbsp;&nbsp;
                                    <b>Specialization :</b><u><i>{$spec}</u></i>
                                </p>
                            </caption>";
            $gradeTable .= "<thead style='background-color:#ace;'>
                    <th>#</th>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Acad. Year</th>
                    <th>Sem.</th>
                    <th>CrdtHr</th>
                    <th>Grade</th>
                    <th>Grd. Pt.</th>
                </thead><tbody>";
            // <th>Crs. Added</th>
            $count = 0;
            $crdtSum = 0;
            $gptSum = 0;
            $displayedCourses= [];
            $gradeTableMajor = "";
            $gradeTableCommon = "";
            
            // echo "<pre>";
            // var_dump($cataCourses);
            // die();

            $srd2Model = new \frontend\models\SemesterRegistration2Detail();

            $gPointTotal = 0;
            $crdtTotal = 0;
            $pcrdtSum  = 0;

            $pcrdtSumMajor = 0;
            $pcrdtSumCommon = 0;

            $gPointSumMajor = 0;
            $gPointSumCommon = 0;

            $projectCourseCrdtSumMajor = 0;
            $projectCourseCrdtSumCommon = 0;

            $crdtSumCommon = 0;
            $crdtSumMajor = 0;

            $gradeTableMajor .= "<tr style='background-color:#CD0;'><td colspan=8 style='text-align:center; font-weight:bolder;'>Major Courses</td></tr>";
            $gradeTableCommon .= "<tr style='background-color:#CD0;'><td colspan=8 style='text-align:center; font-weight:bolder;'>Common and Related Courses</td></tr>";

            foreach ($cataCourses as $CCkey) {

                if ($CCkey->cocat_courseMajor == 1) {//major

                    $course = $CCkey->cocatCourseRel;
                    $srd2 = $srd2Model->getSemregInfo($student,$course);

                    $lGrade = '';
                    $ayear = '';
                    $sem = '';
                    $rptd = '';
                    $gPoint = 0;
                    if ($srd2) {//enrolled course
                        $lGrade = $srd2->srd2_grade_relId?$srd2->srd2GradeRel->gs_ltr_grade:'-';
                        $gPoint = $srd2->srd2_grade_relId?($srd2->srd2GradeRel->gs_gradePoint * $course->course_crdthr):0;
                        $ayear = $srd2->srd2SemregRel->semregAcadYearRel->sch_year_desc;
                        $sem = $srd2->srd2SemregRel->semregSemesterRel->semester_name;
                        $rptd = $srd2->srd2_corptd_flag == 1?'*':'';
                    }

                    //consider project courses only
                    if ($CCkey->cocat_courseProject == 1) {
                        if ($srd2) {
                            if ($srd2->srd2GradeRel->gs_gradePoint != 0) {
                                # code...
                            }else{
                                $projectCourseCrdtSumMajor += $course->course_crdthr;
                            }
                        }else{
                            $projectCourseCrdtSumMajor += $course->course_crdthr;
                        }
                    }

                    $gPointSumMajor  += $gPoint;
                    $crdtSumMajor += $course->course_crdthr;
                    $gPoint = sprintf("%0.2f",$gPoint);
                    $gradeTableMajor .= "<tr>
                        <td>" . ++$count . "</td>
                        <td>{$course->course_code}</td>
                        <td>{$course->course_title}{$rptd}</td>
                        <td style='text-align:center;'>{$ayear}</td>
                        <td style='text-align:center;'>{$sem}</td>
                        <td style='text-align:center;'>{$course->course_crdthr}</td>
                        <td style='text-align:center;'>{$lGrade}</td>
                        <td style='text-align:right;'>{$gPoint}</td>
                    </tr>";
                }else if ($CCkey->cocat_courseMajor == 0) {//common
                    $course = $CCkey->cocatCourseRel;
                    $srd2 = $srd2Model->getSemregInfo($student,$course);

                    $lGrade = '';
                    $ayear = '';
                    $sem = '';
                    $rptd = '';
                    $gPoint = 0;
                    if ($srd2) {//enrolled course
                        $lGrade = $srd2->srd2_grade_relId?$srd2->srd2GradeRel->gs_ltr_grade:'-';
                        $gPoint = $srd2->srd2_grade_relId?($srd2->srd2GradeRel->gs_gradePoint * $course->course_crdthr):'';
                        $ayear = $srd2->srd2SemregRel->semregAcadYearRel->sch_year_desc;
                        $sem = $srd2->srd2SemregRel->semregSemesterRel->semester_name;
                        $rptd = $srd2->srd2_corptd_flag == 1?'*':'';
                    }

                    //consider project courses only
                    if ($CCkey->cocat_courseProject == 1) {
                        if ($srd2) {
                            if ($srd2->srd2GradeRel->gs_gradePoint != 0) {
                                # code...
                            }else{
                                $projectCourseCrdtSumCommon += $course->course_crdthr;
                            }
                        }else{
                            $projectCourseCrdtSumCommon += $course->course_crdthr;
                        }
                    }

                    $gPointSumCommon  += is_numeric($gPoint)?$gPoint:0;
                    $gPoint = is_numeric($gPoint) ? sprintf("%0.2f",$gPoint):'-';
                    $crdtSumCommon += $course->course_crdthr;

                    $gradeTableCommon .= "<tr>
                        <td>" . ++$count . "</td>
                        <td>{$course->course_code}</td>
                        <td>{$course->course_title}{$rptd}</td>
                        <td style='text-align:center;'>{$ayear}</td>
                        <td style='text-align:center;'>{$sem}</td>
                        <td style='text-align:center;'>{$course->course_crdthr}</td>
                        <td style='text-align:center;'>{$lGrade}</td>
                        <td style='text-align:right;'>{$gPoint}</td>
                    </tr>";
                }
            }

            // put MAJOR GPA result here
            $mgpa = $crdtSumMajor == 0 ? '-' : round(($gPointSumMajor) / ($crdtSumMajor-$projectCourseCrdtSumMajor), 2, PHP_ROUND_HALF_UP);

            $gPointTotal += $gPointSumMajor;
            $crdtTotal += $crdtSumMajor;
            $pcrdtSum += $projectCourseCrdtSumMajor;

            $gPointSumMajor = sprintf("%0.2f",$gPointSumMajor);
            $gradeTableMajor .= "<tr></tr>
                <tr style='font-weight: bold; text-align:center;'>
                    <td colspan=5 style='text-align:right;'>Sub Total</td>
                    <td style='text-align:center;'>{$crdtSumMajor}</td>
                    <td colspan=2 style='text-align:right;'>{$gPointSumMajor}</td>
                </tr>
                <tr style='font-weight: bold; text-align:center;'>
                    <td colspan=5 style='text-align:right;'>Major GPA</td>
                    <td colspan=3 style='text-align:left;'>{$mgpa}</td>
                </tr>
                ";

            // put Common course result here::
            $gradeTableCommon .= "<tr></tr>
                <tr style='font-weight: bold; text-align:center;'>
                    <td colspan=5 style='text-align:right;'>Sub Total</td><td style='text-align:center;'>" . ($crdtSumCommon ) . "</td>
                    <td colspan=3 style='text-align:right;'>" . sprintf("%0.2f",round(($gPointSumCommon),2,PHP_ROUND_HALF_UP)) . "</td>
                </tr>
                ";
            $gPointTotal += $gPointSumCommon;
            $crdtTotal += $crdtSumCommon;
            $pcrdtSum += $projectCourseCrdtSumCommon;

            $cgpa = $crdtTotal == 0 ? '-' : round(($gPointTotal) / ($crdtTotal - $pcrdtSum), 2, PHP_ROUND_HALF_UP);

            // $gPointTotal = s
            $gradeTable .= $gradeTableMajor;
            $gradeTable .= $gradeTableCommon;
            $gradeTable .= "<tr></tr>
                    <tr style='font-weight: bold; text-align:center;'>
                        <td colspan=5 style='text-align:right;'>Grand Total</td><td style='text-align:center;'>" . ($crdtTotal) . "</td>
                        <td colspan=3 style='text-align:right;'>" . sprintf("%0.2f",$gPointTotal) . "</td>
                    </tr>
                    <tr style='font-weight: bold; text-align:center;'>
                        <td colspan=5 style='text-align:right;'>CGPA</td>
                        <td colspan=3 style='text-align:left;'>{$cgpa}</td>
                    </tr>
                    ";

            $gradeTable .= "</tbody></table>";

            echo $gradeTable;
            die();
    }

    private function clearRepeatedCourse($srd2){
        $semreg = \frontend\models\SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2','semester_registration_2.id = srd2_semreg_relId')
            ->where([
                'semreg_std_relId'=>$srd2->srd2SemregRel->semregStdRel->id,
            ])
            ->andWhere([
                'srd2_course_relId'=>$srd2->srd2_course_relId,
                'srd2_corptd_flag'=>1
            ])
            ->andWhere(['IS NOT','srd2_grade_relId',NULL])
            ->andWhere([
                '<>','semester_registration_2_detail.id',$srd2->id,
            ])
            ->one();

        if ($semreg && count($semreg) > 0) {
            return $semreg->srd2CourseRel;
        }

        // check for normal added courses.

        $semreg = \frontend\models\SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2','semester_registration_2.id = srd2_semreg_relId')
            ->where([
                'semreg_std_relId'=>$srd2->srd2SemregRel->semregStdRel->id,
            ])
            ->andWhere([
                'srd2_course_relId'=>$srd2->srd2_course_relId,
            ])
            ->andWhere(['IS NOT','srd2_grade_relId',NULL])
            ->andWhere([
                '<>','semester_registration_2_detail.id',$srd2->id,
            ])
            ->one();

        if ($semreg) {
            return $semreg->srd2CourseRel;
        }

        return NULL;
    }

    private function separateMajor($enrolledCourses, $studentObject) {
        $major = [];
        $common = [];

        // check in catalog for major or not.

        $enrolledCoursesArray = [];
        foreach ($enrolledCourses as $key) {
            if (!in_array($key['id'], $enrolledCoursesArray)) {
                $enrolledCoursesArray[] = $key['srd2_course_relId'];
            }
        }
        // get student current group:
        $stdGroup = $studentObject->stdGroupReal;

        if ($stdGroup && count($stdGroup) > 0) {
            # code...
        } else {
            return NULL;
        }
        $cocat = CCF::getCatalogCourse($stdGroup)->andWhere(['cocat_course_relId' => $enrolledCoursesArray])->all();

        // var_dump($enrolledCoursesArray);die();

        if ($cocat) {
            foreach ($cocat as $key) {
                if ($key->cocat_courseMajor == 1) {
                    $major[] = $key->cocat_course_relId;
                } else {
                    $common[] = $key->cocat_course_relId;
                }
            }
        } else {
            return NULL;
        }
        return [$major, $common];
    }

}
