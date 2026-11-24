<?php

namespace frontend\controllers;

use Yii;
use backend\models\TouchedGrades;
use backend\models\TouchedGradesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ChagedGradesController implements the CRUD actions for TouchedGrades model.
 */
class ChagedGradesController extends Controller
{
    /**
     * {@inheritdoc}
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
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::class,
               ],
               'rules' => [
                   [
                       'actions' => ['index','create', 'update', 'delete','get-student-list','get-list'],
                       'allow' => true,
                       'roles' => ['Admin','Registrar'],
                   ],
                   [
                       'actions' => ['approve-grade-change','reject-grade-change'],
                       'allow' => true,
                       'roles' => ['Registrar'],
                   ],
                   [
                       'actions' => ['index'],
                       'allow' => true,
                       'roles' => ['DepartmentHead'],
                   ],[
                       'actions' => ['get-report-print'],
                       'allow' => true,
                       'roles' => ['@'],
                   ],
               ]
           ]
        ];
    }

    public function actionGetReportPrint(){
        $searchModel = new TouchedGradesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,1);

        $this->layout='printLayout';

        return $this->render('__report/changed-grades-list-print', [
            'model' => $dataProvider
        ]);
    }

    // grade scale list
    public function actionGetList(){
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Select Program');

        $gss = \backend\models\GradeScale::find()
            ->where([
                'gs_forProgram' => $progId,
            ])
            ->all();
        $report = "<option value=''> - </option>";

        if ($gss && count($gss) > 0) {
            foreach ($gss as $key) {
                $report .= "<option value='{$key->id}'>{$key->gs_ltr_grade}</option>";
            }
        }

        return $report;
    }

    public function actionGetStudentList(){
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('Select Acdemic Year');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('Select Semester');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Select Program');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Select Class Level');

        $ng_i = \backend\models\GradeScale::find()->where(['gs_ltr_grade' => ['NG','I','F']])->all();
        $ng_i_grades = [];
        if ($ng_i && count($ng_i) > 0) {
            foreach ($ng_i as $keyGs) {
                $ng_i_grades[] = $keyGs->id;
            }
        }

        // 'student.std_prog_relId' => $progId,
        $sdr2Obj = \frontend\models\SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')
            ->innerJoin('user','user.id = student.std_user_relId')
            ->where([
                'semreg_semester_relId' => $semId,
                'semreg_acadYear_relId' => $acadYearId,
                'semreg_clslvl_relId' => $clslvlId,
            ])
            ->andWhere(['OR',
                ['srd2_grade_relId' => NULL],
                ['srd2_grade_relId' => $ng_i_grades],
                ['srd2_gradeApproved' => NULL],
            ])
            ->orderBy('user.first_name')
            ->all();

        $wreport = "";

        if ($sdr2Obj && count($sdr2Obj) > 0) {
            $wreport .= "<table class='table table-condensed table-striped table-bordered' style='font-size:9pt;'>";
            $wreport .= "<thead>   
                            <th>#</th>
                            <th>Reg. ID</th>
                            <th>Student Name</th>
                            <th>Course Detail</th>
                            <th>Before Grade</th>
                            <th>Option</th>
                        </thead></tbody>";
            $count = 0;
            foreach ($sdr2Obj as $SRD2key) {
                if($SRD2key->alreadyRequested()){
                    continue;
                }
                $userObj = $SRD2key->srd2SemregRel->semregStdRel->stdUserRel;
                $courseObj = $SRD2key->srd2CourseRel;
                $bGradeObj = $SRD2key->srd2GradeRel ? $SRD2key->srd2GradeRel->gs_ltr_grade : '-';
                $button = "<button class='btn btn-primary btn-xs' onclick='loadSrd2Info({$SRD2key->id},{$SRD2key->srd2_semreg_relId});return false;'>Change</button>";
                $wreport .= "<tr>
                                <td>".++$count."</td>
                                <td>{$SRD2key->srd2SemregRel->semregStdRel->std_regId}</td>
                                <td>{$userObj->getFullName()}</td>
                                <td>{$courseObj->course_code} => {$courseObj->course_title}</td>
                                <td>{$bGradeObj}</td>
                                <td>{$button}</td>
                            </tr>";
            }
            $wreport .= "</tbody></table>";
            return $wreport;
        }else{
            return "<p class='bg-warning'>No Record</p>";
        }
    }

    /**
     * Lists all TouchedGrades models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TouchedGradesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TouchedGrades model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionApproveGradeChange(){
        // approve-grade-change
        $requestId = isset($_POST['reqId']) && !empty($_POST['reqId']) ? intval($_POST['reqId']) : die('INVALID_REQUEST');
        $option = isset($_POST['option']) && !empty($_POST['option']) ? intval($_POST['option']) : die('INVALID_REQUEST');

        $option = $option == 2?NULL:$option;

        $model = TouchedGrades::findOne(['id'=>$requestId,'tg_operation_completed'=>NULL]);
        if (Yii::$app->user->can('Registrar') && $model) {
            $result = Yii::$app->db->createCommand()
                ->update('touched_grades',
                    [
                        'tg_approved_by' => Yii::$app->user->id,
                        'tg_approved_at' => date('Y-m-d H:i:s'),
                        'tg_updated_at' => date("Y-m-d h:i:s"),
                        'tg_updated_by' => Yii::$app->user->identity->id,
                    ],
                    [
                        'id' => $model->id,
                    ])
                ->execute();

            if ($result) {
                $result = Yii::$app->db->createCommand()
                    ->update(
                        'semester_registration_2_detail',
                        [
                            'srd2_grade_relId' => $model->tg_afterGrade_relId,
                            'srd2_gradeApproved' => 1,
                            'srd2_updated_at' => date("Y-m-d h:i:s"),
                            'srd2_updated_by' => Yii::$app->user->identity->id,
                        ],
                        [
                            'id' => $model->tg_srd2_relId,
                        ])
                    ->execute();
                // if()
                $model->tgSrd2Rel->srd2SemregRel->calculateGPA();
                return $result;
            }
        }
        return 'Operation NOT allowed!';
    }

    public function actionRejectGradeChange(){
        // approve-grade-change
        // reject-grade-change
        $requestId = isset($_POST['reqId']) && !empty($_POST['reqId']) ? intval($_POST['reqId']) : die('INVALID_REQUEST');
        $model = TouchedGrades::findOne(['id'=>$requestId,'tg_operation_completed'=>NULL]);
        if (Yii::$app->user->can('Registrar') && $model) {
            $result = Yii::$app->db->createCommand()
                ->update('touched_grades',
                    [
                        'tg_approved_by' => NULL,
                        'tg_approved_at' => NULL,
                        'tg_updated_at' => date("Y-m-d h:i:s"),
                        'tg_updated_by' => Yii::$app->user->identity->id,
                    ],
                    [
                        'id' => $model->id,
                    ])
                ->execute();
        }

        if ($result) {
            $result = Yii::$app->db->createCommand()
                    ->update(
                        'semester_registration_2_detail',
                        [
                            'srd2_grade_relId' => $model->tg_beforeGrade_relId,
                            'srd2_gradeApproved' => 1,
                            'srd2_updated_at' => date("Y-m-d h:i:s"),
                            'srd2_updated_by' => Yii::$app->user->identity->id,
                        ],
                        [
                            'id' => $model->tg_srd2_relId,
                        ])
                    ->execute();
                return $result;
        }
    }

    /**
     * Creates a new TouchedGrades model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TouchedGrades();

        if ($model->load(Yii::$app->request->post())) {

            $model->tg_created_by = Yii::$app->user->id;
            $model->tg_created_at =date('Y-m-d h:i:s');

            if (isset($model->tg_srd2_relId)) {
                $modelsrd2 = \frontend\models\SemesterRegistration2Detail::findOne($model->tg_srd2_relId);
                if ($modelsrd2) {
                    $model->tg_semreg_relId = $modelsrd2->srd2_semreg_relId;
                    $model->tg_beforeGrade_relId = $modelsrd2->srd2_grade_relId;
                }
            }else{
                return 'Invalid Request, Please correct.';
            }
            if ($model->save()) {
                return $this->redirect(['index', 'id' => $model->id]);
            }else{
                var_dump($model->getErrors());die();
            }
            
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TouchedGrades model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->tg_approved_by != null) {
            Yii::$app->session->setFlash('message','You cannot update approved request');
            Yii::$app->session->setFlash('type','error');
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TouchedGrades model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model) {
            if ($model->tg_approved_by != null) {
                Yii::$app->session->setFlash('message','You cannot remove approved request');
                Yii::$app->session->setFlash('type','error');
                return $this->redirect(['index']);
            }
            $model->delete();
            return $this->redirect(['index']);
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));        
    }

    /**
     * Finds the TouchedGrades model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TouchedGrades the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TouchedGrades::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
