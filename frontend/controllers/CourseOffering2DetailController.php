<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use backend\models\CourseCatalog;
use yii\web\NotFoundHttpException;
use backend\models\CourseOffering2;
use backend\models\CourseOffering2Detail;
use backend\models\CourseOffering2DetailSearch;
use \common\components\CommonControllerFunctions as CCF;

/**
 * CourseOffering2DetailController implements the CRUD actions for CourseOffering2Detail model.
 */
class CourseOffering2DetailController extends Controller
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
    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    public function actionGetInstLoadReportPrint2()
    {
        // retrun all course offering entries of this academic year.
        $this->layout = 'printLayout';
        if (isset($_GET['acadYearId']) && !empty($_GET['acadYearId'])) {
            $acadYearId = intval($_GET['acadYearId']);
        } else {
            return $this->render('__report/inst-load-report2', [
                'activeProgram' => new \frontend\modules\AcademicCalendar\models\AcAcademicYear(),
                'selectYear' => 1
            ]);
        }
        $model = new CourseOffering2Detail();
        $AcademicYearModel = \frontend\modules\AcademicCalendar\models\AcAcademicYear::findOne($acadYearId);
        return $this->renderPartial('__report/inst-load-report2', [
            'model' => $model,
            'activeProgram' => $AcademicYearModel,
            'staffModel' => $model->getOfferingsforThisAcademicYear2()
        ]);
    }

    public function actionGetInstLoadReportPrint()
    {
        // retrun all course offering entries of this academic year.
        $this->layout = 'printLayout';
        $model = new CourseOffering2Detail();
        return $this->render('__report/inst-load-report', ['model' => $model->getOfferingsforThisAcademicYear()]);
    }

    public function actionChangeBypassEvaluationStatus()
    {
        $coff2detId = isset($_POST['coff2detId']) && !empty($_POST['coff2detId']) ? intval($_POST['coff2detId']) : die('INvalId ReqUESt');

        if (Yii::$app->user->can('Registrar')) {
            $model = $this->findModel($coff2detId);
            return $model->toggleBypassStatus();
        }

        return "Inavalid Access";
    }

    public function actionGenerateEvaluationReport()
    {
        $searchModel = new CourseOffering2DetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index-evaluation-report', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionGetOfferingHistoryPrint()
    {

        $this->layout = 'printLayout';

        $searchModel = new CourseOffering2DetailSearch();
        $dataProvider = $searchModel->search([], NULL, NULL, 1);

        $pharams = isset($_SESSION['printParams']['CourseOffering2DetailSearch']) ? $_SESSION['printParams']['CourseOffering2DetailSearch'] : Yii::$app->request->queryParams;

        // var_dump($searchParams);
        // die();

        return $this->render('__report/course-offering-history-print', ['model' => $dataProvider, 'printParams' => $pharams]);
    }

    public function actionGetCourses2()
    {
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? $_POST['admId'] : die('Uknown admission');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? $_POST['progId'] : die('Uknown Program');
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId']) ? $_POST['deptId'] : die('Uknown Deptartment');

        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? $_POST['clslvlId'] : die('Uknown Class Year');

        $groupId = isset($_POST['groupId']) && !empty($_POST['groupId']) ? $_POST['groupId'] : die('Uknown Group');

        $gset = \backend\models\WGroupSetup::findOne($groupId);
    }

    public function actionGetOfferingHistory()
    {
        $coff2detId = isset($_GET['coff2detId']) && !empty($_GET['coff2detId']) ? intval($_GET['coff2detId']) : die('INvalI ReqUESt');

        $model = $this->findModel($coff2detId);

        $rep = $model->getCourseOfferingHistory();
        $_SESSION['rep223'] = $rep;

        return $this->renderPartial('__report/course-offering-history-print', ['model' => $rep]);
    }

    public function actionGetCourses()
    {
        //echo "Getting courses...";
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Uknown admission');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Uknown Program');
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId']) ? intval($_POST['deptId']) : die('Uknown Deptartment');

        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Uknown Class Year');

        $groupId = isset($_POST['groupId']) && !empty($_POST['groupId']) ? intval($_POST['groupId']) : die('Uknown Group');

        //echo "Group setupId: ". $groupId; die();

        $intDept = NULL;

        if (Yii::$app->user->can('DepartmentHead')) {
        } else {
            die("<p class='You can NOT perform this action.'></p>");
        }

        $this->__constructor();

        // Insert into coff2 if not done before
        $coff2Id = null;
        $coffModel = null;
        $alreadyOfferedCourseCount = 0;
        $alreadyOfferedCourse = [];
        $totalCoursesToOffer = 0;

        $exists = CourseOffering2::findOne([
            'coff2_acadYear_relId' => $this->acadYearId,
            'coff2_sem_relId' => $this->semId,
            'coff2_group_relId' => $groupId,
        ]);

        if ($exists) {
            $coff2Id = $exists->id;

            $coff2detail = CourseOffering2Detail::find()
                ->where(['coff2det_coff2_relId' => $coff2Id])
                ->all();

            $alreadyOfferedCourseCount = count($coff2detail);
            if (count($coff2detail) > 0) {
                foreach ($coff2detail as $cd) {
                    array_push($alreadyOfferedCourse, $cd['coff2det_course_relId']);
                }
            }

            //var_dump($alreadyOfferedCourse);die();

        } else {
            $alreadyOfferedCourseCount = 0;
            $coffModel = new CourseOffering2();
            $coffModel->coff2_acadYear_relId = $this->acadYearId;
            $coffModel->coff2_sem_relId = $this->semId;
            $coffModel->coff2_group_relId = intval($groupId);
            $coffModel->admission = 0;
            $coffModel->program = 0;
            $coffModel->department = 0;
            $coffModel->clslvl = 0;
            $coffModel->spec = 0;
            $coffModel->extra_course = 0;

            if ($coffModel->save()) {
                $coff2Id = $coffModel->id;
            } else {
                echo 'Failed to complete, please retry again.';
                var_dump($coffModel->errors);
                die();
            }
        }

        //var_dump($coff2detail);die();

        $courses = CourseCatalog::find()
            ->where([
                'cocat_admission_relId' => $admId,
                'cocat_program_relId' => $progId,
                'cocat_dept_relId' => $deptId,
            ])
            ->andWhere(['OR', ['cocat_clslvl_relid' => $clslvlId,], ['cocat_clslvl_relid' => NULL]])
            ->andWhere(['OR', ['cocat_sem_relId' => $this->semId], ['cocat_sem_relId' => NULL]]);
        // $courses = $courses->

        // Filter if spec is filtered.
        if (isset($_POST['specId']) && !empty($_POST['specId'])) {
            $specId = $_POST['specId'];
            $courses = $courses->andWhere(['OR', ['cocat_spec_relId' => $specId], ['cocat_spec_relId' => NULL]]);
        }

        $courses22 = $courses;
        $totalCoursesToOffer = $courses22->count();


        if (($alreadyOfferedCourseCount > 0) &&  ($totalCoursesToOffer >= $alreadyOfferedCourseCount)) {

            $courses = $courses->andWhere([
                'NOT IN',
                'cocat_course_relId',
                $alreadyOfferedCourse
            ]);
        } else if ($alreadyOfferedCourseCount == $totalCoursesToOffer && $totalCoursesToOffer > 0) {
            echo "<p class='alert alert-success'>All courses of this semester have been offered for this Group.</p>";
            die();
        } else {
            if ($totalCoursesToOffer == 0) {
                echo "<p class='alert alert-danger'>No course found in Catalog for the selected section in this semester. </p>";
                die();
            }
        }

        $courses = $courses->all();

        if ($courses) {
            // echo "<p class='bg-success'> Course offering completed [". $alreadyOfferedCourseCount.' / '.$totalCoursesToOffer ."]</p>";

            $model = new CourseOffering2Detail();

            $instDept = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id])->inst_dep_relId;

            // check if the course is of his/her department or not.
            $deptCourse = \backend\models\Course::findOne(['id' => $courses[0]['cocat_course_relId'], 'course_dept_relId' => $instDept]);
            // if his dept course, allow assigning the instructor for it as TBA optional

            if ($deptCourse) {
                $model->HoDDept = ArrayHelper::map(\backend\models\Department::find()->where(['id' => $instDept])->all(), 'id', 'dept_name');
            }

            $wreport = "";
            // else no option of assignning instructor.

            $wreport .= "<table class='table table-condensed table-striped table-hover table-bordered' style='font-size:10pt;'>
                <thead style='background-color:#8D0;'>
                    <th>#</th>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Credit Hr.</th>
                    <th>Option</th>
                </thead>
                <tbody>";
            $count = 0;
            foreach ($courses as $key) {
                $btn = "<button class='btn btn-primary btn-xs' onclick='offer({$key->id},{$coff2Id},{$key->cocat_course_relId});'>Offer</button>";
                $wreport .= "<tr>
                                <td>" . ++$count . "</td>
                                <td>{$key->cocatCourseRel->course_code}</td>
                                <td>{$key->cocatCourseRel->course_title}</td>
                                <td>{$key->cocatCourseRel->course_crdthr}</td>
                                <td>{$btn}</td>
                            </tr>";
            }
            return $wreport . "</tbody></table>";
        } else {
            echo "<p class='alert alert-success'>All courses for this semester are successfully offered.</p>";
        }
    }

    public function actionGetEnrollmentsPrint()
    {
        $this->layout = 'printLayout';
        if (isset($_SESSION['coff2detId'])) {
            $coff2detId = isset($_SESSION['coff2detId']) && !empty($_SESSION['coff2detId']) ? intval($_SESSION['coff2detId']) : die('INvalI ReqUESt');
            $model = $this->findModel($coff2detId);

            $enrollments = $model->coff2detCoff2Rel
                ->getSemesterRegistration2Details($model->coff2det_course_relId)
                ->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
                ->innerJoin('student', 'student.id=semester_registration_2.semreg_std_relId')
                ->innerJoin('user', 'user.id=student.std_user_relId')
                ->orderBy(['user.first_name' => SORT_ASC, 'user.middle_name' => SORT_ASC])
                ->all();
            // return $this->render('__report/enrollments',['model' => $_SESSION['model'],'enrollments' => $_SESSION['enrollments'],'print' => false]);
            return $this->render('@frontend/views/course-offering2-detail/__report/enrollments', ['model' => $model, 'enrollments' => $enrollments, 'print' => false]);
        } else {
            return null;
        }
    }

    public function actionGetEnrollments()
    {
        $coff2detId = isset($_GET['coff2detId']) && !empty($_GET['coff2detId']) ? intval($_GET['coff2detId']) : die('INvalI ReqUESt');

        $model = $this->findModel($coff2detId);

        $enrollments = $model->coff2detCoff2Rel
            ->getSemesterRegistration2Details($model->coff2det_course_relId)
            ->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
            ->innerJoin('student', 'student.id=semester_registration_2.semreg_std_relId')
            ->innerJoin('user', 'user.id=student.std_user_relId')
            ->orderBy(['user.first_name' => SORT_ASC, 'user.middle_name' => SORT_ASC])
            ->all();

        $_SESSION['coff2detId'] = $coff2detId;
        // $_SESSION['enrollments'] = $enrollments;

        if (isset($_GET['hide'])) {
            // $this->layout='__pdf_print_layout';
            // $content = $this->renderPartial('@frontend/views/course-offering2-detail/__report/enrollments',['model' => $model,'enrollments' => $enrollments,'print' => false]);
            // $pdf = Yii::$app->pdfk;
            // return $pdf->generatePDF($content);

            $content = $this->renderPartial('@frontend/views/course-offering2-detail/__report/enrollments', ['model' => $model, 'enrollments' => $enrollments, 'print' => false]);
            $pdf = Yii::$app->pdf;
            // exportData($title='',$filename='Pdf File',$html=NULL)
            return $pdf->exportData('', '', $content);
        }

        return $this->renderPartial('__report/enrollments', ['model' => $model, 'enrollments' => $enrollments]);
    }

    /**
     * Lists all CourseOffering2Detail models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CourseOffering2DetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->user->can('DepartmentHead')) {
            return $this->render('__report/index-for-hod', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetDetail()
    {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('Invalid request');
        return $this->renderAjax('/mark-distribution-report/index', ['model' => $this->findModel($id)]);
    }
    /**
     * Displays a single CourseOffering2Detail model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    // public function actionSaveDetail(){
    //     var_dump($model);die();
    // }

    public function actionCreate2()
    {
        $cocatId = isset($_POST['cocatId']) ? intval($_POST['cocatId']) : die('Error, Course Matrix entry INVALID');
        $coff2Id = isset($_POST['coff2Id']) ? intval($_POST['coff2Id']) : die('Error, Course Offering entry INVALID');
        $courseId = isset($_POST['courseId']) ? intval($_POST['courseId']) : die('Error, Course entry INVALID');

        $model = new CourseOffering2Detail();

        $model->coff2det_course_relId = $courseId;
        $model->coff2det_coff2_relId = $coff2Id;
        $model->coff2det_cocat_relId = $cocatId;
        $model->coff2det_course_relId = $courseId;
        $model->coff2det_registrarApproved = NULL;

        $model->coff2det_created_by = $model->coff2det_updated_by = Yii::$app->user->id;
        $model->coff2det_created_at = $model->coff2det_updated_at = date('Y-m-d h:i:s');

        if ($model->save()) {
            return 1;
        }

        var_dump($model->getErrors());
        die();
    }

    /**
     * Creates a new CourseOffering2Detail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CourseOffering2Detail();

        if ($model->load(Yii::$app->request->post())) {

            //var_dump($model);die();
            $model->coff2det_created_at = $model->coff2det_updated_at = date('Y-m-d h:i:s');
            $model->coff2det_created_by = $model->coff2det_updated_by = Yii::$app->user->identity->id;
            $model->coff2det_registrarApproved = 1;
            $model->coff2det_registrarApproved_by = NULL;
            $model->coff2det_registrarApproved_at = NULL;

            if ($model->save()) {
                return json_encode([
                    'ok' => 1,
                    'message' => 'Success'
                ]);
            } else {
                return json_encode([
                    'ok' => 0,
                    'message' => $model->errors,
                ]);
            }
            //die();
        } else {
            if (Yii::$app->user->can('DepartmentHead')) {
                // check if the course is of his/her department or not.
                $instDept = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id])->inst_dep_relId;
                $deptCourse = \backend\models\Course::findOne(['id' => $model->coff2det_course_relId, 'course_dept_relId' => $instDept]);
                // if his dept course, allow assigning the instructor for it as TBA optional
                $model->HoDDept = [];
                if ($deptCourse) {
                    // ->where(['id'=>$instDept])
                    $model->HoDDept = ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name');
                }
            }
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CourseOffering2Detail model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/course-offering2/index']);
        } else {
            if (Yii::$app->user->can('DepartmentHead')) {
                // check if the course is of his/her department or not.
                $instDept = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id])->inst_dep_relId;
                $deptCourse = $model->coff2detCourseRel;
                // if his dept course, allow assigning the instructor for it as TBA optional
                $model->HoDDept = [];
                if ($deptCourse) {
                    $model->HoDDept = ArrayHelper::map(\backend\models\Department::find()->where(['id' => $instDept])->all(), 'id', 'dept_name');
                }
            }
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CourseOffering2Detail model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    private function removeFromCoff2CatList($coff2DetModel)
    {
        $coff2Model = $coff2DetModel->coff2detCoff2Rel;
        $deCat = json_decode($coff2Model->coff2_cocatIds, true);

        if (in_array($coff2DetModel->coff2det_cocat_relId, $deCat)) {
            $CATIDS = array_diff($deCat, [$coff2DetModel->coff2det_cocat_relId]);
            $coff2Model->coff2_cocatIds = json_encode($CATIDS);
            $coff2Model->update();

            return;
        } else {
            // var_dump($deCat,$coff2DetModel->coff2det_cocat_relId,$coff2Model->id);die();
        }
    }
    public function actionDelete($id)
    {
        if (Yii::$app->user->can('Registrar')) {
            $thisModel = $this->findModel($id);
            $this->removeFromCoff2CatList($thisModel);
            $coffTempModel = $thisModel->coff2detCoff2Rel->coff2detAcProgRel->coffTemp;
            if ($coffTempModel) {

                $deCat = json_decode($coffTempModel->cot_cocat_IDS, true);
                if (in_array($thisModel->coff2det_cocat_relId, $deCat)) {

                    $CATIDS = array_diff($deCat, [$thisModel->coff2det_cocat_relId]);
                    $coffTempModel->cot_cocat_IDS = json_encode($CATIDS);
                    if ($coffTempModel->update(false)) {
                        $thisModel->delete();
                    } else {
                        var_dump($coffTempModel->getErrors());
                        die();
                    }
                } else {
                    $thisModel->delete();
                }
            } else {
                $thisModel->delete();
            }
        } else {
            throw new \yii\web\ForbiddenHttpException("Action is NOT allowed", 1);
        }
        return Yii::$app->request->isAjax ? 1 : $this->redirect(['/course-offering2/index']);
    }

    /**
     * Finds the CourseOffering2Detail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CourseOffering2Detail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CourseOffering2Detail::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
