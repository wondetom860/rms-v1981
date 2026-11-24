<?php

namespace frontend\controllers;

use Yii;
use backend\models\SemesterEvaluationScale;
use backend\models\SemesterEvaluationScaleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SemesterEvaluationScaleController implements the CRUD actions for SemesterEvaluationScale model.
 */
class SemesterEvaluationScaleController extends Controller
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
                       'actions' => ['index','create', 'update', 'delete','get-scales'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all SemesterEvaluationScale models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SemesterEvaluationScaleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetScales(){
        $evalTypes = SemesterEvaluationScale::find()->all();

        if ($evalTypes && count($evalTypes) > 0) {
            echo "<table class='table table-striped table-hover table-condensed table-sm table-bordered'>";
            echo "<caption><p class='bg-success'> <i class='bg-success'>". count($evalTypes) ."</i> record(s).</p></caption>";
            echo "<thead>
            <th>#</th>
            <th>Scale Name</th>
            <th>Scale Min.</th>
            <th>Scale Max.</th>
            </thead><tbody>";
            $count = 0;
            foreach ($evalTypes as $evalType) {
                // $student = \backend\models\Student::findOne(['id'=>$std->gas_std_relId]);
                // $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
                echo "<tr>
                <td>". ++$count ."</td>
                <td>". $evalType['ses_name']."</td>
                <td>". sprintf("%0.2f",$evalType['ses_minPoint']) ."</td>
                <td>". sprintf("%0.2f",$evalType['ses_maxPoint']) ."</td>
                </tr>";
            }
            echo "</tbody></table>";
            }else{
                echo "<p class='bg-danger'>No record found...</p>";
            }
    }

    /**
     * Displays a single SemesterEvaluationScale model.
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
     * Creates a new SemesterEvaluationScale model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SemesterEvaluationScale();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->render('create', [
                'model' => new SemesterEvaluationScale(),
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SemesterEvaluationScale model.
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
     * Deletes an existing SemesterEvaluationScale model.
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
     * Finds the SemesterEvaluationScale model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SemesterEvaluationScale the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SemesterEvaluationScale::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
