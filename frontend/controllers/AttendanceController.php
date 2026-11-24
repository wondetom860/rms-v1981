<?php

namespace frontend\controllers;

use Yii;
use backend\models\Attendance;
use backend\models\Instructor;
use backend\models\AttendanceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \common\components\CommonControllerFunctions as CCF;

/**
 * AttendanceController implements the CRUD actions for Attendance model.
 */
class AttendanceController extends Controller
{

    public $acadYearId;
    public $semId;

    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

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
                        'actions' => ['index', 'delete', 'view'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Admin'],
                    ],
                    [
                        'actions' => ['index', 'create-attendance', 'enrstudlists', 'student-list', 'course-list', 'delete', 'view', 'update', 'absent-student', 'present-student', 'exam-attendance-sheet', 'get-attendance-sheet-report', 'get-attendance-sheet-report-print', 'get-class-attendance-form-report', 'class-attendance-form', 'get-attendance-form-report-print'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Instructor', 'DepartmentHead'],
                    ],
                    [
                        'actions' => ['index', 'create-attendance', 'enrstudlists', 'student-list', 'course-list'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }

    public function actionGetAttendanceSheetReportPrint()
    {
        $this->layout = 'printLayout';
        $model = new Attendance();
        return $this->render('__report/exam_attendace_sheet', ['attendanceModel' => $model, 'semRegs' => $_SESSION['semRegs'], 'iar' => $_SESSION['iar'], 'courseId' => $_SESSION['courseId'], 'gsetId' => $_SESSION['gsetId'], 'print' => 1]);
    }

    public function actionGetAttendanceFormReportPrint()
    {
        $this->layout = 'printLayout';
        $model = new Attendance();
        return $this->render('__report/class-attendance-form', ['attendanceModel' => $model, 'semRegs' => $_SESSION['semRegs'], 'iar' => $_SESSION['iar'], 'courseId' => $_SESSION['courseId'], 'gsetId' => $_SESSION['gsetId'], 'print' => 1]);
    }

    public function actionGetClassAttendanceFormReport()
    {
        $iar = isset($_POST['iar']) && !empty($_POST['iar']) ? $_POST['iar'] : NULL;
        // $iar = $iar == 'on' ? 1:null;
        $coffdetId = isset($_POST['coffdetId']) && !empty($_POST['coffdetId']) ? intval($_POST['coffdetId']) : die('Invalid request.');
        $groupId = isset($_POST['groupId']) && !empty($_POST['groupId']) ? intval($_POST['groupId']) : die('Invalid request.');

        $coff2det = \backend\models\CourseOffering2Detail::findOne($coffdetId);

        $courseId = $coff2det->coff2det_course_relId;

        $model = new Attendance();

        $attendanceData = Attendance::find()
            ->where(['attendance.a_course_relId' => $courseId])
            ->andWhere(['attendance.a_group_relId' => $groupId]);
        $numberOfClasses = $attendanceData->count();
        $attendanceData = $attendanceData->all();

        $semRegs = \backend\models\SemesterRegistration2::find()
            ->innerJoin('semester_registration_2_detail', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->innerJoin('student', 'student.id = semester_registration_2.semreg_std_relId')
            ->innerJoin('user', 'user.id = student.std_user_relId')
            ->where([
                'srd2_course_relId' => intval($courseId),
                'srd2_group_relId' => intval($groupId),
            ])
            ->orderBy(['user.first_name' => SORT_ASC, 'user.middle_name' => SORT_ASC])
            ->all();

        // preserve for the feature, rather than re-computing 
        $_SESSION['semRegs'] = $semRegs;
        $_SESSION['iar'] = $iar;
        $_SESSION['courseId'] = $courseId;
        $_SESSION['gsetId'] = $groupId;


        return $this->renderPartial('__report/class-attendance-form', ['attendanceModel' => $model, 'semRegs' => $semRegs, 'iar' => $iar, 'courseId' => $courseId, 'gsetId' => $groupId, 'print' => 0, 'coff2Det' => $coff2det]);
    }
    public function actionGetAttendanceSheetReport()
    {
        $iar = isset($_POST['iar']) && !empty($_POST['iar']) ? $_POST['iar'] : NULL;
        // $iar = $iar == 'on' ? 1:null;
        $coffdetId = isset($_POST['coffdetId']) && !empty($_POST['coffdetId']) ? intval($_POST['coffdetId']) : die('Invalid request.');
        $groupId = isset($_POST['groupId']) && !empty($_POST['groupId']) ? intval($_POST['groupId']) : die('Invalid request.');

        $coff2det = \backend\models\CourseOffering2Detail::findOne($coffdetId);

        $courseId = $coff2det->coff2det_course_relId;

        $model = new Attendance();

        $attendanceData = Attendance::find()
            ->where(['attendance.a_course_relId' => $courseId])
            ->andWhere(['attendance.a_group_relId' => $groupId]);
        $numberOfClasses = $attendanceData->count();
        $attendanceData = $attendanceData->all();

        $semRegs = \backend\models\SemesterRegistration2::find()
            ->innerJoin('semester_registration_2_detail', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->innerJoin('student', 'student.id = semester_registration_2.semreg_std_relId')
            ->innerJoin('user', 'user.id = student.std_user_relId')
            ->where([
                'srd2_course_relId' => intval($courseId),
                'srd2_group_relId' => intval($groupId),
            ])
            ->orderBy(['user.first_name' => SORT_ASC, 'user.middle_name' => SORT_ASC])
            ->all();

        // preserve for the feature, rather than re-computing 
        $_SESSION['semRegs'] = $semRegs;
        $_SESSION['iar'] = $iar;
        $_SESSION['courseId'] = $courseId;
        $_SESSION['gsetId'] = $groupId;


        return $this->renderPartial(
            '__report/exam_attendace_sheet',
            [
                'attendanceModel' => $model,
                'semRegs' => $semRegs,
                'iar' => $iar,
                'courseId' => $courseId,
                'gsetId' => $groupId,
                'print' => 0,
                'coff2Det' => $coff2det
            ]
        );
    }

    // get-attendance-form
    public function actionClassAttendanceForm()
    {
        $searchModel = new AttendanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('class-attendance-form', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        // return $this->render('exam-attendance-sheet');
    }


    public function actionExamAttendanceSheet()
    {
        $searchModel = new AttendanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('exam-attendance-sheet', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        // return $this->render('exam-attendance-sheet');
    }

    public function actionAbsentStudent()
    {
        $attendanceEntry = isset($_POST['attendanceEntry']) && !empty($_POST['attendanceEntry']) ? intval($_POST['attendanceEntry']) : die('Invalid request.');
        $stdId = isset($_POST['stdId']) && !empty($_POST['stdId']) ? intval($_POST['stdId']) : die('Invalid Request');

        $model = $this->findModel($attendanceEntry);
        if (isset($_POST['mode']) && !empty($_POST['mode']) && intval($_POST['mode']) == 1) {

            if (!in_array($stdId, json_decode($model->a_presentStudentsList, true))) {
                return "Student already absent from that class session";
            }

            $p = array_diff(json_decode($model->a_presentStudentsList, true), [$stdId]);

            $model->a_presentStudentsList = json_encode($p);

            // var_dump($model->a_presentStudentsList);die();

            //creates object but I want an array of the form [23,33,65,90]
            // but what it does is {0:22,1:43}....

            $model->a_updatedBy = Yii::$app->user->identity->id;
            $model->a_updatedAt = date("Y-m-d h:i:s");

            $model->update();
        }

        return $this->renderPartial('__report/absents', ['model' => $model]);
    }

    public function actionPresentStudent()
    {
        $attendanceEntry = isset($_POST['attendanceEntry']) && !empty($_POST['attendanceEntry']) ? intval($_POST['attendanceEntry']) : die('Invalid request.');
        $stdId = isset($_POST['stdId']) && !empty($_POST['stdId']) ? intval($_POST['stdId']) : die('Invalid Request');

        $model = $this->findModel($attendanceEntry);
        if (isset($_POST['mode']) && !empty($_POST['mode']) && intval($_POST['mode']) == 1) {
            $decodedList = (array)json_decode($model->a_presentStudentsList, true);
            if (in_array($stdId, $decodedList)) {
                return "Student already in present in the list";
            }

            array_push($decodedList, $stdId);
            $model->a_presentStudentsList = json_encode($decodedList);

            $model->a_updatedBy = Yii::$app->user->identity->id;
            $model->a_updatedAt = date("Y-m-d h:i:s");

            $model->update();
        }

        return $this->renderPartial('__report/presents', ['model' => $model]);
    }

    /**
     * Lists all Attendance models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AttendanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Attendance model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Attendance model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Attendance();
        // $modelAttendanceDetail = new Attendancedetail();
        $attndanceId = 0;
        if ($model->load(Yii::$app->request->post())) {
            $attndanceId = $model->id;
            // $model->a_created
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
            //'courseID' => $courseID,
        ]);
    }

    public function actionCreateAttendance()
    {
        // Yii::$app->session->setFlash('success', "Welcome Back !");
        $count = 0;
        $userInfromIns = 0;
        if (!(Yii::$app->user->can('Instructor') || Yii::$app->user->can('DepartmentHead'))) {
            throw new \yii\web\ForbiddenHttpException("Action NOT allowed!", 1);
        } else {
            $model = new Attendance();

            $findInsId = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);

            $model->a_instructor_relId = $findInsId->id;
            $stdList = [];

            if (isset($_POST['studentsList']) && !empty($_POST['studentsList'])) {

                $courseId = $_POST['courseId'];
                $coffDetId = $_POST['coffdetID'];
                // $attendanceRemark = $_POST['attendanceRemark'];
                $group = intval($_POST['groupId']);
                $date = $_POST['date'];
                $aHour = intval($_POST['aHour']);
                $studentList = $_POST['studentsList'];
                // $model->a_instructor_relId = Yii::$app->user->identity->id;
                $model->a_course_relId = intval($courseId);
                $model->a_group_relId = intval($group);
                $model->a_coff_relId = intval($coffDetId);
                $model->a_createdAt = $date;
                $model->a_hour = $aHour;
                $model->a_presentStudentsList = $studentList;
                // $model->a_remark = $attendanceRemark;
                $model->a_createdBy = Yii::$app->user->identity->id;
                $model->a_updatedBy = Yii::$app->user->identity->id;

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Attendance recorded successfully!");
                    return $this->redirect('index');
                } else {
                    print_r($model->errors);
                    die();
                }
            } else {
                return $this->render('create1', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Updates an existing Attendance model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Attendance model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->a_createdBy == Yii::$app->user->identity->id) {
            $model->delete();
        } else {
            throw new yii\web\ForbiddenHttpException("You are not allowed to perform this operation.555", 1);
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Attendance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Attendance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Attendance::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    // public function actionStudentList() {
    //     $modelGroupSetup = new WGroupSetup();
    //     $modelGroupAssignment = new WGroupAssignment();
    //     if (isset($_POST['courseID']) && !empty($_POST['courseID']) && isset($_POST['groupID']) && !empty($_POST['groupID'])) {
    //         $courseId = intval($courseID);
    //         $groupId = intval($groupID);

    //         $findGroupSetupId = WGroupSetup::find('id')->where(['gs_group_relId' => $groupId])->one();
    //         foreach ($findGroupSetupId as $ID) {
    //             $gSetIpId = $ID->id;
    //         }
    //         $studentslist = WGroupAssignment::find('gas_std_relId')->where(['gas_gset_relId' => $gSetIpId])->all();
    //     }
    // }

    private function ddOption($coffDetObj)
    {
        $option = "<option value=''> - </option>";
        if (!empty($coffDetObj)) {
            foreach ($coffDetObj as $key) {
                $coff = \backend\models\CourseOffering2::findOne($key->coff2det_coff2_relId);
                $gset = \backend\models\WGroupSetup::findOne($coff->coff2_group_relId);

                $gname = \backend\models\WGroup::findOne($gset->gs_group_relId);

                $adm = \backend\models\Admission::findOne($gset->gs_adm_relId);
                $prog = \backend\models\Program::findOne($gset->gs_prog_relId);
                $dept = \backend\models\Department::findOne($gset->gs_dept_relId);
                $clslvl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId);
                $spec = is_null($gset->gs_spec_relId) ? '' : '_' . \backend\models\Specialization::findOne($gset->gs_spec_relId)->spec_name;

                $title = $gname->group_name . '_' . $adm->admission_name . '_' . $prog->program_name . '_ ' . $dept->dept_name . '_' . $clslvl->clslvl_num . $spec;


                $course = \backend\models\Course::findOne($key->coff2det_course_relId);
                $option .= "<option data-gsetId='{$gset->id}' data-course='{$course->id}' data-gname = {$gname->group_name} title='{$title}' value='{$key->id}'>{$course->course_title}</option>";
            }
        }

        echo "{$option}";
        die();
    }
    private function myCourse()
    {
        $usrId = Yii::$app->user->identity->id;
        $this->__constructor();

        $instId = Instructor::findOne(['inst_user_relId' => $usrId])->id;

        $coff3 = \backend\models\CourseOffering2Detail::find()
            ->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
            ->where([
                'course_offering2.coff2_acadYear_relId' => $this->acadYearId,
                'course_offering2.coff2_sem_relId' => $this->semId,
            ])
            ->andWhere([
                'course_offering2_detail.coff2det_inst_relId' => $instId
            ])
            ->orderBy('course_offering2_detail.coff2det_course_relId ASC')
            ->all();


        $instCourses = $coff3;

        if (empty($instCourses) && count($instCourses) < 1) {
            echo "<p class='bg-danger'>No course assigned for you in this semester</p>";
            die();
        } else {
            $cc = "";
            if (isset($_POST['rrrrrrr'])) {
                return $this->ddOption($instCourses);
            }
            $cc .= "<table class='table table-hover table-sm table-bordered table-condensed'>";
            $cc .= "<thead style='background-color:#ace;'>
                        <th>#</th>
                        <th>Course Detail</th>
                        <th>Yr.</th>
                        <th>Grp</th>
                        <th>CrdtHr</th>
                        <th>Show</th>
                    </thead>
                    <tbody>";
            $count = 0;
            foreach ($instCourses as $instCourse) {
                // $course = Course::findOne(['id' => $instCourse['coff2det_course_relId']]);
                $course = $instCourse->coff2detCourseRel;

                // $gsetId = CourseOffering2::findOne($instCourse['coff2det_coff2_relId'])->coff2_group_relId;
                $groupId = $instCourse->coff2detCoff2Rel->coff2GroupRel;

                $yr = $groupId->gsClslvlRel->clslvl_num;
                $group = $groupId->gsGroupRel->group_name;
                $tttt = $groupId->gsDeptRel->dept_name;

                if (!is_null($groupId->gs_spec_relId)) {
                    $tttt .= ' : ' . $groupId->gsSpecRel->spec_name;
                }

                $ta = $instCourse['coff2det_TA_relId'] == $instId ? 'danger' : 'default';
                $taaa = '';

                if (
                    $instCourse['coff2det_TA_relId'] != $instId &&
                    !is_null($instCourse['coff2det_TA_relId'])
                ) {

                    $taInst = $instCourse->coff2detTARel;
                    $taUser = $taInst->instUserRel;

                    $taaa .= 'Tech. Assistant : ' . $taUser->getFullName();
                }
                $cc .= "<tr class='{$ta}' title='" . $taaa . "'>
                        <td>" . ++$count . "</td>
                        <td>" . $course->course_code . ' => ' . $course->course_title . "</td>
                        <td style='text-align:center;'>" . $yr . "</td>
                        <td style='text-align:center;' title='" . $tttt . "'>" . $group . "</td>
                        <td style='text-align:center;'>" . $course->course_crdthr . "</td>
                        <td><button class='btn btn-sm btn-success' onclick='loadStudent(" . $instCourse['id'] . "," . $course->id . "," . $groupId->id . ")'>Students</button></td>
                    </tr>";
            }
            $cc .= "</tbody></table>";

            return ($cc . "<p class='bg-success'>" . count($instCourses) . " courses offered  this semester.</p>");
        }
    }

    public function actionCourseList()
    { //course-list
        return $this->myCourse();
    }

    // return the list of students who enrolled the course offered for an instructor.
    public function actionEnrstudlists()
    { //enrstudlists
        if (isset($_POST['instId']) && !empty($_POST['instId']) && isset($_POST['courseId']) && !empty($_POST['courseId'])) {

            $this->__constructor(); //Initialize global vars.

            $coffdetId = $_POST['instId'];
            $courseId = $_POST['courseId'];

            $coff2DetId = \backend\models\CourseOffering2Detail::findOne(['id' => $coffdetId]);
            if ($coff2DetId) {
                $gsetId = $coff2DetId->coff2detCoff2Rel->coff2_group_relId;
            } else {
                echo "<p class='bg-warning'>Invalid Orrefing record.</p>";
                die();
            }

            $stds2 = \backend\models\SemesterRegistration2::find()
                ->innerJoin('student', 'student.id = semester_registration_2.semreg_std_relId')
                ->innerJoin('user', 'user.id = student.std_user_relId')
                ->where([
                    'semreg_acadYear_relId' => $this->acadYearId,
                    'semreg_semester_relId' => $this->semId,
                ])->orderBy('user.first_name');

            $rep_wonde = "";
            // Get inst groups from course offering detail.

            if (!empty($stds2)) {
                $stds2 = $stds2->all();


                //<th>Grade</th>      Use this if for displaying the grade of students enrolled his/her course.
                // <th>Save</th>
                // <td>". $grade ."</td>
                // <td><button class='btn btn-success btn-sm'>Option</button></td>

                $rep_wonde .= "<table class='table table-hover table-sm table-bordered table-condensed'>";
                $rep_wonde .= "<thead style='background-color:#8D0;'>
                        <th>#</th>
                        <th>Reg. ID.</th>
                        <th>Student Name</th>
                        <th>Status <input type='checkbox' id='seelctAll' onchange='selectAll()' /> </th>
                    </thead><tbody>";
                $count = 0;
                foreach ($stds2 as $std) {

                    $s = $std->semregStdRel;
                    $u = $s->stdUserRel;

                    $srd = \backend\models\SemesterRegistration2Detail::findOne([
                        'srd2_semreg_relId' => $std['id'],
                        'srd2_course_relId' => $courseId,
                        'srd2_group_relId' => $gsetId,
                    ]);

                    if (empty($srd)) {
                        continue;
                    }
                    // $gid = $srd->srd2_grade_relId;
                    //$grade = is_null($gid)?'-':GradeScale::findOne(['id'=>$gid])->gs_ltr_grade;
                    // $caddda = is_null($srd->srd2_coadd_flag)?'NO':'YES';
                    $rep_wonde .= "<tr>
                          <td style='text-align:center;'>" . ++$count . "</td>
                          <td>" . $s->std_regId . "</td>
                          <td>{$s->getFullName()}</td>
                          <td style='text-align:center;'><input type='checkbox' class='selectionBox' onchange='addtoList({$s->id})' id='{$std->semreg_std_relId}' value=''/> </td>
                    </tr>";

                    //echo "<hr>";
                }
                $rep_wonde .= "</tbody></table>";
                return $rep_wonde;
                // $count = $count==0?'NO Student ':($count == 1?$count.' Student ':$count.' Students ');
                // echo "<p class='bg-success'><i><b>". $count ."</b></i> enrolled this course in this semester.</p>";
            } else {
                echo "<p class='bg-warning'>No student enrolled this course in from from the selected group..</p>";
            }
        } else {
            echo "<p>No data found.</p>";
            die();
        }
    }
}
