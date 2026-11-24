<?php

namespace frontend\controllers;

use Yii;
use backend\models\ContinuousAssessment;
use backend\models\ContinuousAssessmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \backend\models\ContAssessCatagory;
use \backend\models\ContAssessMarkAssignment;
use yii\base\ErrorException;
use \frontend\models\SemesterRegistration2;
use \common\components\CommonControllerFunctions as CCF;

/**
 * This is the model class for table "cont_assess_mark_assignment".
 *
 * @property integer $id
 * @property integer $ma_catagory_relId
 * @property integer $cat_coff_relId
 * @property string $ma_instance
 * @property double $ma_possiblePoint
 * @property string $ma_created_at
 * @property integer $ma_created_by
 * @property integer $ma_updated_by
 * @property string $ma_updated_at
 *
 * @property ContAssessCatagory $maCatagoryRel
 * @property User $maCreatedBy
 * @property User $maUpdatedBy
 * @property CourseOffering2Detail $catCoffRel
 * @property ContinuousAssessment[] $continuousAssessments
 */

/**
 * This is the model class for table "continuous_assessment".
 *
 * @property integer $id
 * @property integer $ca_coff_relId
 * @property integer $ca_semreg_relId
 * @property integer $ca_std_relId
 * @property integer $ca_assignment_relId
 * @property double $ca_markObtained
 * @property string $ca_created_at
 * @property integer $ca_created_by
 * @property integer $ca_updated_by
 * @property string $ca_updated_at
 * @property integer $ca_approved
 * @property string $ca_approved_at
 * @property integer $ca_approved_by
 *
 * @property CourseOffering2Detail $caCoffRel
 * @property SemesterRegistration2 $caSemregRel
 * @property Student $caStdRel
 * @property User $caCreatedBy
 * @property User $caUpdatedBy
 * @property User $caApprovedBy
 * @property ContAssessMarkAssignment $caAssignmentRel

  /**
 * ContinuousAssessmentController implements the CRUD actions for ContinuousAssessment model.
 */
class ContinuousAssessmentController extends Controller {

    public $acadYearId;
    public $semId;

