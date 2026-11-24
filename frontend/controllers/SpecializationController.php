<?php

namespace frontend\controllers;
use yii;
use backend\models\Specialization;
use backend\models\SpecializationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class SpecializationController extends \yii\web\Controller
{

	
    // Returns a list of specs for course preq.
    public function actionGetspecs(){
        if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
            $deptId  =$_POST['deptId'];
            $specs = \backend\models\Specialization::find()->where(['spec_dep_relId'=>$deptId]);
            if (isset($_POST['admId']) && !empty($_POST['admId'])) {
                $specs = $specs->andWhere(['spec_prog_relId'=>$_POST['admId']]);
            }
            $specs = $specs->all();

            if ($specs) {
               foreach ($specs as $spec) {
                   echo "<option value='". $spec['id'] ."'>". $spec['spec_name'] ."</option>";
               }
               echo "<option value='0' title='Select this if the course is preq. for all sepcs in the dept.'>All</option>";
            }
            echo "<option value='0' title='Select this if the course is preq. for all sepcs in the dept.'>-</option>";
        }else{
            echo "Uknown department.";
        }
        die(); 
    }

    public function actionListspecs2(){
        //echo ;
        $specs = \backend\models\Specialization::find();
        if (isset($_POST['admId'])) {
            if (isset($_POST['progId'])) {
                if (isset($_POST['deptId'])) {
                    $specs->andFilterWhere([
                        'spec_dep_relId'=>$_POST['deptId'],
                        'spec_prog_relId'=>$_POST['progId'],
                        'spec_adm_relId'=>$_POST['admId'],
                    ]);
                }else{
                    $specs->andFilterWhere([
                        'spec_prog_relId'=>$_POST['progId'],
                        'spec_adm_relId'=>$_POST['admId'],
                    ]);
                }
            }else{
                $specs->andFilterWhere([
                        'spec_adm_relId'=>$_POST['admId'],
                    ]);
            }
        }
        $specs = $specs->all();
        if ($specs) {
           echo "<table class='table table-sm table-striped table-condensed table-bordered'><caption>";
           $dp = !empty($_POST['deptId'])?(' in '. \backend\models\Department::findOne(['id'=>$_POST['deptId']])->dept_name.'.'):('.') ;
           echo "<i>" .count($specs)."</i> Specializations found ". $dp .".</caption><thead>
                    <th>#</th>
                    <th>Specialization</th>    
                    <th>Admission</th>
                    <th>Program</th>    
                    <th>Department</th>                
                </thead><tbody>";
                $count = 0;
            foreach ($specs as $spec) {
                echo "<tr>
                <td>". ++$count ."</td>
                <td>". $spec['spec_name'] ."</td>
                <td>". \backend\models\Admission::findOne(['id'=>$spec['spec_adm_relId']])->admission_name ."</td>
                <td>". \backend\models\Program::findOne(['id'=>$spec['spec_prog_relId']])->program_name ."</td>
                <td>". \backend\models\Department::findOne(['id'=>$spec['spec_dep_relId']])->dept_name ."</td>
                </tr>";
            }
        }else{
            echo "<tr><td colspan='5'>No specialization found...</td></tr>";
        }
        echo "</tbody></table>";
        die();
    }

    public function actionListspecCp()
    {               
        $deptId = Yii::$app->request->post('deptId');
        $progId = Yii::$app->request->post('progId');
        $clslvl = Yii::$app->request->post('clslvl');
        $admId = Yii::$app->request->post('admId');
        //Here you have to list also admission, because similar program specialization ids my found.
        $posts = \backend\models\Specialization::find()
                ->where([
                    'spec_dep_relId' => $deptId,
                    'spec_prog_relId'=>$progId,
                    'spec_adm_relId'=>$admId,
                ])
                ->orderBy('id ASC')
                ->all();
                
        if (!empty($posts)) {
            foreach($posts as $post) {
                echo "<option value='".$post->id."'>".$post->spec_name."</option>";
            }
            echo "<option value='' title='Select this option if the specialization is not know yet.'> - </option>";
        } else {
            echo "<option value='' title='No specializations found for this department.'> - </option>";
        }
        
    }

    //returns the list of specs accordingly.

    public function actionListspec()
    {               
        $deptId = Yii::$app->request->post('deptId');
        $progId = Yii::$app->request->post('progId');
        $clslvl = Yii::$app->request->post('clslvl');
        $admId = Yii::$app->request->post('admId');
        //Here you have to list also admission, because similar program specialization ids my found.
        $posts = \backend\models\Specialization::find()
                ->where([
                    'spec_dep_relId' => $deptId,
                    'spec_prog_relId'=>$progId,
                ])
                ->orderBy('id ASC')
                ->all();
                
        if (!empty($posts)) {
            foreach($posts as $post) {
                echo "<option value='".$post->id."'>".$post->spec_name."</option>";
            }
            echo "<option value='' title='Select this option if the specialization is not know yet.'> - </option>";
        } else {
            echo "<option value='' title='No specializations found for this department.'> - </option>";
        }
        
    }

    /**
     * Lists all Specialization models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SpecializationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
          $model = new Specialization();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
             'model'=>$model,
        ]);
    }

    /**
     * Displays a single Specialization model.
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
     * Creates a new Specialization model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Specialization();

        if ($model->load(Yii::$app->request->post())) {

            $model->spec_status = 1;
            $model->spec_created_by = Yii::$app->user->identity->id;
            $model->spec_created_at = date('Y-m-d h:i:s');
            $model->spec_updated_by =Yii::$app->user->identity->id;
            $model->spec_updated_at =date('Y-m-d h:i:s');
            //$model->spec_desc =text();
    
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
     * Updates an existing Specialization model.
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
     * Deletes an existing Specialization model.
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
     * Finds the Specialization model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Specialization the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Specialization::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
