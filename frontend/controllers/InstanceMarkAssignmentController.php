<?php

namespace frontend\controllers;

use Yii;
use backend\models\InstanceMarkAssignment;
use backend\models\InstanceMarkAssignmentSearch;
use \backend\models\ContAssessCatagory2;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \common\components\CommonControllerFunctions as CCF;

/**
 * InstanceMarkAssignmentController implements the CRUD actions for InstanceMarkAssignment model.
 */
class InstanceMarkAssignmentController extends Controller
{

    public $courseId;
    public $acadYearId;
    public $semId;

    protected function __constructor() {

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
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionChangeMdStatus(){
        $imaList = isset($_POST['imaList']) && !empty($_POST['imaList'])?$_POST['imaList']:die('Assessment Inastnec List empity..., Please try again');
        $option = isset($_POST['option']) && !empty($_POST['option'])?intval($_POST['option']):die('Uknown opertaion, Please try again');

        $option = $option==2?0:$option;

        $option = $option == 3 ? NULL : $option;

        // $imaList = json_decode($imaList);

        $c = count($imaList);

        $result = Yii::$app->db->createCommand()
            ->update('instance_mark_assignment',
                [
                    'ima_approved'=>$option,
                    'ima_approved_at'=>date('Y-m-d h:i:s'),
                    'ima_approved_by'=>Yii::$app->user->identity->id,
                ],
                [
                    'id'=>$imaList,
                ])
            ->execute();

        echo "{$result}";
    }

    /**
     * Lists all InstanceMarkAssignment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InstanceMarkAssignmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new InstanceMarkAssignment();

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('index_ajax', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'model'=>$model,
            ]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$model,
        ]);
    }


    // get assessment catagory
    public function actionGetCourseCatagoryIma(){
        $courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])?intval($_POST['courseId']):die("Uknown Course");
        $coffdet = isset($_POST['coffdet']) && !empty($_POST['coffdet'])?intval($_POST['coffdet']):die("Course Offering detail error.");
        $model = new ContAssessCatagory2();
        $imodel = new InstanceMarkAssignment();
        $catagories = $model->getCourseCatagory($courseId);

        $this->__constructor();
        if (\backend\models\CourseCordinator::isCordinator($courseId,$this->acadYearId,$this->semId)) {
            # code...
        }else{
            echo "<option value=''> Only Course co-ordiantor is privilleged for this task. </option>"; die();
        }
        if ($catagories && count($catagories) > 0) {
            $options = "<option value=''> - </option>";

            foreach ($catagories as $key) {
                $remPoint = $imodel->remainingPoint($key->id,$key->cat_maxPoint); //get remaining point for the assessment catagory.
                if ($remPoint[0] <= 0) {
                    continue;
                }
                $catagoryNames = \backend\models\CatagoryName2::findOne($key->cat_catagory_relId);
                $options .= "<option data-max = {$remPoint[0]} data-slice='{$remPoint[1]}' value='{$key->id}'>{$catagoryNames->catagory_name}</option>";

                if ($catagoryNames->catagory_name == 'Final Exam') {
                    $exists = InstanceMarkAssignment::findOne(['ima_catagorySetup_relId'=>$key->id]);
                    if (!($exists && count($exists) > 0)) {
                        $this->saveIma($key->id,'Final Exam',$key->cat_maxPoint);
                    }
                }
            }
        }else{
            $options = "<option value='' title='No Assessment catagory setup found for this course.'> - </option>";
        }
        echo $options; die();
    }

    private function saveIma($catagoryId,$instanceName,$maxPoint){
        $model = new InstanceMarkAssignment();

        echo "Saving..";
        $model->ima_catagorySetup_relId = $catagoryId;
        $model->ima_instanceName = $instanceName;
        $model->ima_possiblePoint = $maxPoint;

        $model->ima_created_at = $model->ima_updated_at = date('Y-m-d h:i:s');
        $model->ima_created_by = $model->ima_updated_by = Yii::$app->user->identity->id;

        $model->save();
        return;
    }

    /**
     * Displays a single InstanceMarkAssignment model.
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
     * Creates a new InstanceMarkAssignment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new InstanceMarkAssignment();

        if ($model->load(Yii::$app->request->post())) {

            $exists = InstanceMarkAssignment::findOne(['ima_catagorySetup_relId'=>$model->ima_catagorySetup_relId,'ima_possiblePoint'=>$model->ima_possiblePoint,'ima_instanceName'=>$model->ima_instanceName]);
            if ($exists && count($exists) > 0) {
                Yii::$app->session->setFlash('message', "Such Assessment catagory already exists. Please try another..");
            }else{
                $catagory = ContAssessCatagory2::findOne($model->ima_catagorySetup_relId);

                if ($model->ima_possiblePoint > $catagory->cat_maxPoint) {
                    Yii::$app->session->setFlash('message', "Max point for this Instance can NOT be more than {$catagory->cat_maxPoint}.");
                }else{
                    $remPoint = $model->remainingPoint($catagory->id,$catagory->cat_maxPoint); //get remaining point for the assessment catagory.

                    if ($model->ima_possiblePoint > $remPoint[0]) {
                        Yii::$app->session->setFlash('message', "Max point for this Instance can NOT be more than {$remPoint[0]}.");
                    }else{
                        $model->ima_created_at = $model->ima_updated_at = date('Y-m-d h:i:s');
                        $model->ima_created_by = $model->ima_updated_by = Yii::$app->user->identity->id;

                        if ($model->save()) {
                            return $this->redirect(['index']);
                        }else{
                            var_dump($model);die();
                            Yii::$app->session->setFlash('message', "Error while saving record.");
                        }
                    }
                }
            }
        return $this->redirect(['index',['model'=>$model]]);
        } else {
           return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing InstanceMarkAssignment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $catagory = ContAssessCatagory2::findOne($model->ima_catagorySetup_relId);

            if ($model->ima_possiblePoint > $catagory->cat_maxPoint) {
                Yii::$app->session->setFlash('message', "Max point for this Instance can NOT be more than {$catagory->cat_maxPoint}.");
            }else{
                $remPoint = $model->remainingPoint($catagory->id,$catagory->cat_maxPoint); //get remaining point for the assessment catagory.

                if ($model->ima_possiblePoint > $remPoint[0]) {
                    Yii::$app->session->setFlash('message', "Max point for this Instance can NOT be more than {$remPoint[0]}.");
                }else{
                    $model->ima_updated_at = date('Y-m-d h:i:s');
                    $model->ima_updated_by = Yii::$app->user->id;

                    if ($model->save()) {
                        return $this->redirect(['index']);
                    }else{
                        var_dump($model);die();
                        Yii::$app->session->setFlash('message', "Error while saving record.");
                    }
                }
            }
            // return $this->redirect('index');
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing InstanceMarkAssignment model.
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
     * Finds the InstanceMarkAssignment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InstanceMarkAssignment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InstanceMarkAssignment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