    protected function __constructor() {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    // a function to return a form to submit a mark for assessment.
    public function actionGetAssessmentSubmissionForm() {
        $courseId = isset($_POST['courseId']) && !empty($_POST['courseId']) ? $_POST['courseId'] : die('Uknown Course');
        $instId = isset($_POST['instId']) && !empty($_POST['instId']) ? $_POST['instId'] : die('Uknown Course course Error.');

        $coffdetId = $_POST['instId'];
        $courseId = $_POST['courseId']; 
        $catObj = new ContAssessCatagory();
        $assessmentObject = new ContAssessMarkAssignment();

        // list assessment catagories for the instructor to choose from.
        $asssessmentCatagory = "<select class='form-control' id='w_t_009' onChange = 'getInstance({$coffdetId},{$courseId})'>";
        $this->__constructor(); //Initialize global vars.
        $course = \backend\models\Course::findOne(['id' => $courseId]);

        // get assessment catagory setup for the course.
        $catagory = $catObj->getCourseCatagor($course->id);

        if ($catagory && count($catagory) > 0) {
            $asssessmentCatagory .= "<option value=''>-</option>";
            foreach ($catagory as $key) {
                $catagoryName = \backend\models\ContAssessCatagoryName::findOne(['id' => $key->cat_name_relId])->cont_cat_name;
                // get assessment made before under this catagory if any. 
                $assessments = $assessmentObject->getCourseAssessInstances($course->id, $coffdetId, $key->id);
                $asInst = "";
                $rem = NULL;
                $maxPoint = $key->cat_maxPoint;
                if ($assessments && count($assessments) > 0) {
                    // if assessed before, check if maximum is reached.
                    $assessMentSum = 0;
                    foreach ($assessments as $key2) {
                        $assessMentSum += $key2->ma_possiblePoint;
                    }
                    if ($assessMentSum < $maxPoint) {
                        $asInst = $catagoryName . '_' . (count($assessments) + 1);
                        $rem = $maxPoint - $assessMentSum;
                    } else {
                        continue;
                    }
                } else {
                    $asInst = $catagoryName . '_1';
                    $rem = $key->cat_maxPoint;
                }

                $asssessmentCatagory .= "<option data-instance='{$asInst}' data-max='{$maxPoint}' data-rem='{$rem}' value='{$key->id}'>{$catagoryName}</option>";
            }
            $asssessmentCatagory .= "</select>";
            echo $asssessmentCatagory;
            die();
        } else {
            die("<p class='bg-danger'>No Assessment catagory made for this course yet. Please configure one first.</p>");
        }
    }

    // protected function getQuerySet(){
    //     $courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])?$_POST['courseId']:die('Uknown Course');
    //     $instId = isset($_POST['instId']) && !empty($_POST['instId'])?$_POST['instId']:die('Uknown Course course Error.');
    //     $this->__constructor(); //Initialize global vars.
    //     $coffdetId =intval( $_POST['instId']);
    //     $courseId = intval($_POST['courseId']);
    //     // ->andWhere(['cont_assess_mark_assignment.cat_coff_relId'=>'course_offering2_detail.id'])
    //     $contass = ContinuousAssessment::find()
    //         ->innerJoin('semester_registration_2','semester_registration_2.id = continuous_assessment.ca_semreg_relId')
    //         ->andWhere(['semreg_acadYear_relId'=>$this->acadYearId, 'semreg_semester_relId'=>$this->semId])
    //         ->innerJoin('course_offering2_detail','course_offering2_detail.id = continuous_assessment.ca_coff_relId')
    //         ->andWhere(['coff2det_course_relId'=>$courseId, 'course_offering2_detail.id'=>$coffdetId])
    //         ->innerJoin('cont_assess_mark_assignment', 'cont_assess_mark_assignment.id = continuous_assessment.ca_assignment_relId')
    //         ->with(['caCoffRel','caSemregRel','caStdRel','caAssignmentRel'])
    //         ->all();
    //     if ($contass && count($contass) > 0) {
    //         $table = '';
    //         $coff2DetId = \backend\models\CourseOffering2Detail::findOne(['id' => $coffdetId]);
    //         if ($coff2DetId && count($coff2DetId) > 0) {
    //             $gsetId = \backend\models\CourseOffering2::findOne(['id' => $coff2DetId->coff2det_coff2_relId])->coff2_group_relId;
    //         } else {
    //             echo "<p class='bg-warning'>Invalid Orrefing record.</p>";
    //             die();
    //         }
    //         $gset = \backend\models\WGroupSetup::findOne(['id'=>$gsetId]);
    //         // Get inst groups from course offering detail.
    //         $dept = \backend\models\Department::findOne(['id'=>$gset->gs_dept_relId])->dept_name;
    //         $clslvl = \backend\models\ClassLevel::findOne(['id'=>$gset->gs_clslvl_relId])->clslvl_num;
    //         $gname = \backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId])->group_name;
    //         $course = \backend\models\Course::findOne(['id'=>$courseId]);
    //         $table .= "<table class='table table-hover table-sm table-bordered table-condensed' style='font-size:9pt;'>";
    //         $table .= "<caption style='text-align:center;'>
    //             <h4 style='text-decoration:underline; text-align:center;'>Mark Sheet.</h4>
    //             Department:<u><i>{$dept}</i></u>&nbsp;&nbsp;&nbsp;
    //             Class Level:<u><i>{$clslvl}</i></u><br>
    //             Group:<u><i>{$gname}</i></u>&nbsp;&nbsp;&nbsp;
    //             Course:<u><i>{$course->course_title}</i></u>&nbsp;&nbsp;&nbsp;
    //             </caption>";
    //         $table .= "<thead style='background-color:#ace;'>
    //                 <th>#</th>
    //                 <th colspan=2 style='text-align:center;'>Student Detail</th>
    //                 <th colspan=1 style='text-align:center;'>Crs Added</th>
    //                 <th colspan=".(count($contass))." style='text-align:center;'>Assessment detail</th>
    //                 <th>Total</th>
    //                 </thead>";
    //         $table .= "<thead style='background-color:#ace;'>
    //                 <th>#</th>
    //                 <th>Reg. ID.</th>
    //                 <th>Student Name</th>
    //                 <th>Crs. Added</th>
    //                 ";
    //         // foreach ($contass[0]['caAssignmentRel'] as $instanceName) {
    //         //     echo $instanceName;
    //         //     // $table .= "<th style='text-align:center;'>{$instanceName->ma_instance}({$instanceName->ma_possiblePoint}%)</th>";
    //         // }
    //     }else{
    //         die("<p class='bg-danger'>ERROR, No record found.</p>");
    //     }
    //     echo "<pre>";
    //     var_dump($contass);die();
    // }
    // return the list of students who enrolled the course offered for an instructor. =>for Report
    
    public function actionEnrstudlists() {
        $table = '';

        $courseId = isset($_POST['courseId']) && !empty($_POST['courseId']) ? $_POST['courseId'] : die('Uknown Course');
        $instId = isset($_POST['instId']) && !empty($_POST['instId']) ? $_POST['instId'] : die('Uknown Course course Error.');
        $this->__constructor(); //Initialize global vars.

        $coffdetId = $_POST['instId'];
        $courseId = $_POST['courseId'];
        $catObj = new ContAssessCatagory();
        $contAssObj = new ContinuousAssessment();

        $coff2DetId = \backend\models\CourseOffering2Detail::findOne(['id' => $coffdetId]);
        if ($coff2DetId && count($coff2DetId) > 0) {
            $gsetId = \backend\models\CourseOffering2::findOne(['id' => $coff2DetId->coff2det_coff2_relId])->coff2_group_relId;
        } else {
            echo "<p class='bg-warning'>Invalid Orrefing record.</p>";
            die();
        }


        $srd = \frontend\models\SemesterRegistration2Detail::find()
                ->where(['srd2_course_relId' => $courseId, 'srd2_group_relId' => $gsetId,])
                ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                ->andWhere(['semreg_acadYear_relId' => $this->acadYearId, 'semreg_semester_relId' => $this->semId,])
                ->with('srd2SemregRel')
                ->all();


        $gset = \backend\models\WGroupSetup::findOne(['id' => $gsetId]);
        // Get inst groups from course offering detail.

        $dept = \backend\models\Department::findOne(['id' => $gset->gs_dept_relId])->dept_name;
        $clslvl = \backend\models\ClassLevel::findOne(['id' => $gset->gs_clslvl_relId])->clslvl_num;
        $gname = \backend\models\WGroup::findOne(['id' => $gset->gs_group_relId])->group_name;
        $course = \backend\models\Course::findOne(['id' => $courseId]);

        if ($srd && count($srd) > 0) {

            $table .= "<table class='table table-hover table-sm table-bordered table-condensed' style='font-size:9pt;'>";
            $table .= "<caption style='text-align:center;'>
                <h4 style='text-decoration:underline; text-align:center;'>Mark Sheet.</h4>
                Department:<u><i>{$dept}</i></u>&nbsp;&nbsp;&nbsp;
                Class Level:<u><i>{$clslvl}</i></u><br>
                Group:<u><i>{$gname}</i></u>&nbsp;&nbsp;&nbsp;
                Course:<u><i>{$course->course_title}</i></u>&nbsp;&nbsp;&nbsp;
                </caption>";
            $mdCatagory = $catObj->getCourseCatagor($course->id);
            $instances = $contAssObj->getAssessmentInstances($course->id, $coffdetId);
            $table .= "<thead style='background-color:#ace;'>
                <th>#</th>
                <th colspan=2 style='text-align:center;'>Student Detail</th>
                <th colspan=1 style='text-align:center;'>Crs Added</th>
                <th colspan=" . (count($instances) + 2) . " style='text-align:center;'>Assessment detail</th>
                
                
                </thead>";
            $table .= "<thead style='background-color:#ace;'>
                    <th>#</th>
                    <th>Reg. ID.</th>
                    <th>Student Name</th>
                    <th>Crs. Added</th>
                    ";
            // get assessment catagries (sub_catagories if any) made for the course and append them as the headr.

            if ($instances && count($instances) > 0) {
                $namesArray = [];
                foreach ($instances as $key) {
                    // display actual instances of assessment under the category.
                    // $catagoryName = \backend\models\ContAssessCatagoryName::findOne(['id'=>$key->cat_name_relId])->cont_cat_name;
                    $instanceName = \backend\models\ContAssessMarkAssignment::findOne(['id' => $key->ca_assignment_relId]);
                    if (!in_array($instanceName->ma_instance, $namesArray)) {
                        $namesArray[] = $instanceName->ma_instance;
                        $table .= "<th style='text-align:center;'>" . $instanceName->ma_instance . "(" . $instanceName->ma_possiblePoint . "%)</th>";
                    }
                }
            } else {

                $table .= "<th colspan=" . (count($instances) - 1) . "><p class='bg-danger'>No mark submitted for this course yet.</p></th>";
            }

            $table .= "<th style='text-align:center;'>Total.</th>
                    <th>Grade</th>";

            // get assessments made under this category.
            // echo "<thead style='background-color:#bbe;'>
            //             <th></th>
            //             <th></th>
            //             <th></th>
            //             <th></th>
            //             ";
            $table .= "</thead><tbody>";
            $count = 0;
            foreach ($srd as $std) {

                $s = \backend\models\Student::findOne(['id' => $std['srd2SemregRel']['semreg_std_relId']]);
                $u = \backend\models\User::findOne(['id' => $s->std_user_relId]);

                $gid = $std->srd2_grade_relId;
                //$grade = is_null($gid)?'-':GradeScale::findOne(['id'=>$gid])->gs_ltr_grade;
                $caddda = is_null($std->srd2_coadd_flag) ? 'NO' : 'YES';
                $table .= "<tr>
                      <td>" . ++$count . "</td>
                      <td>" . $s->std_regId . "</td>
                      <td>" . $u->first_name . ' ' . $u->middle_name . "</td>
                      <td>" . $caddda . "</td>
                     ";
                $totalAssigned = 0;
                $totalgained = 0;
                $grade = 'N/C';
                $ttt = 'Mark Submission NOT COMPLETED yet.';
                foreach ($instances as $key2) {
                    // For each assessment catagory, get the mark obtained by the student.
                    $mark = '-';
                    $instanceName = \backend\models\ContAssessMarkAssignment::findOne(['id' => $key2->ca_assignment_relId]);
                    $mark = $contAssObj->getStudentMark($course->id, $s->id, $key2->id, $instanceName->id);
                    if ($mark && count($mark) > 0) {
                        $totalAssigned += $instanceName->ma_possiblePoint;
                        $mark = $mark->ca_markObtained;
                        $totalgained += $mark;
                        $asessDate = date_format(date_create($key2->ca_created_at), 'D d M Y');
                        $table .= "<td style='text-align:center;' title='{$asessDate}'>" . $mark . "</td>";
                    } else {
                        continue;
                    }
                }
                $table .= "<td style='text-align:center; font-weight:bold;'>" . $totalgained . "/" . $totalAssigned . "</td>";
                if ($totalAssigned >= 100) {
                    // Indicates the mark submission is completed. Ready to calculate LetterGrade depending on the grade scale.
                    $ttt = 'Letter Grade equivalent.';
                    $gradeObj = $contAssObj->setLetterGrade($totalAssigned, $totalgained, $std['srd2SemregRel']['semreg_std_relId'], $coffdetId, $std['id']);

                    if ($gradeObj && count($gradeObj) > 0) {
                        $grade = $gradeObj->gs_ltr_grade;
                    }else{
                        $grade = "N/G";
                    }
                }
                $table .= "<td style='text-align:center;' title='{$ttt}'>{$grade}</td>";
                $table .= "</tr>";
            }
            $table .= "</tbody></table>";
            $count = $count == 0 ? 'NO Student ' : ($count == 1 ? $count . ' Student ' : $count . ' Students ');
            $table .= "<p class='bg-success'><i><b>" . $count . "</b></i> enrolled this course in this semester.</p>";
            echo $table;
            die();
        } else {
            echo "<p class='bg-warning'>No student enrolled this course in this semester.</p>";
        }
    }

    public function actionEnrstudlists2() {
        if (1 == 1) {

            $courseId = isset($_POST['courseId']) && !empty($_POST['courseId']) ? $_POST['courseId'] : die('Uknown Course');
            $instId = isset($_POST['instId']) && !empty($_POST['instId']) ? $_POST['instId'] : die('Uknown Course course Error.');
            $this->__constructor(); //Initialize global vars.

            $coffdetId = $_POST['instId'];
            $courseId = $_POST['courseId'];
            $maxPoint = $_POST['maxPoint'];
            $catObj = new ContAssessCatagory();
            $contAssObj = new ContinuousAssessment();

            $coff2DetId = \backend\models\CourseOffering2Detail::findOne(['id' => $coffdetId]);
            if ($coff2DetId && count($coff2DetId) > 0) {
                $gsetId = \backend\models\CourseOffering2::findOne(['id' => $coff2DetId->coff2det_coff2_relId])->coff2_group_relId;
            } else {
                echo "<p class='bg-warning'>Invalid Orrefing record.</p>";
                die();
            }

            $srd = \frontend\models\SemesterRegistration2Detail::find()
                    ->where(['srd2_course_relId' => $courseId, 'srd2_group_relId' => $gsetId,])
                    ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                    ->andWhere(['semreg_acadYear_relId' => $this->acadYearId, 'semreg_semester_relId' => $this->semId,])
                    ->with('srd2SemregRel')
                    ->all();


            $gset = \backend\models\WGroupSetup::findOne(['id' => $gsetId]);
            // Get inst groups from course offering detail.

            $dept = \backend\models\Department::findOne(['id' => $gset->gs_dept_relId])->dept_name;
            $clslvl = \backend\models\ClassLevel::findOne(['id' => $gset->gs_clslvl_relId])->clslvl_num;
            $gname = \backend\models\WGroup::findOne(['id' => $gset->gs_group_relId])->group_name;
            $course = \backend\models\Course::findOne(['id' => $courseId]);

            if ($srd && count($srd) > 0) {

                echo "<table class='table table-hover table-sm table-bordered table-condensed' style='font-size:9pt;'>";
                // echo "<caption>
                //     <h4 style='text-decoration:underline; text-align:center;'>Mark Sheet.</h4>
                //     Department:<u><i>{$dept}</i></u>&nbsp;&nbsp;&nbsp;<br>
                //     Class Level:<u><i>{$clslvl}</i></u><br>
                //     Group:<u><i>{$gname}</i></u>&nbsp;&nbsp;&nbsp;<br>
                //     Course:<u><i>{$course->course_title}</i></u>&nbsp;&nbsp;&nbsp;<br>
                //     </caption>";
                echo "<thead style='background-color:#ace;'>
                        <th>#</th>
                        <th>Reg. ID.</th>
                        <th>Student Name</th>
                        <th>Mark ({$maxPoint})%</th>
                        ";

                echo "</thead><tbody>";
                $count = 0;
                foreach ($srd as $std) {

                    $s = \backend\models\Student::findOne(['id' => $std['srd2SemregRel']['semreg_std_relId']]);
                    $u = \backend\models\User::findOne(['id' => $s->std_user_relId]);
                    $caddda = "<input data-stdId = '{$s->id}' class='__markInput' type='Number' step='0.01' max='{$maxPoint}' min='0' value='' style='text-align:right;' />";
                    echo "<tr>
                          <td>" . ++$count . "</td>
                          <td>" . $s->std_regId . "</td>
                          <td>" . $u->first_name . ' ' . $u->middle_name . "</td>
                          <td style='text-align:center;'>" . $caddda . "</td>
                         ";

                    echo "</tr>";
                }
                echo "</tbody></table>";
                //$count = $count == 0 ? 'NO Student ' : ($count == 1 ? $count . ' Student ' : $count . ' Students ');
                echo "<p class='bg-success'><button class='btn btn-xs btn-success' onclick='dhfgfvbsfgshgfjsdfatydasdv()' style='float:right;'>Save</button></p>";
            } else {
                echo "<p class='bg-warning'>No student enrolled this course in this semester.</p>";
            }
        } else {
            echo "<p>No data found.</p>";
            die();
        }
    }

    /**
     * Lists all ContinuousAssessment models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ContinuousAssessmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ContinuousAssessment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    // myCreate function
    // save the mark list submitted by the instructor.

    public function actionCreate2() {
        $coffdetId = isset($_POST['coffdetId']) && !empty($_POST['coffdetId']) ? intval($_POST['coffdetId']) : die('Invalid Mark submission. Please correct and re-try');
        $courseId = isset($_POST['courseId']) && !empty($_POST['courseId']) ? intval($_POST['courseId']) : die('Invalid Mark submission. Please correct and re-try');
        $maxPoint = isset($_POST['maxPoint']) && !empty($_POST['maxPoint']) ? $_POST['maxPoint'] : die('Max. point not set for the assessment, please set one.');
        $catagory = isset($_POST['catagory']) && !empty($_POST['catagory']) ? intval($_POST['catagory']) : die('Assessment catagory selection error. Pleasse correct.');

        $studentList = isset($_POST['studentList']) && !empty($_POST['studentList']) ? $_POST['studentList'] : die('Student List empity, please mark atleast one student.');
        $instanceName = isset($_POST['instanceName']) && !empty($_POST['instanceName']) ? $_POST['instanceName'] : die('Instance Name error, Please retry.');

        // coffdetId
        // courseId
        // maxPoint
        // instanceName
        // studentList
        // catagory
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // create instance of assessment,
            $studentList = json_decode($studentList);
            if ($studentList && count($studentList) > 0) {
                
            } else {
                throw new ErrrorException("Student List empty, Please select one.", 1);
            }

            $message = [];
            $assessmentObject = new ContAssessMarkAssignment();
            $assessmentObject->ma_possiblePoint = $maxPoint;
            $assessmentObject->ma_catagory_relId = $catagory;
            $assessmentObject->ma_instance = $instanceName;
            $assessmentObject->cat_coff_relId = $coffdetId;

            $assessmentObject->ma_created_by = $assessmentObject->ma_updated_by = Yii::$app->user->identity->id;
            $assessmentObject->ma_created_at = $assessmentObject->ma_updated_at = date('Y-m-d h:i:s');

            if ($assessmentObject->save()) {
                foreach ($studentList as $key => $value) {

                    // get Semreg detail of the student for this semester.
                    $semregObj = new SemesterRegistration2();
                    $model = new ContinuousAssessment();

                    $ss = $model->getCaSemregRel2($key, $courseId);
                    if ($ss && count($ss) > 0) {
                        $model->ca_semreg_relId = $ss->id;
                    } else {
                        $message = [
                            'std' => $key,
                            'message' => "Student not enrolled this course, please check if any is error."
                        ];
                        continue;
                    }

                    $model->ca_assignment_relId = $assessmentObject->id;
                    $model->ca_coff_relId = $assessmentObject->cat_coff_relId;
                    $model->ca_std_relId = $key;
                    $model->ca_markObtained = $value;

                    $model->ca_created_by = $model->ca_updated_by = Yii::$app->user->identity->id;
                    $model->ca_created_at = $model->ca_updated_at = date('Y-m-d h:i:s');

                    if ($model->save()) {
                        $message = [
                            'std' => $key,
                            'message' => "successful."
                        ];
                        continue;
                    } else {
                        $message = [
                            'std' => $key,
                            'message' => $model->errors,
                        ];
                    }
                }
                $transaction->commit();
                echo 1;
            } else {
                var_dump($assessmentObject->errors);
                die();
                //throw new ErrorException(, 1);
            }

            // save mark for each student under this assessment.
        } catch (ErrorException $e) {
            $transaction->rollBack();
            var_dump($e->getMessage());
            die();
        }
    }

    /**
     * Creates a new ContinuousAssessment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new ContinuousAssessment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ContinuousAssessment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ContinuousAssessment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ContinuousAssessment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ContinuousAssessment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ContinuousAssessment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
