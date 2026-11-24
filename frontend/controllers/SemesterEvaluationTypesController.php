<?php

namespace frontend\controllers;

use Yii;
use backend\models\SemesterEvaluationTypes;
use backend\models\SemesterEvaluationTypesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SemesterEvaluationTypesController implements the CRUD actions for SemesterEvaluationTypes model.
 */
class SemesterEvaluationTypesController extends Controller
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
                       'actions' => ['index','create', 'update', 'delete','get-types'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin'],
                   ],
               ]
           ]
        ];
    }

    public function actionGetTypes(){
        $evalTypes = SemesterEvaluationTypes::find()->all();

        if ($evalTypes && count($evalTypes) > 0) {
            echo "<table class='table table-striped table-hover table-condensed table-sm table-bordered'>";
            echo "<caption><p class='bg-success'> <i class='bg-success'>". count($evalTypes) ."</i> record(s).</p></caption>";
            echo "<thead>
            <th>#</th>
            <th>Evaluation Type</th>
            <th>Given Level</th>
            <th>Max. pt.</th>
            </thead><tbody>";
            $count = 0;
            foreach ($evalTypes as $evalType) {
                // $student = \backend\models\Student::findOne(['id'=>$std->gas_std_relId]);
                // $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
                echo "<tr>
                <td>". ++$count ."</td>
                <td>". $evalType['semev_type_desc']."</td>
                <td>". $evalType['semev_type_level'] ."</td>
                <td>". $evalType['semev_type_maxPoint'] ."</td>
                </tr>";
            }
            echo "</tbody></table>";
            }
    }

    /**
     * Lists all SemesterEvaluationTypes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SemesterEvaluationTypesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SemesterEvaluationTypes model.
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
     * Creates a new SemesterEvaluationTypes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SemesterEvaluationTypes();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('type','success');
            \Yii::$app->session->setFlash('message','Record saved successful.');
            return $this->redirect('index');
        } else {
            \Yii::$app->session->setFlash('type','error');
            \Yii::$app->session->setFlash('message','Error: '.json_encode($model->getErrors()));
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SemesterEvaluationTypes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('type','success');
            \Yii::$app->session->setFlash('message','Update successful.');
            return $this->redirect('index');
        } else {
            \Yii::$app->session->setFlash('type','error');
            \Yii::$app->session->setFlash('message','Error: '.json_encode($model->getErrors()));
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SemesterEvaluationTypes model.
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
     * Finds the SemesterEvaluationTypes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SemesterEvaluationTypes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SemesterEvaluationTypes::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
