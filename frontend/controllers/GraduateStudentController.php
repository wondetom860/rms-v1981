<?php

namespace frontend\controllers;

use Yii;
use backend\models\GraduateStudent;
use backend\models\GraduateStudentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\User;

/**
 * GraduateStudentController implements the CRUD actions for GraduateStudent model.
 */
class GraduateStudentController extends Controller
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
                       'actions' => [],
                       'allow' => true,
                       'roles' => ['@'],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all GraduateStudent models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GraduateStudentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $_SESSION['query_params'] = is_array(Yii::$app->request->queryParams) ? Yii::$app->request->queryParams: json_encode(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetPointHistory(){
        // get-point-history

        $this->layout = 'printLayout';

        $graduateStudentRecordEntryId = isset($_GET['fdfhsgh']) && !empty($_GET['fdfhsgh']) ? (string)($_GET['fdfhsgh']) : die('INVALID_REQUEST');

        $modelGstudent = NULL;

        $graduateStudentRecordEntryId = array_unique(json_decode($graduateStudentRecordEntryId));

        if (is_array($graduateStudentRecordEntryId) && count($graduateStudentRecordEntryId) > 0) {
            $modelGstudent = GraduateStudent::find()->innerJoin('user','user.id=graduate_student.gstd_user_relId')->where(['graduate_student.id' => $graduateStudentRecordEntryId])->orderBy('user.first_name')->all();
        }else{
            if (isset($_SESSION['query_params']) && !empty($_SESSION['query_params'])) {
                $searchModel = new GraduateStudentSearch();
                // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                $queryParams = $_SESSION['query_params'];
                $modelGstudent = $searchModel->search($queryParams,1);
            }else{
                $modelGstudent = GraduateStudent::find()->innerJoin('user','user.id=graduate_student.gstd_user_relId')->orderBy('user.first_name')->all();
            }
        }

        // $pointHistoryObject = ;

        return $this->render('gph-index',['model'=>$modelGstudent]);


    }

    public function actionApproveRecords(){

    }

    public function actionApproveRecord(){
        $recordId = isset($_POST['recordId']) && !empty($_POST['recordId']) ? intval($_POST['recordId']) : die('INVALID_REQUEST');

        if (!Yii::$app->user->can('Registrar')) {
            return "UN_PRIEVELLEGED_ACCESS";
        }
        $record = GraduateStudent::find()
            ->where([
                'gstd_approved_by' => NULL,
                'gstd_approved_at' => NULL,
                'id' => $recordId,
            ])
            ->one();
        if ($record) {
            return Yii::$app->db->createCommand()
                ->update('graduate_student',
                    [
                        'gstd_approved_at' => date('Y-m-d H:i:s'),
                        'gstd_approved_by' => Yii::$app->user->id,
                    ],
                    [
                        'id' => $record->id,
                    ])
                ->execute();
        }

        return "RECORD_ENTRY_ERROR!";
    }

    public function actionGetRecord(){
        if (isset($_POST['recordId'])) {
            $_SESSION['recordId'] = $_POST['recordId'];
        }elseif (isset($_SESSION['recordId'])) {
            $_POST['recordId'] = $_SESSION['recordId'];
        }
        return $this->renderPartial('/graduate-student-point-history/includes/_graduation_history', 
            [
                'model' => GraduateStudent::find()->where(['id' => intval($_POST['recordId'])])->all()
            ]);
    }

    public function actionGetStudentListForApproval(){
        // check if the usr is registrar
        if (Yii::$app->user->can('Registrar')) {
            $model = GraduateStudent::find()
                ->innerJoin('user','user.id = graduate_student.gstd_user_relId')
                ->where(['OR',
                    ['gstd_approved_by' => NULL],['gstd_approved_at' => NULL]
                ])
                ->orderBy('user.first_name');
            if (isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])) {
                $model = $model->andWhere(['gstd_grad_year' => $_POST['acadYearId']]);
            }

            if (isset($_POST['admId']) && !empty($_POST['admId'])) {
                $model = $model->andWhere(['gstd_adm_relId' => intval($_POST['admId'])]);
            }

            if (isset($_POST['progId']) && !empty($_POST['progId'])) {
                $model = $model->andWhere(['gstd_prog_relId' => intval($_POST['progId'])]);
            }

            if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
                $model = $model->andWhere(['gstd_dept_relId' => intval($_POST['deptId'])]);
            }

            $model = $model->all();
            return $this->renderPartial('/approve-graduate-student-information/includes/__gstd_list',['model' => $model]);
        }else{
            return "UN_PRIEVELLEGED_ACCESS!";
        }
    }

    public function actionGetAllUsers(){
        if (Yii::$app->user->can('Admin') || Yii::$app->user->can('clerk') || Yii::$app->user->can('Registrar')) {
            $users = User::find()->orderBy(['first_name' => SORT_ASC])->all();
            $model = new GraduateStudent();
            if ($users && count($users)) {
                $report = "<table style='font-size:9pt;' class='table table-bordered table-condensed table-striped table-hover'>";
                $report .= "<thead style='background-color:#8DF;'>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Gender</th>
                        <th>Nationality</th>
                        <th>Role</th>
                        <th>Option</th>
                    </thead><tbody>";
                $count = 0;
                foreach ($users as $user) {
                    // An student can admit on more ONE program and graduate via too. So checking for       // existing record should in another way, Once graduated on a program, record cannot   // repeated for similar program

                    // $imported = GraduateStudent::findOne(['gstd_user_relId' => $user->id]);
                    // if ($imported && count($imported) > 0) {
                    //     continue;
                    // }

                    $role = $model->getUserRole2($user->id);
                    if (strtoupper($role) == 'STUDENT' || strtoupper($user->username) == 'ADMIN') {
                        continue;
                    }
                    $fullname = $user->first_name .' '.$user->middle_name .' '. $user->last_name;
                    $nat = is_null($user->nationality) || $user->nationality == 0?'':\backend\models\Nationality::findOne($user->nationality)->name;

                    $btn = "<button class='btn btn-xs btn-primary' onclick='loadForm({$user->id},". json_encode($fullname) .")'>Load</button>";

                    $report .= "<tr>
                                <td>". ++$count ."</td>
                                <td>{$fullname}</td>
                                <td>{$user->gender}</td>
                                <td>{$nat}</td>
                                <td>{$role}</td>
                                <td>{$btn}</td>
                            </tr>";
                }

                return $report ."</tbody></table>";
            }
        }
    }

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
            echo "<option value='0' title='Select this option if the courses are common to all specs in the dept.'> N/A </option>";
        }else{
            echo "Dept selesction error.";
        }
    }


    /**
     * Displays a single GraduateStudent model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionImport(){
        $model = new GraduateStudent();
        $modelUser = new \common\models\User1();
        if ($model->load(Yii::$app->request->post())) {
            if ($this->checkIfExists2($model,$modelUser)) {
                Yii::$app->session->setFlash('message','Such record already Exists!');
            }else{
                $model->gsdt_prepared_by = Yii::$app->user->id;
                if ($model->save()) {
                    return $this->redirect('index');
                }
            }
        }
        return $this->render('import', [
            'model' => $model,
            'modelUser' => $modelUser,
        ]);
    }

    public function checkIfExists2($model,$modelUser){
        $modelUser = isset($modelUser->first_name) ? $modelUser : $model->gstdUserRel;
        return GraduateStudent::find()
            ->innerJoin('user','user.id = graduate_student.gstd_user_relId')
            ->where([
                'user.first_name'=>$modelUser->first_name,
                'user.middle_name'=>$modelUser->middle_name,
                'user.last_name'=>$modelUser->last_name,
            ])
            ->andWhere(['gstd_prog_relId' => $model->gstd_prog_relId])
            ->count() > 0;
    }

    /**
     * Creates a new GraduateStudent model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GraduateStudent();
        $modelUser = new \common\models\User1();

        if ($model->load(Yii::$app->request->post())) {

            if ($modelUser->load(Yii::$app->request->post())) {
                $transaction = Yii::$app->db->beginTransaction();
                // $modelUser->user_role = 'Student';
                $modelUser->phone_mobile = '+251'. rand(999999,100000000);
                $modelUser->OTP = substr(md5($modelUser->phone_mobile), 0,24);
                $modelUser->activated = 1;
                

                $modelUser->setPassword($modelUser->getPassword());
                $modelUser->generateAuthKey();
                $modelUser->dob = date_format(new \DateTime('- 20 years'),'Y-m-d');


                $modelUser->status = $modelUser::STATUS_GRADUATED;
                $modelUser->username = $modelUser->OTP;
                $modelUser->user_type = $model->gstd_profession_category;
                if ($this->checkIfExists2($model,$modelUser)) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('message','Such record already Exists!');
                }else{
                    if ($modelUser->save()) {
                        $model->gstd_user_relId = $modelUser->id;
                        $model->gsdt_prepared_by = Yii::$app->user->id;
                        if ($model->save()) {
                            $transaction->commit();
                            Yii::$app->session->setFlash('message','Successfully completed');
                        }else{
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('message','Record NOT completed!');
                        }
                    return $this->render('create', [
                        'model' => new GraduateStudent(),
                        'modelUser' => $modelUser,
                    ]);
                    }else{
                        var_dump($modelUser->getErrors());
                        die();
                    }
                }

                return $this->render('create', [
                        'model' => $model,
                        'modelUser' => $modelUser,
                    ]);
            }
        } else {
            $modelUser = isset($_GET['user_id']) && !empty($_GET['user_id']) ? $this->findModel(intval($_GET['user_id'])):new \common\models\User1();
            return $this->render('create', [
                'model' => $model,
                'modelUser' => $modelUser,
            ]);
        }
    }

    /**
     * Updates an existing GraduateStudent model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelUser = \common\models\User1::findOne($model->gstd_user_relId);

        if ($model->gstd_approved_by != NULL || $model->gstd_approved_at != NULL) {
            die('YOU CAN NOT UPDATE APPROVED RECORD, Please contact Registrar Head...');
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'modelUser'=>$modelUser,
            ]);
        }
    }

    /**
     * Deletes an existing GraduateStudent model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->gstd_approved_by != NULL || $model->gstd_approved_at != NULL) {
            Yii::$app->session->setFlash('type','Error');
            Yii::$app->session->setFlash('message','Record Can NOT be deleted!');
            return $this->redirect(['index']);
        }
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the GraduateStudent model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GraduateStudent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GraduateStudent::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
