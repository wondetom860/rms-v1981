<?php

namespace frontend\controllers;

use backend\models\Course;
use Yii;
use backend\models\SemesterRegistration2;
use backend\models\SemesterRegistration2Detail;
use backend\models\SemesterRegistration2Search;
use frontend\modules\AcademicCalendar\models\AcActivePrograms;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SemesterRegistrationController implements the CRUD actions for SemesterRegistration model.
 */
class SemesterRegistrationController extends Controller
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
                        'actions' => ['get-course-failed-students', 'get-course-failed-students2', 'index-grade-report', 'get-semester-grade-report', 'get-srd2-detail', 'get-report-print', 'get-report-print-backlog', 'index', 'update', 'view', 'delete', 'print-point-history', 'active-student-list'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Registrar', 'UniversityRegistrar'],
                    ],
                    [
                        'actions' => ['get-course-failed-students', 'get-course-failed-students2', 'index-grade-report', 'get-semester-grade-report', 'get-srd2-detail', 'get-report-print', 'get-report-print-backlog', 'index', 'update', 'view', 'print-point-history', 'active-student-list'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Admin',],
                    ],
                    [
                        'actions' => ['index-grade-report', 'get-semester-grade-report', 'get-srd2-detail', 'get-report-print', 'get-report-print-backlog', 'index', 'print-point-history'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['DepartmentHead', 'Instructor', 'clerk'],
                    ],
                    [
                        'actions' => ['index-grade-report', 'get-semester-grade-report', 'get-srd2-detail', 'index'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Student'],
                    ],

                ]
            ]
        ];
    }

    // print-point-history

    public function actionActiveStudentList()
    {
        $searchModel = new SemesterRegistration2Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 1);
        $pharams = isset($_SESSION['printParams']['SemesterRegistration2Search']) ? $_SESSION['printParams']['SemesterRegistration2Search'] : Yii::$app->request->queryParams;
        $this->layout = 'printLayout';
        return $this->render(
            '/semester-registration/__report/active-student-list',
            [
                'model' => $dataProvider,
                'printParams' => $pharams,
            ]
        );
    }

    public function actionPrintPointHistory()
    {
        $acipId = Yii::$app->request->get('acpId');
        $activeProgramModel = AcActivePrograms::findOne($acipId);
        $this->layout = 'printLayout';
        return $this->render('__report/gc-print-point-history', [
            'model' => $activeProgramModel,
        ]);
    }

    public function actionGetCourseFailedStudents2()
    {
        $courseId = Yii::$app->request->get('courseId');
        $catalogid = Yii::$app->request->get('catalogid'); //
        // var_dump($catalogid);exit;
        $course = Course::findOne($courseId);
        // $srd2 = new SemesterRegistration2Detail();
        // $srd2->setCatalogAttributeValue();
        // getCatalogMinPassGrade();
        return $this->renderPartial('__report/course-failed-students2', [
            'students' => SemesterRegistration2Detail::getCourseFailedStudents2(),
            'course' => $course
        ]);
    }

    public function actionGetCourseFailedStudents()
    {
        $courseId = Yii::$app->request->get('courseId');
        $catalogid = Yii::$app->request->get('catalogid'); //
        // var_dump($catalogid);exit;
        $course = Course::findOne($courseId);
        // $srd2 = new SemesterRegistration2Detail();
        // $srd2->setCatalogAttributeValue();
        // getCatalogMinPassGrade();
        return $this->renderPartial('__report/course-failed-students', [
            'students' => SemesterRegistration2Detail::getCourseFailedStudents($courseId, $catalogid),
            'course' => $course
        ]);
    }

    public function actionGetSrd2Detail()
    {
        $semregId = isset($_POST['semregId']) && !empty($_POST['semregId']) ? intval($_POST['semregId']) : die('InvalId request');

        $model = $this->findModel($semregId);

        return $this->renderPartial('__report/semreg-details', ['model' => $model, 'srd2s' => $model->semesterRegistration2Details]);
    }

    /**
     * Lists all SemesterRegistration2 models.
     * @return mixed
     */
    public function actionIndexGradeReport() //index-grade-report
    {
        $searchModel = new SemesterRegistration2Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('/semester-registration/__report/index-grade-report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetReportPrintBacklog()
    { // get-report-print
        $searchModel = new SemesterRegistration2Search();
        $gAssignmentModel = new \backend\models\WGroupAssignment();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 1);

        $pharams = isset($_SESSION['printParams']['SemesterRegistration2Search']) ? $_SESSION['printParams']['SemesterRegistration2Search'] : Yii::$app->request->queryParams;

        $this->layout = 'printLayout';

        return $this->render(
            '/semester-registration/__report/get-report-print-backlog',
            [
                'model' => $dataProvider,
                'printParams' => $pharams,
            ]
        );
    }

    public function actionGetReportPrint()
    { // get-report-print
        $searchModel = new SemesterRegistration2Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 1);

        $pharams = isset($_SESSION['printParams']['SemesterRegistration2Search']) ? $_SESSION['printParams']['SemesterRegistration2Search'] : Yii::$app->request->queryParams;

        $this->layout = 'printLayout';

        return $this->render('/semester-registration/__report/index-grade-report-print', [
            'model' => $dataProvider,
            'printParams' => $pharams
        ]);
    }

    public function actionGetSemesterGradeReport()
    { //get-semester-grade-report
        $semregId = isset($_GET['semregId']) && !empty($_GET['semregId']) ? intval($_GET['semregId']) : die('Invalid Request');

        $this->layout = 'gradeReportLayout';
        $_SESSION['report'] = 'Student Semester Grade Report';

        $semregModel = $this->findModel($semregId);
        $srd2 = $semregModel->getSemesterRegistration2Details()
            ->andWhere([
                'srd2_coadd_flag' => NULL,
                'srd2_semreg_relId' => $semregId,
            ])
            ->one();
        if ($srd2) {
            $gsetObject = $srd2->srd2GroupRel;
        } else {
            $gAssignmentModel = new \backend\models\WGroupAssignment();
            $gsetObject = $gAssignmentModel->getStudentGroupAssignment($semregModel->semregAcadYearRel, $semregModel->semregSemesterRel, $semregModel->semregStdRel)->gasGsetRel;
        }

        // update semester grade averages
        // $semregModel->semregStdRel->calculateGPAs();

        // var_dump($gsetObject->id);exit;

        return $this->render('__report/grade-report', [
            'studentObject' => $semregModel->semregStdRel,
            'gsetObject' => $gsetObject,
            'semregModel' => $semregModel
        ]);
    }

    /**
     * Lists all SemesterRegistration models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SemesterRegistration2Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->redirect('/semester-registration/index-grade-report');
        // return $this->render('index', [
        //     'searchModel' => $searchModel,
        //     'dataProvider' => $dataProvider,
        // ]);
    }

    /**
     * Displays a single SemesterRegistration2 model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new SemesterRegistration2 model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SemesterRegistration2();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SemesterRegistration2 model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
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
     * Deletes an existing SemesterRegistration2 model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {

        $model = $this->findModel($id);

        $m = $model->RemovePossible();

        return $m[0] ? $model->delete() : $m[1];
    }

    /**
     * Finds the SemesterRegistration2 model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SemesterRegistration2 the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SemesterRegistration2::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
