<?php

namespace frontend\controllers;

use Yii;
use backend\models\CourseOffering2;
use backend\models\CourseOffering2Search;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * CourseOffering2Controller implements the CRUD actions for CourseOffering2 model.
 */
class CourseOffering2Controller extends Controller
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
        ];
    }

    /**
     * Lists all CourseOffering2 models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CourseOffering2Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionApproveOffering(){
        $coff2Id = isset($_POST['coff2Id']) && !empty($_POST['coff2Id']) ? intval($_POST['coff2Id']) : die('Invalid request');

        if (Yii::$app->user->can('Registrar')) {
            $model = $this->findModel($coff2Id);

            $model->coff2_lock_status = 1;
            $model->coff2_updated_at = time();
            $model->coff2_updated_by = \Yii::$app->user->id;
            $model->getData();

            if ($model->update(false)) {
                foreach ($model->courseOffering2Details as $coff2det) {
                    if ($coff2det->coff2det_registrarApproved != $model->coff2_lock_status) {
                        $coff2det->coff2det_registrarApproved = $model->coff2_lock_status;
                        $coff2det->coff2det_registrarApproved_at = date('Y-m-d H:i:s',$model->coff2_updated_at);
                        $coff2det->coff2det_registrarApproved_by = $model->coff2_updated_by;
                        if ($coff2det->update(false)) {
                            continue;
                        }else{
                            return json_decode($coff2det->errors());
                        }
                    }
                }
                return 1;
            }else{
                return "Uknown error occured while processing.... please try again later.";
            }
        }
    }

    /**
     * Displays a single CourseOffering2 model.
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
     * Creates a new CourseOffering2 model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (Yii::$app->user->can('DepartmentHead')) {
            $model = new CourseOffering2();

            $instDept = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;
            $model->HoDDept = ArrayHelper::map(\backend\models\Department::find()->where(['id'=>$instDept])->all(), 'id', 'dept_name');

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            
        }else{
            die("<p class='bg-danger'>You can not perform this operation.</p>");
            
        }
    }

    /**
     * Updates an existing CourseOffering2 model.
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
            if (Yii::$app->user->can('DepartmentHead')) {
                $instDept = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;
                $model->HoDDept = ArrayHelper::map(\backend\models\Department::find()->where(['id'=>$instDept])->all(), 'id', 'dept_name');
            }else{
                $model->HoDDept = [];
            }
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CourseOffering2 model.
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
     * Finds the CourseOffering2 model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CourseOffering2 the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CourseOffering2::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
