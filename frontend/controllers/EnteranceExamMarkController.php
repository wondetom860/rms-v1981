<?php

namespace frontend\controllers;

use Yii;
use backend\models\EnteranceExamMark;
use backend\models\EnteranceExamMarkSearch;
use backend\models\Student;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\ModelEEM;
use yii\web\Response;
use yii\widgets\ActiveForm;


/**
 * EnteranceExamMarkController implements the CRUD actions for EnteranceExamMark model.
 */
class EnteranceExamMarkController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'delete', 'create2'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Admin',],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all EnteranceExamMark models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EnteranceExamMarkSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EnteranceExamMark model.
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
     * Creates a new EnteranceExamMark model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $models = [new EnteranceExamMark()];

        $posts = Yii::$app->request->post();

        $eiId = intval($posts['EnteranceExamMark'][0]['eem_ei_relId']);
        // echo "<pre>";var_dump($eiId);die();


        $models = ModelEEM::createMultiple(EnteranceExamMark::class);
        ModelEEM::loadMultiple($models, Yii::$app->request->post());
        $flag = false;
        // validate all models
        $valid = ModelEEM::validateMultiple($models);

        if ($valid) {
            $transaction = Yii::$app->db->beginTransaction();
            $ids = [];
            try {
                foreach ($models as $post12) {
                    $post12['eem_ei_relId'] = $eiId;
                    // $post12['eem_created_by'] = Yii::$app->user->identity->id;
                    $post12['eem_created_at'] = date('Y-m-d H:i:s');
                    $post12['eem_year'] = intval($post12['eem_year']);
                    if (strtotime($post12['eem_year']) === false) {
                        $transaction->rollBack();
                        $flag = false;
                        die('Invalid year Provided on Enterance Exam tab.');
                    }
                    // $post12['ssa_gradeLevels'] = json_encode($post12['ssa_gradeLevels']);
                    // echo "<pre>";var_dump($post12);die();
                    if (! ($flag = $post12->save())) {
                        $err = json_encode($post12->getErrors());
                        die("{$err}");
                    }
                    $ids[] = $post12->id;
                }

                if ($flag) {
                    echo json_encode([1, $ids]);
                    $transaction->commit();
                    die();
                }
            } catch (\Exception $e) {
                die($e->getMessage());
            }
        } else {
            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validateMultiple($models);
            }
        }
    }

    public function actionCreate2()
    {
        $model = new EnteranceExamMark();

        if ($model->load(Yii::$app->request->post())) {
            // var_dump($model);exit;
            $model->eem_ei_relId = Student::findOne(['id' => $model->std_id])->st_educ_info_relId;
            if ($model->suchExists()) {
                Yii::$app->session->setFlash('warning', 'Such record already exists.');
            } else {
                $model->eem_created_at = date('Y-m-d h:i:s');
                $model->eem_created_by = Yii::$app->user->identity->id;

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Creating successful');
                } else {
                    Yii::$app->session->setFlash('warning', 'Creating failed: ' . json_encode($model->errors));
                }
            }
            return $this->redirect(['/student/view', 'id' => $model->std_id]);
        } else {
            $model->std_id = Yii::$app->request->get('std_id');
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionValidate()
    {
        $models = ModelEEM::createMultiple(EnteranceExamMark::class);
        ModelEEM::loadMultiple($models, Yii::$app->request->post());
        if (Yii::$app->request->isAjax && $models) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validateMultiple($models);
        }
    }

    /**
     * Updates an existing EnteranceExamMark model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->user->can('Admin')) {
            $model = $this->findModel($id);
            $model->ar_request = Yii::$app->request->get('ar_request');
            if ($model->load(Yii::$app->request->post())) {
                if ($model->suchExists($model->id)) {
                    Yii::$app->session->setFlash('warning', 'Such record already registered.');
                } else {
                    $model->eem_updated_at = date('Y-m-d H:i:s');
                    $model->eem_updated_by = Yii::$app->user->identity->id;
                    if ($model->save()) {
                        Yii::$app->session->setFlash('success', 'Update successful');
                    } else {
                        Yii::$app->session->setFlash('warning', 'Update failed: ' . json_encode($model->errors));
                    }
                }
                $modelStd_id = $model->eem_ei_relId;
                $modelStd_id = Student::findOne(['st_educ_info_relId' => $modelStd_id])->id;
                return $model->ar_request ?
                    $this->redirect(['/student/view', 'id' => $modelStd_id]) :
                    $this->redirect(['index']);
            } else {
                return $model->ar_request ? $this->renderAjax('update', [
                    'model' => $model,
                ]) : $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Deletes an existing EnteranceExamMark model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (Yii::$app->user->can('Admin')) {
            $this->findModel($id)->delete();

            return $this->redirect(['index']);
        } else {
            throw new NotFoundHttpException('Invald Access.');
        }
    }

    /**
     * Finds the EnteranceExamMark model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EnteranceExamMark the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EnteranceExamMark::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
