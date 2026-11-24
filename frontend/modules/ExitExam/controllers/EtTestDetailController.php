<?php

namespace frontend\modules\ExitExam\controllers;

use Yii;
use frontend\modules\ExitExam\models\EtTestDetail;
use frontend\modules\ExitExam\models\EtTestDetailSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EtTestDetailController implements the CRUD actions for EtTestDetail model.
 */
class EtTestDetailController extends Controller
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
                        'actions' => ['update', 'delete', 'submit-mark'],
                        'allow' => true,
                        'roles' => ['Admin'],
                    ],
                    [
                        'actions' => ['update', 'delete', 'submit-mark', 'change-approval-status'],
                        'allow' => true,
                        'roles' => ['Registrar'],
                    ]
                ]
            ]
        ];
    }

    public function actionChangeApprovalStatus()
    {
        $id = Yii::$app->request->post('id');
        $model = EtTestDetail::findOne($id);
        if ($model) {
            $model->grade_accepted = $model->grade_accepted === 1 ? 0 : 1;
            $model->pass_status = $model->determinePassStatus();
            $model->grade_accepted_at = time();
            $model->grade_accepted_by = Yii::$app->user->id;
            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->id;
            $model->getData();
            if ($model->save()) {
                return json_encode(['status' => 'success']);
            } else {
                return json_encode(['status' => 'error', 'message' => 'Failed to change the approval status. Error: ' . json_encode($model->getErrors())]);
            }
        } else {
            return json_encode(['status' => 'error', 'message' => 'Model not found.']);
        }
    }

    public function actionSubmitMark()
    {
        $id = Yii::$app->request->post('id');
        $model = EtTestDetail::findOne($id);
        if ($model && $model->grade_accepted != 1) {
            $model->mark_obtained = Yii::$app->request->post('mark');
            $model->pass_status = $model->determinePassStatus();
            $model->date_submitted = time();
            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->id;
            $model->getData();
            if ($model->save()) {
                return json_encode(['status' => 'success']);
            } else {
                return json_encode(['status' => 'error', 'message' => 'Failed to save the mark.']);
            }
        } else {
            return json_encode(['status' => 'error', 'message' => 'Model not found.']);
        }
    }

    /**
     * Lists all EtTestDetail models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EtTestDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EtTestDetail model.
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
     * Creates a new EtTestDetail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EtTestDetail();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EtTestDetail model.
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
     * Deletes an existing EtTestDetail model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $m_id = $model->test_relId;
        $model->testRel->remaining_trails += 1;
        $model->testRel->save();
        if ($model->mark_obtained != null) {
            Yii::$app->session->setFlash('error', 'You cannot delete this attempt because it has already been submitted.');
        }else{
            $model->delete();
            Yii::$app->session->setFlash('success', 'Attempt deleted successfully.');
        }

        return $this->redirect(['/ExitExam/et-elligiblity-test/view', 'id' => $m_id]);
    }

    /**
     * Finds the EtTestDetail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EtTestDetail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EtTestDetail::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}