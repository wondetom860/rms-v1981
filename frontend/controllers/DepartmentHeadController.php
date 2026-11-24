<?php

namespace frontend\controllers;

use Yii;
use backend\models\DepartmentHead;
use backend\models\Instructor;
use backend\models\DepartmentHeadSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DepartmentHeadController implements the CRUD actions for DepartmentHead model.
 */
class DepartmentHeadController extends Controller
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
                       'actions' => ['index','create', 'update', 'delete','clslvllists','dhlists','inistlists'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
                    [
                       'actions' => ['clslvllists','clslvllistss'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => [],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all DepartmentHead models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepartmentHeadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    //gets current active dept head of the selected department. eg CIT - HAFTU HALEFOM or - if not set yet.
    
    public function actionDhlists(){
        $dhs = DepartmentHead::find()->where(['dh_status'=>1])->orderBy('dh_assig_date DESC')->all();
        if (empty($dhs)) {
            echo "<p class='bg-danger'> No department head assigned yet.</p>";
        }else{
            echo "<table class='table table-sm table-striped table-condensed table-bordered'>
            <thead style='background-color:#8DF;'>
                <th>#</th>
                <th>Dept Name</th>
                <th>Dept. Head Name</th>
                <th>Date assigned</th> 
                <th>Status</th>                 
            </thead><tbody>";
            $count = 0;
            foreach ($dhs as $dh) {
                $dept = \backend\models\Department::findOne(['id'=>$dh['dh_dept_relId']]);
                $inst = Instructor::findOne(['id'=>$dh['dh_inst_relId']]);
                $usr = \backend\models\User::findOne(['id'=>$inst->inst_user_relId]);
                $st = $dh['dh_status'] == 1?'ACTIVE':'TERMINATED';
                echo "<tr>
                    <td>". ++$count ."</td>
                    <td>". $dept->dept_name ."</td>
                    <td>". $usr->first_name.' '.$usr->middle_name ."</td>
                    <td>". date_format(date_create($dh->dh_assig_date),"d M, Y") ."</td>
                    <td>". $st ."</td>
                </tr>";
            }
            echo "</tbody></table>";
        }
        die();

    }

        //List of instructors in the department for assigning as DH;
    public function actionInistlists(){
            if(isset($_POST['deptId']) && !empty($_POST['deptId'])){
                $deptId = $_POST['deptId'];

                $insts = Instructor::find()->where(['inst_dep_relId'=>$deptId])->all();
                if (empty($insts)) {
                    echo "<option value='' title='No instructors found in the dept. please register them to find here.'> - </option>";die();
                }else{
                    foreach ($insts as $inst) {
                        $instName  = \backend\models\User::findOne(['id'=>$inst['inst_user_relId']]);
                        echo "<option value='". $inst->id ."'> ". $instName->first_name.' '.$instName->middle_name ."</option>";
                    }
                }

            }else{
                echo "No data provided.";die();
           }
    }
   
    /**
     * Displays a single DepartmentHead model.
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
     * Creates a new DepartmentHead model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DepartmentHead();
        //$model->dhUserRelId = $id;

        if ($model->load(Yii::$app->request->post())) {
            
            $model->created_by = Yii::$app->user->identity->id;
            $model->updated_by = $model->created_by;
            $model->created_at = date('Y-m-d');
            $model->updated_at = $model->created_at;

            $auth = Yii::$app->authManager;
            $role = $auth->getRole('DepartmentHead');
            $userId = Instructor::findOne(['id'=>$model->dh_inst_relId])->inst_user_relId;
            // Check if such active DH exists before.
            $exists = DepartmentHead::findOne(['dh_status'=>1,'dh_inst_relId'=>$model->dh_inst_relId]);
            if ($exists) {
                echo "Currently active department head selected.";
                die();
            }else{
                // get current active DH of the dept.
                $dhs = DepartmentHead::find()->where(['dh_status'=>1,'dh_dept_relId'=>$model->dh_dept_relId])->all();

                $role = $role ? : $auth->getPermission('DepartmentHead');
                if (!empty($dhs)) {
                    foreach ($dhs as $dh) {
                        $userIdi = Instructor::findOne(['id'=>$model->dh_inst_relId])->inst_user_relId;
                        $mm = $this->revokeMine($dh['id']);
                        if ($mm) {
                            $auth->revoke($role,$userIdi);//Remove if exisirs before.
                        }else{
                            print_r($mm);die();
                        }
                    }
                }
            }

            if($model->save()){
                    
                $auth->assign($role,$userId); //Assign role.
            
                // update department head table.
                echo "<script>alert('HoD Asignement success, please assign previlage to new assigned HoD.');</script>";
                return $this->redirect(['index']);
            }else{
                print_r($model->getMessage());die();
            }
                
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    protected function revokeMine($dhId){

        $rec = DepartmentHead::find()
            ->where(['id'=>$dhId])
            ->all();

        //var_dump($rec);die();

        $res = Yii::$app->db->createCommand()
                ->update('department_head', 
                    [
                        'dh_status' => 0,
                        'dh_terminated_date' => date('Y-m-d h:i:s'),
                    ], 
                    [
                        'id'=>$dhId,
                        'dh_status'=>1
                    ])
                ->execute();
        return $res;
    }
    /**
     * Updates an existing DepartmentHead model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing DepartmentHead model.
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
     * Finds the DepartmentHead model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DepartmentHead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DepartmentHead::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
