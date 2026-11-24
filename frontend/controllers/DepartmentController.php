<?php

namespace frontend\controllers;

use Yii;
use backend\models\Department;
use backend\models\Course;
use backend\models\DepartmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DepartmentController implements the CRUD actions for Department model.
 */
class DepartmentController extends Controller
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
                       'actions' => ['index','create', 'update', 'delete','view','speclists','getdeptcourses','getdepts','get-depts-for-choice','deptcode'],
                       'allow' => true,
                       'roles' => ['Admin',],
                   ],
                   [
                       'actions' => ['index'],
                       'allow' => true,
                       'roles' => ['Registrar','clerk'],
                   ],
                   [
                       'actions' => ['index'],
                       'allow' => true,
                       'roles' => ['DepartmentHead','Instructor'],
                   ],
               ]
           ]
        ];
    }

    // returns list of deots for preq setting

    public function actionGetdepts(){
        $depts = Department::find()->all();
        if ($depts) {
           foreach ($depts as $dept) {
               echo "<option value='". $dept['id'] ."'>". $dept['dept_name'] ."</option>";
           }
           echo "<option value='0' title='Select this option if the preq. applies for all departments'>All</option>";
        }
        die();
    }


    public function actionGetDeptsForChoice(){
        $options = "<table class='table table-condensed table-striped table-sm'>";
        $options .= "<thead style='background-color: #ace;'>  <th>#</th>  <th>Department Name</th>  <th>Choice</th></thead><tbody>";
        if (isset($_POST['Program']) && !empty($_POST['Program'])) {
            $program  = $_POST['Program'];
            if ($program == 'UG') {

                $depts = Department::find()->where(['<>','dept_name','Basic and Applied Courses'])->all();
                if ($depts && count($depts) > 0) {
                    $count = 0;
                    foreach ($depts as $key) {
                        $dpdns = "<select class='form-control dept_choice_select_class' id='dept_choice_select_{$key->id}' onchange = 'chooseDept({$key->id},$(this).val())' placeholder='Select choice order'>
                                    <option value=''> - </option>
                                    <option id='{$key->id}_1' class='1_index' value='1'>First Chioice</option>
                                    <option id='{$key->id}_2' class='2_index' value='2'>Second Chioice</option>
                                    <option id='{$key->id}_3' class='2_index' value='3'>Third Chioice</option>
                                </select>";
                        $options .= "<tr>";
                        $options .= "<td>".++$count."</td>
                                    <td style='text-alaign:center;'>{$key->dept_name}</td>
                                    <td style='text-alaign:center;'>{$dpdns}</td>";
                        $options .= "</tr>";
                    }

                    $options .= "</tbdoy></table>";

                    echo "{$options}";
                    die();
                }                
            }
        }
    }
    /**
     * Lists all Department models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepartmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new Department();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$model,
        ]);
    }

    // returns the list of dept courses, 
    // @params deptId
    public function actionGetdeptcourses(){
        if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
            $deptId = $_POST['deptId'];

            $dcourses = Course::find()->where(['course_dept_relId'=>$deptId])->all();
            if ($dcourses) {
                foreach ($dcourses as $dcourse) {
                    echo "<option value='". $dcourse['id'] ."'>". $dcourse['course_code'].' => '.$dcourse['course_title'] ."</option>";
                }
            }
            echo "<option value='0' title='Seelct this if the course not a pre-requisit for selected dept (spec.).'>None</option>";
        }
        die();
    }
   
    // returns the list of specs under the selected department

    public function actionSpeclists(){
        if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
            $deptId = $_POST['deptId'];

            $specs = \backend\models\Specialization::find()->where(['spec_dep_relId'=>$deptId]); 
            if (isset($_POST['progId']) && !empty($_POST['progId'])) {
                $specs = $specs->andWhere(['spec_prog_relId'=>intval($_POST['progId'])]);
            }
            if (isset($_POST['admId']) && !empty($_POST['admId'])) {
                $specs = $specs->andWhere(['spec_adm_relId'=>intval($_POST['admId'])]);
            }

            $specs = $specs->all();
            
            if (!empty($specs)) {
                foreach ($specs as $spec) {
                    echo "<option value='". $spec['id'] ."'>". $spec['spec_name'] ."</option>";
                }
            }
            echo "<option value='' title='Select this option if the courses are common to all specs in the dept.'> N/A </option>";
        }else{
            echo "Dept selesction error.";
        }
    }


    // this function returns the equivalent dept code while registering the course of this dept.
    public function actionDeptcode(){
        if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
            $deptId = $_POST['deptId'];

            $deptCode = \backend\models\Department::findone(['id'=>$deptId])->dept_code;

            echo $deptCode.'-';
        }
    }
    /**
     * Displays a single Department model.
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
     * Creates a new Department model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Department();

        if ($model->load(Yii::$app->request->post())) {
            $model->dept_created_by = Yii::$app->user->identity->id;
            $model->dept_updated_by = $model->dept_created_by;
            $model->dept_created_at = date('Y-m-d');
            $model->dept_updated_at = $model->dept_created_at;

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
     * Updates an existing Department model.
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
     * Deletes an existing Department model.
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
     * Finds the Department model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Department the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Department::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
