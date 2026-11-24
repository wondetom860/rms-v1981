<?php

namespace frontend\modules\ExitExam\controllers;

use Yii;
use frontend\modules\ExitExam\models\EtElligiblityTest;
use frontend\modules\ExitExam\models\EtElligiblityTestSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EtElligiblityTestController implements the CRUD actions for EtElligiblityTest model.
 */
class EtElligiblityTestController extends Controller
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
                        'actions' => ['create', 'view', 'index'],
                        'allow' => true,
                        'roles' => ['Student'],
                    ],
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['Admin', 'Registrar'],
                    ],
                    [
                        'actions' => ['index', 'view', 'unapproved-requests'],
                        'allow' => true,
                        'roles' => ['Registrar'],
                    ]
                ]
            ]
        ];
    }

    public function actionUnapprovedRequests(){
        $model = new EtElligiblityTest();
        $model = $model->unapprovedRequests();
        $this->layout = "@frontend/views/layouts/certPrintLayout";
        return $this->render('__reports/unapproved-requests', [
            'model' => $model,
        ]);
    }

    /**
     * Lists all EtElligiblityTest models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EtElligiblityTestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EtElligiblityTest model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->user->can('Student') && $model->semregRel->semregStdRel->std_user_relId != Yii::$app->user->id) {
            throw new ForbiddenHttpException(Yii::t('rms', 'You are not allowed to view this page.'));
        } else {
            return $this->render('view', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new EtElligiblityTest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EtElligiblityTest();
        $model->reason = Yii::$app->request->post('EtElligiblityTest')['reason'];
        $setting_id = Yii::$app->request->post('setting_id', null);
        $student_id = Yii::$app->request->post('student_id', null);
        if ($setting_id) {
            $model->setting_relId = $setting_id;
        }
        if ($model->reason == 1) {
            // requesting for exit exam
            $message = $model->prcessExitExamRequest($student_id);
            if ($message['status'] == 1) {
                Yii::$app->session->setFlash('success', $message['message']);
                if (isset($message['model'])) {
                    return $this->redirect(['view', 'id' => $message['model']]);
                }
            } else {
                Yii::$app->session->setFlash('error', $message['message']);
            }
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EtElligiblityTest model.
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
     * Deletes an existing EtElligiblityTest model.
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
     * Finds the EtElligiblityTest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EtElligiblityTest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EtElligiblityTest::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
