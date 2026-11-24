<?php

namespace frontend\controllers;

use Yii;
use backend\models\ReRegistration;
use backend\models\ReRegistrationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReRegistrationController implements the CRUD actions for ReRegistration model.
 */
class ReRegistrationController extends Controller
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
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Admin', 'Registrar'],
                    ],
                    [
                        'actions' => ['index', 'view', 'change-apporve-status'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['DepartmentHead',],
                    ],
                ]
            ]
        ];
    }

    public function actionRequestFormPrint()
    {
        $req_id = Yii::$app->request->get('req_id');
        $this->layout = "printLayout";
        $model = $this->findModel($req_id);
        return $this->render('view', ['model' => $model, 'print' => 1]);
    }

    public function actionSetNewOffering()
    {
        $offering_detail_id = Yii::$app->request->post('offering_detail_id');
        $req_id = Yii::$app->request->post('req_id');

        $model = $this->findModel($req_id);

        $resp = $model->setCoff2det($offering_detail_id);

        if ($resp == 1) {
            return $this->redirect(['view', 'id' => $req_id]);
        }

        return $resp;
    }

    public function actionGetOfferings()
    {
        $req_id = Yii::$app->request->post('req_id');
        $model = $this->findModel($req_id);

        return $this->renderPartial('__report/course-offerings', ['offerings' => $model->getOfferings(), 'req' => $model]);
    }

    public function actionChangeApporveStatus()
    {
        $req_id = Yii::$app->request->post('req_id');
        $option = Yii::$app->request->post('option');

        $option = $option == 2 ? 0 : $option;

        $model = $this->findModel($req_id);

        return $model->changeApproveStatus($option);
    }

    /**
     * Lists all ReRegistration models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReRegistrationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReRegistration model.
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
     * Creates a new ReRegistration model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ReRegistration();
        $srd2_id = Yii::$app->request->post('srd2_id');
        $student_id = Yii::$app->request->post('student_id');
        $model->std_relId = $student_id;
        $resp = $model->createRequest($srd2_id);

        if (is_integer($resp)) {
            return $this->redirect(['view', 'id' => $resp]);
        }

        return $resp;

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->id]);
        // }

        // return $this->render('create', [
        //     'model' => $model,
        // ]);
    }

    /**
     * Updates an existing ReRegistration model.
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
     * Deletes an existing ReRegistration model.
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
     * Finds the ReRegistration model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReRegistration the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReRegistration::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
