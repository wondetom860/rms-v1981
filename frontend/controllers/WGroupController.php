<?php

namespace frontend\controllers;

use Yii;
use backend\models\WGroup;
use backend\models\WGroupSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WGroupController implements the CRUD actions for WGroup model.
 */
class WGroupController extends Controller
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
     * Lists all WGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $model = new WGroup();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$model,
        ]);
    }

    /**
     * Displays a single WGroup model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    // returns the list of groups available

    public function actionListsgroup(){
        $grps = WGroup::find()->all();
        if (!empty($grps)) {
            echo "<table class='table table-sm table-striped table-condensed table-bordered'><caption>";
           // $dp = !empty($_POST['deptId'])?(' in '. \backend\models\Department::findOne(['id'=>$_POST['deptId']])->dept_name.'.'):('.') ;
            // "<i>" .count($specs)."</i> Specializations found ". $dp .".</caption>
           echo "<thead>
                    <th>#</th>
                    <th>Group Name</th>    
                    <th>Decription</th>
                    <th>Option</th>                  
                </thead><tbody>";
                $count = 0;
                foreach ($grps as $grp) {
                    echo "<tr>
                        <td>". ++$count ."</td>
                        <td>". $grp['group_name'] ."</td>
                        <td>". $grp['remark'] ."</td>
                        <td>Delete</td>
                    </tr>";
                }
                echo "</tbody></table>";
        }else{
            echo "<p class='bg-danger'>No group registered yet.</p>";
        }
    }
    /**
     * Creates a new WGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WGroup();
        if (isset($_POST['gname']) && !empty($_POST['gname'])) {
            $gname = $_POST['gname'];

            $gnms = WGroup::findOne(['group_name'=>$gname]);
            if (empty($gnms)) {
                $model->group_name = $gname;
                $model->remark  = $_POST['remark'];
                if ($model->save()) {
                    echo 1;
                }else{
                    print_r($model->errors);
                }
            }else{
                echo 'Such group Name already registered... (::)Lash Bey(::)';
            }
            die();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing WGroup model.
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
     * Finds the WGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
