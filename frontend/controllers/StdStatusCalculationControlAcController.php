<?php

namespace frontend\controllers;

use Yii;
use backend\models\StdStatusCalculationControlAc;
use backend\models\StdStatusCalculationControlAcSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StdStatusCalculationControlAcController implements the CRUD actions for StdStatusCalculationControlAc model.
 */
class StdStatusCalculationControlAcController extends Controller
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
                        'roles' => ['Admin',],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all StdStatusCalculationControlAc models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StdStatusCalculationControlAcSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StdStatusCalculationControlAc model.
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
     * Creates a new StdStatusCalculationControlAc model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StdStatusCalculationControlAc();

        if ($model->load(Yii::$app->request->post())) {
            $model->sssccAC_created_by = $model->sssccAC_updated_by = Yii::$app->user->identity->id;
            $model->sssccAC_created_at =  $model->sssccAC_updated_at = date('Y-m-d h:i:s');

            if ($model->sssccAC_min_CGPA < 0.00 || $model->sssccAC_max_CGPA < 0.00 || $model->sssccAC_min_SGPA < 0.00 || $model->sssccAC_max_SGPA < 0.00 || $model->sssccAC_min_CGPA > 4.00 || $model->sssccAC_max_CGPA > 4.00 || $model->sssccAC_min_SGPA > 4.00 || $model->sssccAC_max_SGPA > 4.00) {
                die('Invalid Entry');
            }

            if ($model->save()) {
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('warning', 'Error occured: ' . json_encode($model->errors));
                // var_dump($model->errors);die();
            }
            // $model->save()
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing StdStatusCalculationControlAc model.
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
     * Deletes an existing StdStatusCalculationControlAc model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the StdStatusCalculationControlAc model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StdStatusCalculationControlAc the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StdStatusCalculationControlAc::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
