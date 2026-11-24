<?php

namespace frontend\controllers;

use Yii;
use backend\models\FacultyCourseAssignment;
use backend\models\FacultyCourseAssignmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FacultyCourseAssignmentController implements the CRUD actions for FacultyCourseAssignment model.
 */
class FacultyCourseAssignmentController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'delete', 'view', 'generate-evaluation-result-v2'],
                        'allow' => true,
                        'roles' => ['DepartmentHead'],
                    ],
                    [
                        'actions' => ['index', 'view', 'generate-evaluation-result-v2','generate-evaluation-result-v3'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }

     // $this->layout = 'evaluationReportPrintLayout';
    public function actionGenerateEvaluationResultV3()
    { //actionGenerateEvaluationResultV2
        $facuId = Yii::$app->request->get('facuid');
        $model = $this->findModel($facuId);
        // $_SESSION['report'] = "AcStEv_" . str_replace(' ', '_', $model->instRel->getFullName() .'_'. $model->coff2detRel->coff2detCourseRel->getCourseCredits());
        $this->layout = 'evaluationReportPrintLayout';
        return $this->render('__report/evaluation-report-print_v3', ['model' => $model]);
    }

    // $this->layout = 'evaluationReportPrintLayout';
    public function actionGenerateEvaluationResultV2()
    { //actionGenerateEvaluationResultV2
        $facuId = Yii::$app->request->get('facuid');
        $model = $this->findModel($facuId);
        // $this->title = "AcStEv_" . str_replace(' ', '_', $model->instRel->getFullName() .'_'. $model->coff2detRel->coff2detCourseRel->getCourseCredits());
        $this->layout = 'evaluationReportPrintLayout';
        return $this->render('__report/evaluation-report-print_v2', ['model' => $model]);
    }

    /**
     * Lists all FacultyCourseAssignment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FacultyCourseAssignmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FacultyCourseAssignment model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
        if ($model->coff2detRel->coff2detCourseRel->course_dept_relId != $inst->inst_dep_relId) {
            Yii::$app->session->setFlash('type', 'error');
            Yii::$app->session->setFlash('message', 'Record does not belong to your department.');
            return $this->redirect(['index',]);
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new FacultyCourseAssignment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($coff2detId)
    {
        $model = new FacultyCourseAssignment();
        $model->coff2det_relId = $coff2detId;
        // restrict out of department manipulations: 
        $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
        if ($model->coff2detRel->coff2detCourseRel->course_dept_relId != $inst->inst_dep_relId) {
            Yii::$app->session->setFlash('type', 'error');
            Yii::$app->session->setFlash('message', 'You can not modify records out of your department: ');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        if ($model->load(Yii::$app->request->post())) {

            $model->created_by = Yii::$app->user->id;
            $model->category = 0; //this entry is modifiable later:
            $model->created_at = time();
            $model->getData();

            if ($model->save()) {
                Yii::$app->session->setFlash('type', 'success');
                Yii::$app->session->setFlash('message', 'Staff adding successful');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'Please try again, Error: ' . json_encode($model->getErrors()));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing FacultyCourseAssignment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        // if related academic year is closed, it is forbidden to update the record.
        if ($model->coff2detRel->coff2detAcProgRel->status != $model->coff2detRel->coff2detAcProgRel::STATUS_ACTIVE) {
            Yii::$app->session->setFlash('type', 'error');
            Yii::$app->session->setFlash('message', 'Related academic Year is not active.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        if ($model->category == 1) {
            Yii::$app->session->setFlash('type', 'error');
            Yii::$app->session->setFlash('message', 'You cannot modify this entry here.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->id;
            $model->getData();

            if ($model->save()) {
                Yii::$app->session->setFlash('type', 'success');
                Yii::$app->session->setFlash('message', 'Staff update successful');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing FacultyCourseAssignment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
        if (($model->coff2detRel->coff2detAcProgRel->status == $model->coff2detRel->coff2detAcProgRel::STATUS_ACTIVE) && $model->category != 1 && ($model->coff2detRel->coff2detCourseRel->course_dept_relId == $inst->inst_dep_relId)) {
            $model->delete();
            Yii::$app->session->setFlash('type', 'success');
            Yii::$app->session->setFlash('message', 'record deleting successful');
            return 1;
        } else {
            Yii::$app->session->setFlash('type', 'error');
            Yii::$app->session->setFlash('message', 'Operation refused');
            return $this->redirect(['index']);
        }


        return $this->redirect(['index']);
    }

    /**
     * Finds the FacultyCourseAssignment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FacultyCourseAssignment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FacultyCourseAssignment::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
