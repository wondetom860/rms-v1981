<?php

namespace frontend\controllers;

use Yii;
use backend\models\SemesterEvaluationPoints;
use backend\models\SemesterEvaluationPointsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SemesterEvaluationPointsController implements the CRUD actions for SemesterEvaluationPoints model.
 */
class SemesterEvaluationPointsController extends Controller
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
                       'actions' => ['index','create', 'update', 'delete','get-points'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all SemesterEvaluationPoints models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SemesterEvaluationPointsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SemesterEvaluationPoints model.
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
     * Creates a new SemesterEvaluationPoints model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
   
     public function actionCreate()
    {
        $model = new SemesterEvaluationPoints();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    // return the list of registerd evalPoints for View purpose.
    public function actionGetPoints(){

        $evalPoints = SemesterEvaluationPoints::find();
        $under = NULL;
        if (isset($_POST['evalType']) && !empty($_POST['evalType'])) {
            $evalType = \backend\models\SemesterEvaluationTypes::findOne(['id'=>$_POST['evalType']]);
            if ( $evalType) {
                $evalPoints =  $evalPoints->where(['semevPoint_semev_relId'=>$evalType->id]);
                $under = ' Under '  .$evalType->semev_type_desc; 
            }
        }

        $rep = "";

        $evalPoints =  $evalPoints->all();
        if ($evalPoints) {
            $rep .= "<table class='table table-striped table-condensed table-sm table-bordered'>";
            $rep .= "<caption><p class='bg-success'> <i class='bg-danger'>". count($evalPoints) ."</i> Evaluation points <u>". $under."</u>.</p></caption>";
            $rep .= "<thead style='background-color: #69E;'>
                <th>#</th>
                <th>Eval.Lvl</th>
                <th>Eval. Point</th>
                </thead><tbody>";
            $count = 0;
            foreach ($evalPoints as $evalPoint) {
                $evalType = \backend\models\SemesterEvaluationTypes::findOne(['id'=>$evalPoint['semevPoint_semev_relId']]);
                $rep .= "<tr>
                    <td>". ++$count ."</td>
                    <td title='{$evalType->semev_type_desc}'>{$evalType->semev_type_level}</td>
                    <td>{$evalPoint->sem_eval_point_desc}</td>
                </tr>";
            }
        $rep .= "</tbody></table>";
       }else{
            $rep .= "<p class='bg-danger'>No Evaluation point found under this Evaluation type.</p>";
       }

       return $rep;
    }

    /**
     * Updates an existing SemesterEvaluationPoints model.
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
     * Deletes an existing SemesterEvaluationPoints model.
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
     * Finds the SemesterEvaluationPoints model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SemesterEvaluationPoints the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SemesterEvaluationPoints::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
