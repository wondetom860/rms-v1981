<?php

namespace frontend\controllers;

use Yii;
use backend\models\AcademicCalander;
use backend\models\AcademicCalanderSearch;
use yii\web\Controller;
use backend\models\SchoolYear;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AcademicCalanderController implements the CRUD actions for AcademicCalander model.
 */
class AcademicCalanderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
       return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
               'class' => \yii\filters\AccessControl::className(),
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::className(),
               ],
               'rules' => [
                   [
                       'actions' => ['index','create', 'update', 'delete'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all AcademicCalander models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('Admin')) {
            throw new NotFoundHttpException('The requested page does not exist.');            
        }
        $this->creatAcadYear();
        $searchModel = new AcademicCalanderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function creatAcadYear(){
        // check and store acadYear if it is not in the database table.

        $acadYear = SchoolYear::findOne(['sch_year_desc' => Yii::$app->params['C']['Y']]);
        if (empty($acadYear)) {
            $model = new SchoolYear();
            $model->sch_year_desc = Yii::$app->params['C']['Y'];
            $model->sch_year_status = 1;
            $model->sch_created_by = 3;
            $model->sch_updated_by = $model->sch_created_by;
            $model->sch_created_at = date("Y-m-d h:i:s");
            $model->sch_updated_at = $model->sch_created_at;

            if (!$model->save()) {
                print_r($model->errors);die();
            }
        }
    }
    /**
     * Displays a single AcademicCalander model.
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
     * Creates a new AcademicCalander model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AcademicCalander();

        if ($model->load(Yii::$app->request->post())) {

            // check if similar duplicate entry exists
            $model->acad_created_by = $model->acad_updated_by = Yii::$app->user->identity->id;
            $model->acad_created_at = $model->acad_updated_at = date('Y-m-d h:i:s');
            $model->acad_status = 1;
            if (strtotime($model->acad_starttime) == strtotime(date('Y-m-d'))) {
                $model->acad_starttime = date('Y-m-d h:i:s');
            }

            $model->acad_finishtime = strtotime($model->acad_finishtime) + (24*60*60)-1;
            $model->acad_finishtime = date('Y-m-d H:i:s',$model->acad_finishtime);

            $model->acad_clslvl_relId = ($model->acad_clslvl_relId == 0)?NULL:$model->acad_clslvl_relId;

            if ($model->save()) {
                // $model->updateSemester();
                return $this->redirect(['index']);
            }else{
                print_r($model->errors);die();
            }
            // 
            
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AcademicCalander model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->acad_updated_by = Yii::$app->user->identity->id;
            $model->acad_updated_at=date('Y-m-d h:i:s');
            $model->acad_finishtime = strtotime($model->acad_finishtime) + (24*60*60)-1;
            $model->acad_finishtime = date('Y-m-d H:i:s',$model->acad_finishtime);

            if ($model->save()) {
                // $model->updateSemester();
                return $this->redirect(['index']);
            }else{
                print_r($model->errors);
                die();
            }
            // 
            
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AcademicCalander model.
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
     * Finds the AcademicCalander model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AcademicCalander the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AcademicCalander::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
