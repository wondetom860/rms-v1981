<?php

namespace frontend\controllers;

use Yii;
use backend\models\WGroupAssignment;
use backend\models\WGroupSetup;
use backend\models\SchoolYear;
use backend\models\Semester;
use backend\models\WGroupAssignmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\ErrorException;
use \common\components\CommonControllerFunctions as CCF;

/**
 * WGroupAssignmentController implements the CRUD actions for WGroupAssignment model.
 */
class WGroupAssignmentController extends Controller
{
    public $acadYearId;
    public $semId;
    protected function __constructor()
    {

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
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                // We will override the default rule config with the new AccessRule class
                'ruleConfig' => [
                    'class' => \yii\filters\AccessRule::class,
                ],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'liststud', 'degroup', 'get-courses','get-under-loaded-students'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Admin'],
                    ],
                    [
                        'actions' => ['index', 'liststud', 'degroup','get-under-loaded-students'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['clerk', 'Registrar', 'AcademicDean', 'DepartmentHead', 'UniversityRegistrar'],
                    ],
                    [
                        'actions' => ['index', 'liststud', 'degroup'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['DepartmentHead'],
                    ],
                ]
            ]
        ];
    }

    public function actionGetUnderLoadedStudents(){
        $modelGas = new WGroupAssignment();
        $this->__constructor();
        $unEnrolledStudents = $modelGas->getUnEnrolledStudents($this->acadYearId,$this->semId);
        $this->layout = "printLayout";
        return $this->render('__report/get-under-loaded-students',['listModel' => $unEnrolledStudents]);
    }
    /**
     * Lists all WGroupAssignment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WGroupAssignmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetCourses()
    {
        $gas_id = Yii::$app->request->post('gasId');

        $model = $this->findModel($gas_id);

        return $this->renderPartial('__report/courses-for-registration', ['model' => $model]);
    }

    private function enrolledCourse($gsetId, $std)
    {
        return \frontend\models\SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'srd2_group_relId' => $gsetId,
            ])
            ->andWhere(['semreg_std_relId' => $std])
            ->count() > 0;
    }

    public function actionDegroup()
    {
        // POSTs
        // $grpId = isset($_POST['gid'])?$_POST['gid']:die('Uknown Group Name');
        $std = isset($_POST['stdId']) ? $_POST['stdId'] : die('Uknown Student Name');
        $gsetId = isset($_POST['gsetId']) ? $_POST['gsetId'] : die('Uknown Group Setup');

        $gset = WGroupSetup::findOne($gsetId);
        if (!empty($gset)) {
            $gass = WGroupAssignment::findOne(['gas_std_relId' => $std, 'gas_gset_relId' => $gset->id, 'gas_approved' => NULL]);
            if (!empty($gass)) {
                // check if student enrolled course with this group
                if ($this->enrolledCourse($gsetId, $std)) {
                    return "<p class='bg-danger'>Student Enrolled course in this group</p>";
                }
                $model = Yii::$app->db
                    ->createCommand('DELETE FROM w_group_assignment WHERE gas_gset_relId =:gsetId AND gas_std_relId =:stdId');
                $model->bindParam(':gsetId', $gsetId);
                $model->bindParam(':stdId', $std);

                if ($model->execute()) {
                    $up = Yii::$app->db->createCommand()
                        ->update('student', ['std_group_realId' => NULL,], 'id=' . $std);
                    if ($up->execute()) {
                        echo "Success";
                    } else {
                        echo "Student profile update failed.";
                        // $model->rollBack();
                    }
                } else {
                    var_dump($model->errors);
                }
                // die();
                //echo 'Deleting...';
            } else {
                echo "<p class='bg-danger'>Only un-approved group assignments are rolled back.</p>";
            }
        } else {
            echo "<p class='bg-danger'>Such group set up doesn\'t exisits.</p>";
        }
    }

    public function actionListstud()
    {
        if (isset($_POST['grpId']) && !empty($_POST['grpId'])) {
            $this->__constructor();
            $grpId = $_POST['grpId']; //group Id or GsetUp Id?   =>grpId is group ID.
            $wreport = "";
            $stds = WGroupAssignment::find()
                ->innerJoin('w_group_setup', 'w_group_setup.id=w_group_assignment.gas_gset_relId')
                ->innerJoin('student', 'student.id=w_group_assignment.gas_std_relId')
                ->innerJoin('user', 'user.id=student.std_user_relId')
                ->where([
                    'gas_gset_relId' => $grpId,
                    'gs_acadYear_relId' => $this->acadYearId,
                    'gs_sem_relId' => $this->semId,
                ])
                ->orderBy(['trim(first_name)' => SORT_ASC, 'trim(middle_name)' => SORT_ASC])
                ->all();
            // 'gas_approved'=>0,
            if ($stds) {
                $grp = WGroupSetup::findOne($grpId);
                $gnsme = $grp->gsGroupRel->group_name;

                $wreport .= "<table class='table table-striped table-condensed table-sm table-bordered' style='font-size:8pt;'>";
                $wreport .= "<caption><p class='bg-success'> <i class='bg-danger'>" . count($stds) . "</i> Students assigned for " . $gnsme . ".</p></caption>";
                $wreport .= "<thead style='background-color:#8D0;'>
                    <th>#</th>
                    <th>RegId</th>
                    <th>Full Name</th>
                    <th>Group</th>
                    <th>Option</th>
                    </thead><tbody>";
                $count = 0;
                foreach ($stds as $std) {
                    $student = \backend\models\Student::findOne(['id' => $std->gas_std_relId]);
                    //$usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
                    $btn = $this->enrolledCourse($grp->id, $student->id) ? "<span class='bg-warning'> Enrolled Course </span>" : "<button class='btn btn-sm btn-warning btn-xs' onclick='deGroup(" . $std->gas_std_relId . "," . $grpId . "," . $grp->id . "); return false;'>change</button>";
                    $wreport .= "<tr>
                    <td>" . ++$count . "</td>
                    <td>" . $student->std_regId . "</td>
                    <td>{$student->getFullName()}</td>
                    <td>" . $gnsme . "</td>
                    <td>{$btn}</td>
                    </tr>";
                }
                $wreport .= "</tbody></table>";
                return $wreport;
            } else {
                return "<p class='bg-danger'>No student in this group</p>";
            }
        }
    }
    public function actionGradesubmission()
    {
        if (isset($_POST['courseId']) && isset($_POST['groupId'])) {
            // acadYearId
            // semId,
            // courseId,
            // groupId

            $groupId = $_POST['groupId'];
            $semId = Semester::findOne(['semester_name' => Yii::$app->params['C']['S']])->id;
            $acadYearId = SchoolYear::findOne(['sch_year_desc' => Yii::$app->params['C']['Y']])->id;
        } else {
        }
    }

    /**
     * Displays a single WGroupAssignment model.
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
     * Creates a new WGroupAssignment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->__constructor();
        $model = new WGroupAssignment();
        $modelg = new WGroupSetup();

        if (!Yii::$app->user->can('Admin')) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $modelCoff = new \backend\models\CourseOffering2();

        if (isset($_POST['gid'])) {
            $gid = isset($_POST['gid']) ? $_POST['gid'] : die('Group not known');
            $stdList = isset($_POST['stdList']) ? $_POST['stdList'] : die('Student List Uknown.');
            $gid = intval($gid);

            $stdList = json_decode($stdList);
            $scount = 0;
            $fcount = 0;
            if (!empty($stdList) && count($stdList) > 0) {
                // 'gs_acadYear_relId'=>$this->acadYearId,
                // 'gs_sem_relId'=>$this->semId,
                $grp = WGroupSetup::find()
                    ->where([
                        'id' => $gid,
                    ])
                    ->one();
                $statu = "";
                if (!empty($grp)) {
                    $grpId = $grp->id;
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $statu = $modelCoff->distributeCoff($grp);
                        foreach ($stdList as $std) {
                            // check if already assigned a group.
                            $exisits = WGroupAssignment::findOne(['gas_gset_relId' => $grpId, 'gas_std_relId' => $std]);
                            if (!empty($exisits)) {
                                $fcount++;
                                continue;
                            } else {
                                $model = new WGroupAssignment();
                                $model->gas_gset_relId = $grpId;
                                $model->gas_std_relId = $std;
                                $model->gas_created_at = date('Y-m-d h:i:s');
                                if ($model->save()) {
                                    $scount++;
                                    // update student profile.
                                    Yii::$app->db->createCommand()
                                        ->update('student', [
                                            'std_group_realId' => $grpId,
                                            'std_dept_relId' => $grp->gs_dept_relId,
                                            'std_prog_relId' => $grp->gs_prog_relId,
                                            'std_spec_relId' => $grp->gs_spec_relId,
                                            'std_clslvl_relId' => $grp->gs_clslvl_relId,
                                            'std_semester_status' => 'ACTIVE',
                                        ], 'id=' . $std)
                                        ->execute();
                                }
                            }
                        }
                        // successfully completed task!
                        $transaction->commit();
                        return $statu;
                    } catch (ErrorException $e) {
                        $transaction->rollBack();
                        var_dump($e);
                        die();
                    }
                } else {
                    echo 'Group Setup error: Such group is not setted. ';
                    die();
                }

                $t = $scount + $fcount;
                echo "<p class='bg-secondary'>
                    Successfully assigned <b>{$scount}</b> of <b>{$t}</b> students. <b>{$fcount}</b> failed. <br> Distribution Status : {$statu}</p>";
                die();
            } else {
                echo "<p class='bg-danger'>Student List Empty,.</p>";
                die();
            }
        }


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            if (Yii::$app->request->isAjax) {
                echo "<p class='bg-danger'>Form completion error.</p>";
            }
            return $this->render('create', [
                'model' => $model,
                'modelg' => $modelg,
            ]);
        }
    }

    /**
     * Updates an existing WGroupAssignment model.
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

    public function actionDeleteAssign()
    {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('Invalid Entry.');

        $gas = $this->findModel($id);
        if ($gas) {
            $gset = $gas->gasGsetRel;
            $std = $gas->gasStdRel;
            $exisits = \frontend\models\SemesterRegistration2Detail::find()
                ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                ->where(['semreg_std_relId' => $std->id])
                ->andWhere(['srd2_group_relId' => $gset->id])
                ->one();
            if ($exisits && count($exisits) > 0) {
                die("This Student already registered courses by this group. You can NOT drop assignment.");
            }
            $transaction = Yii::$app->db->beginTransaction();

            $result = Yii::$app->db->createCommand()
                ->update(
                    'student',
                    [
                        'std_group_realId' => NULL,
                    ],
                    [
                        'id' => $std->id
                    ]
                )
                ->execute();
            if ($result) {
                if (WGroupAssignment::findOne($id)->delete()) {
                    $transaction->commit();
                    echo 1;
                    die();
                }
            } else {
                die('Group Assignment cancelling failed, UKNOWN ERROR OCCURED.');
            }
        } else {
            die('No Such Entry found...');
        }
    }

    /**
     * Deletes an existing WGroupAssignment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        if (Yii::$app->request->isAjax) {
            return 1;
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the WGroupAssignment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WGroupAssignment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WGroupAssignment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
