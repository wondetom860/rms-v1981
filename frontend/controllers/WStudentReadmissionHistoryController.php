<?php

namespace frontend\controllers;

use Yii;
use backend\models\WStudentReadmissionHistory;
use backend\models\WStudentReadmissionHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use \common\components\CommonControllerFunctions as CCF;

/**
 * This is the model class for table "w_student_readmission_history".
 *
 * @property integer $id
 * @property integer $srh_acadYear_relId
 * @property integer $srh_sem_relId
 * @property integer $srh_swh_relId
 * @property integer $srh_std_relId
 * @property string $srh_return_back_on
 * @property integer $srh_HOD_approved
 * @property string $srh_HOD_approved_at
 * @property integer $srh_HOD_approved_by
 * @property integer $srh_approved
 * @property string $srh_approved_at
 * @property integer $srh_approved_by
 * @property string $srh_return_back_letter
 * @property string $remark_HOD
 * @property string $remark_registrar
 */
/**
 * WStudentReadmissionHistoryController implements the CRUD actions for WStudentReadmissionHistory model.
 */
class WStudentReadmissionHistoryController extends Controller
{

    public $acadYearId;
    public $semId;
    public $gset;

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

    /**
     * Lists all WStudentReadmissionHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WStudentReadmissionHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionApproveRequest(){
        $reqId = isset($_POST['reqId']) && !empty($_POST['reqId'])?intval($_POST['reqId']):die('Invalid request, please refine.');
        $action = isset($_POST['action']) && !empty($_POST['action'])?intval($_POST['action']):die('Invalid status cahnge, please refine.');

        $action = $action==2?0:$action;

        $this->__constructor();
        $req = \backend\models\WStudentReadmissionHistory::findOne($reqId);
        $err = 0;

        if ($req && count($req) > 0) {
            $transaction = Yii::$app->db->beginTransaction();
            if (Yii::$app->user->can('DepartmentHead')) {
                $result = Yii::$app->db->createCommand()
                    ->update('w_student_readmission_history',
                        [
                            'srh_HOD_approved'=>$action,
                            'srh_HOD_approved_at' => date('Y-m-d h:i:s'),
                            'srh_HOD_approved_by'=> Yii::$app->user->identity->id,
                        ],
                        [
                            'id'=>$reqId,
                        ])
                    ->execute();
                if ($result) {
                    if ($action == 1) {
                        $transaction->commit();
                        return 1;    
                        // return $this->render('/approve-readmission/assign-to-group',['id'=>$req->id]);
                    }

                    $result = Yii::$app->db->createCommand()
                            ->update('w_student_withdrawal_history',
                                [
                                    'swh_returnedBack'=>NULL,
                                ],
                                [
                                    'id'=>$req->srh_swh_relId,
                                ])
                            ->execute();
                    $result4 = Yii::$app->db->createCommand()
                            ->update('student',
                                [
                                    'std_semester_status'=>'WITHDRAWN',
                                ],
                                [
                                    'id'=>$req->srh_std_relId
                                ])
                            ->execute();
                    $transaction->commit();
                    echo "<p class='bg-danger'>Re-admission request rejected.</p>";
                    die();
                }else{
                    echo "<p class='bg-danger'>Error occured while approving the request. Please retry later.</p>";die();
                }
            }else if(Yii::$app->user->can('Registrar')){
                $result = Yii::$app->db->createCommand()
                    ->update('w_student_readmission_history',
                        [
                            'srh_approved'=>$action,
                            'srh_approved_at' => date('Y-m-d h:i:s'),
                            'srh_approved_by'=> Yii::$app->user->identity->id,
                        ],
                        [
                            'id'=>$reqId,
                        ])
                    ->execute();
                if ($result) {
                    if ($action == 1) {
                        $result4 = Yii::$app->db->createCommand()
                            ->update('student',
                                [
                                    'std_semester_status'=>'Readmission in-proccess',
                                ],
                                [
                                    'id'=>$req->srh_std_relId
                                ])
                            ->execute();
                        $transaction->commit();
                        echo 1;
                        die();
                    }else{
                        $result = Yii::$app->db->createCommand()
                            ->update('w_student_withdrawal_history',
                                [
                                    'swh_returnedBack'=>NULL,
                                ],
                                [
                                    'id'=>$req->srh_swh_relId,
                                ])
                            ->execute();
                        $result4 = Yii::$app->db->createCommand()
                            ->update('student',
                                [
                                    'std_semester_status'=>'WITHDRAWN',
                                ],
                                [
                                    'id'=>$req->srh_std_relId
                                ])
                            ->execute();
                    $transaction->commit();
                    echo 1;
                }
                }else{
                    $transaction->rollBack();
                    echo "<p class='bg-danger'>Error occured while approving the request. Please retry later.</p>";die();
                }
            }else{
                echo "<p class='bg-danger'>Impossible operation!! (::)</p>"; die();
            }
        }

    }

    public function actionGetRequests(){
        if (Yii::$app->user->can('DepartmentHead') || Yii::$app->user->can('Registrar')) {
            $requests = "";
            $this->__constructor();
            if (Yii::$app->user->can('DepartmentHead')) {
                $instdept = \backend\models\Instructor::find()
                    ->where(['inst_user_relId'=>Yii::$app->user->identity->id])
                    ->one()->inst_dep_relId;

                $requests = \backend\models\WStudentReadmissionHistory::find()
                    ->where([
                        'srh_acadYear_relId'=>$this->acadYearId,
                        'srh_sem_relId'=>$this->semId,
                        'srh_approved'=>1,
                        'srh_HOD_approved'=>NULL,
                    ])
                    ->innerJoin('student','student.id = w_student_readmission_history.srh_std_relId')
                    ->andWhere(['student.std_dept_relId' => $instdept])
                    ->all();
            }else{
                $requests = \backend\models\WStudentReadmissionHistory::find()
                    ->where([
                        'srh_acadYear_relId'=>$this->acadYearId,
                        'srh_sem_relId'=>$this->semId,
                        'srh_approved'=>NULL,
                        'srh_HOD_approved'=>NULL,
                    ])
                    ->innerJoin('student','student.id = w_student_readmission_history.srh_std_relId')
                    ->all();
            }


            if ($requests && count($requests) > 0) {
                $repo = "";


                $repo = "<table class='table table-condensed table-hover table-striped table-bordered' style='font-size:9pt;'>
                            <caption>
                                <h4 style='text-align:center;'>Re-Admission requests - Approval pending requests</h4>
                            </caption>
                            <thead style='background-color:#8D0;'>
                                <th>#</th>
                                <th>Reg. ID</th>
                                <th>Student Name</th>
                                <th>Admission</th>
                                <th>Program</th>
                                <th>Department</th>
                                <th>Class level</th>
                                <th>Specialization</th>";
                            $repo .= Yii::$app->user->can('Registrar')?'<th>HoD approve</th>':'<th>Registrar Approve</th>';

                            $repo .= "
                                <th colspan=2>Approve</th>
                            </thead>
                            <tbody>";
                            $count = 0;
                foreach ($requests as $key) {
                    $std = \backend\models\Student::findOne($key->srh_std_relId);
                    $user = \common\models\User1::findOne($std->std_user_relId);

                    $gset = CCF::currentStudentGroup($std);

                    if ($gset && count($gset) > 0) {
                        

                        // $user = \common\models\User1::findOne($userId);
                        $adm = \backend\models\Admission::findOne($gset->gs_adm_relId);
                        $prog = \backend\models\Program::findOne($gset->gs_prog_relId);
                        $dept = \backend\models\Department::findOne($gset->gs_dept_relId);
                        $spec = is_null($gset->gs_spec_relId)?' - ':\backend\models\Specialization::findOne($gset->gs_spec_relId)->spec_name;
                        $clslvl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId);

                    }else{
                        $adm = \backend\models\Admission::findOne($std->std_admission_relId);
                        $prog = \backend\models\Program::findOne($std->std_prog_relId);
                        $dept = \backend\models\Department::findOne($std->std_dept_relId);
                        $spec = is_null($std->std_spec_relId)?' - ':\backend\models\Specialization::findOne($std->std_spec_relId)->spec_name;
                        $clslvl = \backend\models\ClassLevel::findOne($std->std_clslvl_relId);

                    }
                        // $reason = \backend\models\WithdrawalReason::findone($key->swh_reason_relId)->withdrawalres_description;


                        // $css = "NONE";

                        // if ($key->swh_course_removed == 1) {
                        //     $rCourses = \backend\models\RemovedCourse::find()->where(['rc_withdrawal_relId'=>$key->id])->all();

                        //     if ($rCourses && count($rCourses) > 0) {
                        //         $css = "";
                        //         foreach ($rCourses as $key2) {
                        //             $course = \backend\models\Course::findOne($key2->rc_course_relId);
                        //             $css .= "[{$course->course_code} => {$course->course_title}]<br>";
                        //         }
                        //     }
                        // }

                        $btn = "<button class='btn btn-success btn-xs' onclick='approveRequest({$key->id},1)'>Yes</button>";
                        $btn2 = "<button class='btn btn-warning btn-xs' onclick='approveRequest({$key->id},0)'>No</button>";

                        $approved = "";
                        if (Yii::$app->user->can('DepartmentHead')) {
                            $approved = is_null($key->srh_approved)?'Pending':($key->srh_approved == 1?'Approved':'Rejected');
                        }else if (Yii::$app->user->can('Registrar')) {
                            $approved = is_null($key->srh_HOD_approved)?'Pending':($key->srh_HOD_approved == 1?'Approved':'Rejected');
                        }

                        $repo .= "
                            <tr>
                                <td>".++$count."</td>
                                <td>{$std->std_regId}</td>
                                <td>{$user->first_name} {$user->middle_name} {$user->last_name}</td>
                                <td>{$adm->admission_name}</td>
                                <td>{$prog->program_name}</td>
                                <td>{$dept->dept_name}</td>
                                <td>{$clslvl->clslvl_num}</td>
                                <td>{$spec}</td>
                                <td style='vertical-align:middle;'>{$approved}</td>
                                <td style='vertical-align:middle;'>{$btn}</td>
                                <td style='vertical-align:middle;'>{$btn2}</td>
                            </tr>                                
                            ";
                }
                $repo .= "</tbody>
                        </table>
                    </div>

                </div>";
                echo "{$repo}";
                die();

            }else{
                echo "<p class='bg-danger'> No request found.</p>";die();
            }
            
        }else{
            
        }
    }

    /**
     * Displays a single WStudentReadmissionHistory model.
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
     * Creates a new WStudentReadmissionHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WStudentReadmissionHistory();

        if ($model->load(Yii::$app->request->post())) {

            if (Yii::$app->user->can('Student')) {

                $this->__constructor();
                // $model->file = UploadedFile::getInstance($model,'file');

                $transacrtion = Yii::$app->db->beginTransaction();

                try {

                    $user = \common\models\User1::findOne(Yii::$app->user->identity->id);
                    $std = \backend\models\Student::findOne(['std_user_relId'=>$user->id]);
                    $model->srh_std_relId = $std->id;
                    // $fileName = "_{$user->first_name}_{$user->middle_name}".date('M_D_Y');
                    // // $fileName = md5($user->first_name. '_'.$user->middle_name[0].'_'.date('Y_m_d@h_i'));
                    // $model->file->saveAs( 'uploads/readmission/'.$fileName.'.'.$model->file->extension );

                    $model->srh_return_back_letter = "Withdrawal report";
                    $model->srh_created_at = date('Y-m-d h:i:s');
                    $model->srh_created_by = Yii::$app->user->identity->id;
                    $model->srh_acadYear_relId = $this->acadYearId;
                    $model->srh_sem_relId = $this->semId;
                    
                    if ($model->save()) {
                        // echo 1;
                        // indicate that re-admission is requested for the withdrawal. 
                        // swh_returnedBack => NULL = NOT yet, 1 = Applied for re-admission 2 = completed proces
                        $result = Yii::$app->db->createCommand()
                            ->update('w_student_withdrawal_history',['swh_returnedBack'=>1],['id'=>$model->srh_swh_relId])
                            ->execute();

                        $transacrtion->commit();
                        Yii::$app->session->setFlash('message', "Re-admission request completed successfully. Wait for approval.");
                        return $this->redirect('/request-readmission/index')->send();
                    }else{
                        // var_dump($model->getErrors());die();
                        $transacrtion->rollBack();
                        Yii::$app->session->setFlash('message', "Error while attempting to request re-admission. Pleae re-try by completing your requiered inputs.!");
                        return $this->redirect('/request-readmission/index')->send();
                    }
                    
                } catch (Exception $e) {
                    $transacrtion->rollBack();
                    Yii::$app->session->setFlash('message', "Error while attempting to request re-admission. Pleae re-try!<br><pre>{$e->getMessage()}</pre>");
                    return $this->redirect('/request-readmission/index')->send();
                }
            }            
        } else {
            return $this->redirect('/request-readmission/index')->send();
        }
    }

    /**
     * Updates an existing WStudentReadmissionHistory model.
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
     * Deletes an existing WStudentReadmissionHistory model.
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
     * Finds the WStudentReadmissionHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WStudentReadmissionHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WStudentReadmissionHistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
