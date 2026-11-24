<?php

namespace frontend\controllers;

use Yii;
use backend\models\ContAssessCatagory2;
use backend\models\ContAssessCatagory2Search;
use yii\web\Controller;
use \backend\models\InstanceMarkAssignment;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \common\components\CommonControllerFunctions as CCF;

/**
 * ContAssessCatagory2Controller implements the CRUD actions for ContAssessCatagory2 model.
 */
class ContAssessCatagory2Controller extends Controller
{

    public $courseId;
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
        ];
    }

    /**
     * Lists all ContAssessCatagory2 models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->__constructor();
        $searchModel = new ContAssessCatagory2Search();
        $model = new ContAssessCatagory2();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->acadYearId, $this->semId);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('indexAjax', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'model' => $model,
            ]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    // cont-assess-catagory2/get-distribution-report

    public function actionGetDistributionReport()
    { //better if made with course_offering_id
        $courseId = isset($_POST['courseId']) && !empty($_POST['courseId']) ? intval($_POST['courseId']) : die("Uknown Course");
        $coffdetid = isset($_POST['coffdetid']) && !empty($_POST['coffdetid']) ? intval($_POST['coffdetid']) : null;
        $model = new ContAssessCatagory2();
        $model22 = new InstanceMarkAssignment();
        // var_dump($coffdetid);exit;
        $courseCatagory = $model->getCourseCatagory($courseId, $coffdetid, NULL);

        $table = '';
        $ss = 1;
        $course = \backend\models\Course::findOne($courseId);
        // echo "<pre>";
        // var_dump($courseCatagory);die();
        if ($courseCatagory && count($courseCatagory) > 0) {
            $table = "<table class='table table-sm table-bordered table-condensed' style='background-color:#333; color:white;' cellpadding='1' cellspacing='3'>";
            $table .= "<caption>
                        <h4 class='bg-success' style='text-align:center; text-decoration:underline;'>Course assessment mark distribution report</h4>
                        <b>Course Code: </b> <u>{$course->course_code}</u><br>
                        <b>Course Title: </b> <u>{$course->course_title}</u>
                        </caption>";
            $table .= "<thead style = 'background-color: #09E'><tr>";
            $table .= "<th rowspan=2 style='vertical-align:middle'>#</th>
                    <th rowspan=2 style='vertical-align:middle'>Category</th>
                    <th colspan=2 style = 'text-align:center;'>Instance detail</th>
                    <th rowspan=2 style='vertical-align:middle'>Total Mark</th>";
            $table .= "</tr>";
            $table .= "<tr style = 'background-color: #09E'><th>Instance</th><th>Mark</th></tr>";
            $table .= "</thead><tbody>";
            $count = 0;
            $total = 0;
            $total1 = 0;
            $footer = '';
            $instanceApproveStatus = NULL;
            $instanceArray = [];
            // $catagoryId = $courseCatagory[0]['id'];
            foreach ($courseCatagory as $key) {
                $catagoryName = \backend\models\CatagoryName2::findone($key->cat_catagory_relId);
                // if ($catagoryName->catagory_name == 'Final Exam') {
                //     $exists = InstanceMarkAssignment::findOne(['ima_catagorySetup_relId'=>$key->id]);
                //     var_dump($exists);die();
                // }
                $instance = $model22->getInstance($key->id, NULL);
                // echo "<pre>";
                // var_dump($instance,$key->id);die();
                if ($instance && count($instance) > 0) {
                    $rowCount = count($instance);
                    $table .= "<tr><td style='text-align:center; vertical-align:middle;' rowspan='{$rowCount}'>" . ++$count . "</td>";
                    $table .= "<td style='vertical-align:middle;' rowspan='{$rowCount}'>{$catagoryName->catagory_name}</td>";

                    $table .= "<td>{$instance[0]['ima_instanceName']}</td>
                                <td style='text-align:center;'>{$instance[0]['ima_possiblePoint']}%</td>";
                    $table .= "<td style='text-align:center; vertical-align:middle;' rowspan='{$rowCount}'>{$key->cat_maxPoint}%</td></tr>";
                    $instanceArray[] = $instance[0]['id'];

                    $total1 += $instance[0]['ima_possiblePoint'];
                    if (Yii::$app->user->can('Student') && is_null($instance[0]['ima_approved'])) {
                        die("<h4 class='bg-danger'><br>Continous assessment not approved yet.</h4>");
                    }
                    $kk = 0;
                    $total += $key->cat_maxPoint;
                    foreach ($instance as $key2) {
                        // yesirashin biyi!!
                        $instanceApproveStatus = $key2->ima_approved;
                        if ($kk == 0) {
                            $kk++;
                            continue;
                        }
                        $table .= "<tr>";
                        $table .= "<td>{$key2->ima_instanceName}</td>
                                    <td style='text-align:center;'>{$key2->ima_possiblePoint}%</td>";
                        $total1 += $key2->ima_possiblePoint;
                        $table .= "</tr>";
                        $instanceArray[] = $key2->id;
                    }
                } else {
                    continue;
                }
                // $table .= "<th>{$catagoryName->catagory_name}({$key->cat_maxPoint}%)</th>";
            }
            $table .= "</tbody>";
            $table .= "<tfoot><td colspan='2' style='text-align:center;'><b>Total</b></td><td colspan='2' style='text-align:right;'>{$total1}%&nbsp;&nbsp;&nbsp;</td><td style='text-align:center;'>{$total}%</td></tfoot><table>";

            $course = \backend\models\Course::findOne($courseId);
            $instanceArrayIds = $instanceArray;
            $instanceArray = json_encode($instanceArray);
            if ($total1 >= 100) {
                if (Yii::$app->user->can('DepartmentHead')) {
                    // put approve button
                    if (is_null($instanceApproveStatus)) { //pending...
                        $instDept = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id])->inst_dep_relId;
                        if ($course->course_dept_relId == $instDept) {
                            $footer = "<button class='btn btn-success btn-xs pull-right' onclick='approveMD({$instanceArray},1)'>Approve</button>&nbsp;&nbsp;";
                        } else {
                            $footer = "<p class='bg-warning'>Pending for approval by course department head.</p>";
                        }
                    } else if ($instanceApproveStatus == 1) {
                        // check if any assessment is made under these assessments {{$instanceArrayIds}}

                        $assessed = \backend\models\ContinuousAssessment2::find()->where(['ca_markAssignment_relId' => $instanceArrayIds])->count() > 0;
                        $btnn = $assessed ? "Mark Submitted under this assessment setup." : "<button class='btn btn-danger btn-sm pull-right' title = 'Cancel Approval' onclick='approveMD({$instanceArray},3)'>Cancel</button>";
                        $footer = "<p class='bg-success'>You approved the continuous assessment mark distribution.</p>{$btnn}";
                        // $footer = "<p class='bg-warning'>Pending for approval by course department head. </p>";
                    } else {
                        $footer = "<p class='bg-warning'>The request is rejected.</p>";
                    }
                } else if (Yii::$app->user->can('Instructor')) {
                    if (is_null($instanceApproveStatus)) { //pending...
                        $footer = "<p class='bg-warning'>Pending for approval</p>";
                    } else if ($instanceApproveStatus == 1) {
                        $footer = "<p class='bg-warning'>Continuous assessment mark distribution approved.</p>";
                    } else {
                        $footer = "<p class='bg-warning'>The request is rejected.</p>";
                    }
                } else {
                    $footer = $footer;
                }
            } else {
                if (Yii::$app->user->can('DepartmentHead') && $instanceApproveStatus == 1) {
                    $assessed = \backend\models\ContinuousAssessment2::find()->where(['ca_markAssignment_relId' => $instanceArrayIds])->count() > 0;
                    $btnn = $assessed ? "Mark Submitted under this assessment setup." : "<button class='btn btn-danger btn-sm pull-right' title = 'Cancel Approval' onclick='approveMD({$instanceArray},3)'>Cancel</button>";
                    $footer = "<p class='bg-success'>You approved the continuous assessment mark distribution.</p>{$btnn}";
                } else {
                    $footer = "<p class='bg-warning'>Cantinuous assessment mark distribution not completed for this course. Pease complete it.</p>";
                }
            }
            $table .= "<p class='bg-warning' style='float:right; text-align:right;'>{$footer}</p>";
            echo $table;
            die();
        } else {
            echo "<p class='bg-danger'>No continuous Assessment catagroy prepared for this course in this semester.</p>";
            die();
        }
    }

    public function actionGetInstCourse()
    {
        if (Yii::$app->user->can('DepartmentHead') || Yii::$app->user->can('Instructor')) {
            $model = new ContAssessCatagory2();
            $instCourse = $model->getDeptCourses();

            $optuon = "<option value = ''> - </option>";
            if (is_array($instCourse)) {
                foreach ($instCourse as $key) {
                    $optuon .= "<option data-coff = '{$key['coffdetId']}' value = '{$key['id']}'> {$key['course_title']} </option>";
                }
            }
            // echo die();
            return $optuon;
        }
    }

    // get assessment catagory
    public function actionGetCourseCatagory()
    {
        $courseId = isset($_POST['courseId']) && !empty($_POST['courseId']) ? intval($_POST['courseId']) : die("Uknown Course");
        $coffdet = isset($_POST['coffdet']) && !empty($_POST['coffdet']) ? intval($_POST['coffdet']) : die("Course Offering detail error.");

        // get assessment catagory made for this course.
        $model = new ContAssessCatagory2();
        $courseCatagory = $model->getCourseCatagor($courseId, $coffdet);

        // check if the instructor is course co-ordinator
        $this->__constructor();

        if (\backend\models\CourseCordinator::isCordinator($courseId, $this->acadYearId, $this->semId)) {
            # code...
        } else {
            echo "<option value=''> Only Course co-ordiantor is privilleged for this task. </option>";
            die();
        }

        $catagoriesArray = [];
        if ($courseCatagory && count($courseCatagory) > 0) {
            foreach ($courseCatagory as $key) {
                $catagoriesArray[] = $key->cat_catagory_relId;
            }
        }
        $catagoryNames = NULL;
        if (Yii::$app->user->can('DepartmentHead')) {
            $catagoryNames = \backend\models\CatagoryName2::find()
                ->where(['NOT IN', 'id', $catagoriesArray])
                ->all();
        } else if (Yii::$app->user->can('Instructor')) {
            // ->andWhere(['<>','catagory_name','Final Exam'])
            $catagoryNames = \backend\models\CatagoryName2::find()
                ->where(['NOT IN', 'id', $catagoriesArray])
                ->all();
        }

        $remPoint = $model->remainingPoint($courseId, $coffdet);

        $options = "<option value=''> - </option>";
        if ($remPoint <= 0) {
            $options = "<option value='' title='Assessment catagory setup completed for this course.'> - </option>";
        }
        if ($catagoryNames && count($catagoryNames) > 0 && $remPoint > 0) {
            foreach ($catagoryNames as $key) {
                $options .= "<option data-max = {$remPoint} value='{$key->id}'>{$key->catagory_name}</option>";
            }
        }
        echo $options;
        die();
    }

    /**
     * Displays a single ContAssessCatagory2 model.
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
     * Creates a new ContAssessCatagory2 model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ContAssessCatagory2();
        $this->__constructor();

        if ($model->load(Yii::$app->request->post())) {

            // Model created_at.

            $model->cat_created_by = $model->cat_updated_by = Yii::$app->user->identity->id;
            $model->cat_created_at = $model->cat_updated_at = date('Y-m-d h:i:s');
            $model->cat_acadYear_relId = $this->acadYearId;
            $model->cat_sem_relId = $this->semId;

            // check for dupplicate info.
            $exists = \backend\models\ContAssessCatagory2::findOne(['cat_course_relId' => $model->cat_course_relId, 'cat_acadYear_relId' => $this->acadYearId, 'cat_sem_relId' => $this->semId, 'cat_catagory_relId' => $model->cat_catagory_relId]);

            if ($exists) {
                Yii::$app->session->setFlash('message', "Such record alredy registered, If you need additional distribution, try <a href='/instance-mark-assignment/'>mark assignment</a>.");
            } else {
                // check if the total Mark assigned is not morethan 100.
                if ($model->cat_maxPoint > 100) {
                    Yii::$app->session->setFlash('message', "Max point for this catagory could NOT be more than 100.");
                } else {
                    // check if the total mark is lessthan the remaining total mark out of 100.
                    $remPoint = $model->remainingPoint($model->cat_course_relId, $model->cat_coff_relId);
                    if ($model->cat_maxPoint > $remPoint) {
                        Yii::$app->session->setFlash('message', "Max point for this catagory could NOT be more than {$remPoint}.");
                    } else {
                        if ($model->save()) {
                            // auto create instance for final Exam case.
                            return $this->redirect(['index']);
                        } else {
                            var_dump($model->getErrors());
                            die();
                            Yii::$app->session->setFlash('message', "Setup saving failed, please re-try.");
                        }
                    }
                }
            }
            return $this->redirect(['index', ['model' => $model]]);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing ContAssessCatagory2 model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $approved = \backend\models\InstanceMarkAssignment::find()->where(['ima_catagorySetup_relId' => $model->id, 'ima_approved' => 1])->one();

            if ($approved && count($approved) > 0) {
                Yii::$app->session->setFlash('message', 'You cannot update approved assessment entry.');
                Yii::$app->session->setFlash('type', 'error');
                return $this->redirect('index');
            } else {
                if ($model->cat_maxPoint > 100) {
                    Yii::$app->session->setFlash('message', "Max point for this catagory could NOT be more than 100.");
                    Yii::$app->session->setFlash('type', 'error');
                    return $this->redirect('index');
                } else {
                    // check if the total mark is less than the remaining total mark out of 100.
                    $remPoint = $model->remainingPoint($model->cat_course_relId, $model->cat_coff_relId);
                    // subtract old value from remaining:
                    $oldVal = $model->findOne($model->id)->cat_maxPoint;

                    $newVal = $model->cat_maxPoint;

                    $real_rem = abs($remPoint - $oldVal);

                    if ($newVal > $real_rem) {
                        Yii::$app->session->setFlash('message', "Max point for this catagory could NOT be more than {$real_rem}.");
                        Yii::$app->session->setFlash('type', 'error');
                        return $this->redirect('index');
                    } else {
                        if ($model->save()) {
                            // auto create instance for final Exam case.
                            Yii::$app->session->setFlash('message', "Max point for this catagory could NOT be more than {$real_rem}.");
                            Yii::$app->session->setFlash('type', 'success');
                            return $this->redirect(['index']);
                        } else {
                            // var_dump($model->getErrors());die();
                            Yii::$app->session->setFlash('message', "Setup saving failed, please re-try.");
                            Yii::$app->session->setFlash('type', 'warning');
                            return $this->redirect('index');
                        }
                    }
                }
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ContAssessCatagory2 model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $approved = \backend\models\InstanceMarkAssignment::find()
            ->where([
                'ima_catagorySetup_relId' => $id,
                'ima_approved' => 1
            ])
            ->one();

        if ($approved && count($approved) > 0) {
            Yii::$app->session->setFlash('message', 'You cannot delete approved assessment entry.');
            return $this->redirect(['index']);
        } else {
            $this->findModel($id)->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the ContAssessCatagory2 model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ContAssessCatagory2 the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContAssessCatagory2::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
