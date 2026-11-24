<?php

namespace frontend\controllers;

use Yii;
use backend\models\ClassLevel;
use backend\models\ClassLevelSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ClassLevelController implements the CRUD actions for ClassLevel model.
 */
class ClassLevelController extends Controller
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
                       'actions' => ['index','create', 'update', 'delete','classyearlists'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all ClassLevel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClassLevelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new ClassLevel();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$model,
            

        ]);
    }

    /**
     * Displays a single ClassLevel model.
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
     * Creates a new ClassLevel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ClassLevel();

        if ($model->load(Yii::$app->request->post())) {

            $model->clslvl_created_at = date('Y-m-d');
            $model->clslvl_created_by = Yii::$app->user->identity->id;
            $model->clslvl_updated_at = $model->clslvl_created_at;
            $model->clslvl_updated_by = $model->clslvl_created_by;
            
            if($model->save()){
                return $this->redirect(['index']);
            }else{
                print_r($model->errors);die();
            }
            // clslvl_created_at
            // $model->save()
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    // returns the list of class year levels dependong on the program selested.

    public function actionClassyearlists(){
        if (isset($_POST['progId']) && !empty($_POST['progId'])) {
            $progId = Yii::$app->request->post('progId');
            //'spec_dep_relId' => $deptId, 

            $posts = \backend\models\ClassLevel::find()
                    ->where(['clslvl_program_relId'=>$progId])
                    ->orderBy('id ASC')
                    ->all();
                    
            if (!empty($posts)) {
                foreach($posts as $post) {
                    echo "<option value='".$post->id."'>".$post->clslvl_name."</option>";
                }
            } else {
                echo "<option> - </option>";
            }
        }
    }

    /**
     * Updates an existing ClassLevel model.
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
     * Deletes an existing ClassLevel model.
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
     * Finds the ClassLevel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ClassLevel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClassLevel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
