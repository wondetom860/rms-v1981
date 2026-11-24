<?php

namespace frontend\controllers;

use Yii;
use backend\models\ScholarshipType;
use backend\models\ScholarshipTypeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ScholarshipTypeController implements the CRUD actions for ScholarshipType model.
 */
class ScholarshipTypeController extends Controller
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
                       'actions' => ['index','create', 'update', 'delete','getschtypes'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
               ]
           ]
        ];
    }

    public function actionGetschtypes(){
        $model = ScholarshipType::find()->all();
        if ($model && count($model) > 0) {
           
            echo "<table  class='table table-striped table-condensed table-xs table-bordered'>";
            echo "<caption><p class='bg-success'> <i class='bg-danger'>". count($model) ."</i> Sponsorship(s).</p></caption>";
            echo "<thead>
            <th>#</th>
            <th>Name</th>
            <th>Payment</th>
            <th>Decription</th>
            </thead><tbody>";
            $cont = 0;
            foreach ($model as $m) {
                $p = $m['schType_pay'] == 1?'FEE':'FREE';
                // $student = \backend\models\Student::findOne(['id'=>$reports->ssph_std_relId]);
                // $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);

                echo "<tr>
                <td>". ++$cont ."</td>
                <td>". $m['schType_name']."</td>
                <td>". $p ."</td>
                <td>". $m['schType_desc']."</td>
                </tr>";
            }
            echo "</tbody></table>";
            }else{
                echo "<p class='bg-danger'>No sponsor type registered yet.</p>";
            }
            die();
    }

    /**
     * Lists all ScholarshipType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ScholarshipTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ScholarshipType model.
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
     * Creates a new ScholarshipType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ScholarshipType();

        if ($model->load(Yii::$app->request->post())) {

            $model->schType_created_at = $model->schType_updated_at = date('Y-m-d h:i:s');
            $model->schType_created_by = $model->schType_updated_by = Yii::$app->user->identity->id;
            if ($model->save()) {
                 return $this->redirect(['index']);
            }else{
                print_r($model->errors);die();
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ScholarshipType model.
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
     * Deletes an existing ScholarshipType model.
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
     * Finds the ScholarshipType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ScholarshipType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ScholarshipType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
