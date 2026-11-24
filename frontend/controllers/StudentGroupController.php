<?php

namespace frontend\controllers;

use Yii;
use backend\models\StudentGroup;
use backend\models\StudentGroupSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StudentGroupController implements the CRUD actions for StudentGroup model.
 */
class StudentGroupController extends Controller
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
        ];
    }

    // returns back the list of student groups created before.

    public function actionGrouplists(){
        $grps=StudentGroup::find()->all();
        if (empty($grps)) {
            echo "<p class='bg-info'>N0 group created yet. Please add one...</p>";
        }else{
            $count = 0;
            foreach ($grps as $grp) {
                echo ++$count." => ".$grp->group_name.'<br>';
            }
        }
    }
    /**
     * Lists all StudentGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StudentGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StudentGroup model.
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
     * Creates a new StudentGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StudentGroup();

        if (isset($_POST['gname']) && !empty($_POST['gname'])) {
            $gname = $_POST['gname'];

            $gnms = StudentGroup::findOne(['group_name'=>$gname]);
            if (empty($gnms)) {
                $model->group_name = $gname;
                $model->group_created_by = Yii::$app->user->identity->id;
                $model->group_created_at = date('Y-m-d h:i:s');
                $model->group_updated_by = $model->group_created_by;
                $model->group_updated_at = $model->group_created_at;
                if ($model->save()) {
                    echo 1;
                }else{
                    print_r($model_errors);
                }
            }else{
                echo 'Such group Name already registered... (::)Lash Bey(::)';
            }
            die();
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing StudentGroup model.
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
     * Deletes an existing StudentGroup model.
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
     * Finds the StudentGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StudentGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StudentGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
