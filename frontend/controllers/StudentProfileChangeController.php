<?php

namespace frontend\controllers;

use Yii;
use backend\models\StudentProfileChange;
use backend\models\StudentProfileChangeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StudentProfileChangeController implements the CRUD actions for StudentProfileChange model.
 */
class StudentProfileChangeController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Admin', 'Registrar'],
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'approve-request'],
                        'allow' => true,
                        'roles' => [
                            'Registrar'
                        ]
                    ],
                ]
            ]
        ];
    }

    public function actionApproveRequest()
    {
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);

        $response = $model->approveRequest();

        // var_dump($response);exit;

        if(is_bool($response) && $response){
            return $this->redirect(['/student/view','id' => $model->std_relId]);
        }
        return $response;
    }

    /**
     * Lists all StudentProfileChange models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StudentProfileChangeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StudentProfileChange model.
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
     * Creates a new StudentProfileChange model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StudentProfileChange();

        if ($model->load(Yii::$app->request->post())) {

            $model->created_at = time();
            $model->approval_status = NULL;
            $model->created_by = Yii::$app->user->identity->id;
            $model->getData();

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Requested successfully, Changes will be effective after approval by the Registrar');
            } else {
                Yii::$app->session->setFlash('warning', 'Request failed, Error: ' . json_encode($model->errors));
            }
            return $this->redirect(['/student/view', 'id' => $model->std_relId]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing StudentProfileChange model.
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
     * Deletes an existing StudentProfileChange model.
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
     * Finds the StudentProfileChange model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StudentProfileChange the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StudentProfileChange::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
