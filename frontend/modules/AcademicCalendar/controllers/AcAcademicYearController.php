<?php

namespace frontend\modules\AcademicCalendar\controllers;

use Yii;
use frontend\modules\AcademicCalendar\models\AcAcademicYear;
use frontend\modules\AcademicCalendar\models\AcAcademicYearSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AcAcademicYearController implements the CRUD actions for AcAcademicYear model.
 */
class AcAcademicYearController extends Controller
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
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['Admin','Registrar'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all AcAcademicYear models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AcAcademicYearSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGraduateStudentList()
    {
        $acyId = Yii::$app->request->get('acyId');

        $model = $this->findModel($acyId);

        $result = $model->getGraduateStudentList();
        $this->layout = '@frontend/views/layouts/printLayout';
        if ($result) {
            return $this->render('__report/graduate-student-list', ['list' => $result]);
        }

        return;
    }

    /**
     * Displays a single AcAcademicYear model.
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
     * Creates a new AcAcademicYear model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AcAcademicYear();

        if ($model->load(Yii::$app->request->post())) {

            // set pharameters:
            $model->start_date = strtotime($model->start_date);
            $model->end_date = strtotime($model->end_date) + (24 * 60 * 60) - 1;

            $model->created_by = Yii::$app->user->identity->id;
            $model->created_at = time();

            if ($model->start_date > $model->end_date) {
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'Invalid Date_Interval');

                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            if ($model->checIfExists()) {
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'Such entry already exists');

                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $model->getData();

            if ($model->save(false)) {
                unset($model);
                return $this->redirect('index');
            } else {

                // create failed:
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'record Save failed, ' . json_encode($model->getErrors()));
            }

            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AcAcademicYear model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->start_date = strtotime($model->start_date);
            $model->end_date = strtotime($model->end_date) + (24 * 60 * 60) - 1; //should be active on end date day

            $model->updated_by = Yii::$app->user->identity->id;
            $model->updated_at = time();

            if ($model->start_date > $model->end_date) {
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'Invalid Date_Interval');

                return $this->render('update', [
                    'model' => $model,
                ]);
            }

            if ($model->checIfExists($model->id)) {
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'Such entry already exists');

                return $this->render('update', [
                    'model' => $model,
                ]);
            }

            $model->getData();

            if ($model->save(false)) {
                unset($model);
                return $this->redirect('index');
            } else {

                // create failed:
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'record Save failed, ' . json_encode($model->getErrors()));
            }
            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AcAcademicYear model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $activePrograms = $model->getAcActivePrograms()->andWhere(' program_schedule_used <> 0 ')->all();

        if ($activePrograms && count($activePrograms) > 0) {
            Yii::$app->session->setFlash('type', 'error');
            Yii::$app->session->setFlash('message', 'Entry can NOT be deleted, Programs scheduled under this academic year are referenced by other records.');
        } else {
            $model->delete();
            Yii::$app->session->setFlash('type', 'success');
            Yii::$app->session->setFlash('message', 'Record deleted. ');
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the AcAcademicYear model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AcAcademicYear the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AcAcademicYear::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
