<?php

namespace frontend\controllers;

use Yii;
use backend\models\AddHistory;
use backend\models\AddHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AddHistoryController implements the CRUD actions for AddHistory model.
 */
class AddHistoryController extends Controller
{
    /**
     * {@inheritdoc}
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
                        'actions' => ['index', 'delete', 'change-status', 'get-courses-for-add', 'get-report-print-backlog'],
                        'allow' => true,
                        'roles' => ['Admin'],
                    ],
                    [
                        'actions' => ['index', 'change-status', 'get-report-print-backlog'],
                        'allow' => true,
                        'roles' => ['Registrar'],
                    ],
                    [
                        'actions' => ['index', 'create', 'delete', 'get-courses-for-add', 'change-status'],
                        'allow' => true,
                        'roles' => ['Student'],
                    ],

                    [
                        'actions' => ['index', 'get-report-print-backlog'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }

    public function actionGetReportPrintBacklog()
    { //get-report-print-backlog
        $stdId = isset($_GET['id']) && !empty($_GET['id']) ? intval($_GET['id']) : die('INVALID REQUEST');

        $this->layout = "printLayout";

        $cofferingModel = new \backend\models\WGroupAssignment();

        $studentObject = \backend\models\Student::findOne($stdId);
        return $this->render('__report/courses-for-add', ['catalog' => $cofferingModel->getCatalogEntries($studentObject), 'studentObject' => $studentObject, 'print' => true]);
    }

    public function actionGetCoursesForAdd($stdId = null)
    {
        $cofferingModel = new \backend\models\WGroupAssignment();
        if ($stdId) {
            $studentObject = \backend\models\Student::findOne(['id' => $stdId]);
        } else {
            if (Yii::$app->user->can('Student')) {
                $studentObject = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
            }
        }
        return $this->renderPartial(
            '__report/courses-for-add',
            [
                'catalog' => $cofferingModel->getCatalogEntries($studentObject),
                'studentObject' => $studentObject
            ]
        );
    }

    /**
     * Lists all AddHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AddHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionChangeStatus()
    {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('Invalid request');
        $option = isset($_POST['option']) && !empty($_POST['option']) ? intval($_POST['option']) : die('Invalid request');

        $model = $this->findModel($id);
        if ($option == 1) { //approve the course add request
            // this action shall be forwarded to 
            // Changerequeststatus function of approveCoaurseAdd controller

        } else if ($option == 3) { //remove the request
            if (Yii::$app->user->can('Admin') || (Yii::$app->user->can('Student') && $model->ahStdRel->std_user_relId == Yii::$app->user->identity->id)) {
                if ($model->ah_approved) {
                    return "You can NOT remove apporoved course add request";
                } else {
                    $srd2 = $model->ahSrd2Rel;
                    if ($srd2) {
                        $srd2->delete();
                    }
                    $model->delete();
                    return 1;
                }
            } else if (Yii::$app->user->can('Registrar')) {
                // In case if removal is needed by the registrar, allow him/her
                $srd2 = $model->ahSrd2Rel;
                if ($srd2) {
                    if ($srd2->srd2GradeRel && $srd2->srd2_gradeApproved) {
                        return "You can NOT remove Course registration Entry with approved course grade.";
                    }
                    $srd2->delete();
                }
                $model->delete();
                return 1;
            }
        }
    }

    /**
     * Displays a single AddHistory model.
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
     * Creates a new AddHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AddHistory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AddHistory model.
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
     * Deletes an existing AddHistory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AddHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AddHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AddHistory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
