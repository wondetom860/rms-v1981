<?php

namespace frontend\controllers;

use Yii;
use backend\models\SemesterEvaluation2;
use backend\models\SemesterEvaluation2Search;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \common\components\CommonControllerFunctions as CCF;
use \backend\models\SemesterEvaluationReportController;

/**
 * SemesterEvaluation2Controller implements the CRUD actions for SemesterEvaluation2 model.
 */
class SemesterEvaluationProgressController extends Controller
{

    public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
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

    // Returns the list of evaluators for the evalaution
    public function actionGetList(){
        $se2Id = isset($_POST['id']) && !empty($_POST['id'])?intval($_POST['id']):die('Invalid Entry');

        $statusTable = '';
        // $this->__constructor();
        $model = SemesterEvaluation2::findOne($se2Id);
        $result = SemesterEvaluationReportController::getReport($se2Id);

        // var_dump($result);die();

        $totalEvaluators = $result[1];
        $evaluatedEvaluators = $result[0];


        $evaluatedEvaluatorsIdArray = [];
        if (count($evaluatedEvaluators) > 0) {
            foreach ($evaluatedEvaluators as $key) {
                array_push($evaluatedEvaluatorsIdArray, $key->id);
            }
        }

        // var_dump($totalEvaluators);die();

        $statusTable .= "<div class='container-fluid'>
            <div class='row'>
                <div class='col-md-8'>";

        if ($totalEvaluators && count($totalEvaluators) > 0) {
            $statusTable .= "<table class='table table-striped table-condensed table-sm table-bordered table-hover' style='font-size:10pt; text-align:left;'>";
            $statusTable .= "<caption><h3 class='pull-left'>{$model->semevEvalTypeRel->semev_type_desc}</h3><br><small class='pull-right'>Total Evaluators</small></caption>";
            $statusTable .= "<thead style='background-color:#ace;'>
                <th>#</th>
                <th>Name</th>
                <th>Status</th>
                </thead><tbody>";
                $count =0;
            foreach ($totalEvaluators as $sed) {
                if (is_null($sed)) {
                    continue;
                }
                $bg = 'warning';
                $evStatus = 'Not Yet.';
                if (in_array($sed->id, $evaluatedEvaluatorsIdArray)) {
                    $bg = 'success';
                    $evStatus = 'Completed.';
                }
                $statusTable .= "<tr>
                    <td>".++$count."</td>
                    <td>{$sed->first_name} {$sed->middle_name}</td>
                    <td  class='$bg'>{$evStatus}</td>
                </tr>";
            }
            $statusTable .= "</tbody></table>";

            //echo $statusTable;
        }else{
            echo "<p class='bg-danger'>No evaluee found.</p>";
        }
        $statusTable .= "</div>
                    <div class='col-md-1'>  </div>
                    <div class='col-md-5'>
        ";
        $statusTable .= "</div>
                    </div>
                </div>
        ";
        return $statusTable;
    }
    /**
     * Lists all SemesterEvaluation2 models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SemesterEvaluation2Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SemesterEvaluation2 model.
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
     * Creates a new SemesterEvaluation2 model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SemesterEvaluation2();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SemesterEvaluation2 model.
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
     * Deletes an existing SemesterEvaluation2 model.
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
     * Finds the SemesterEvaluation2 model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SemesterEvaluation2 the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SemesterEvaluation2::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
