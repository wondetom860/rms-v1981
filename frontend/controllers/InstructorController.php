<?php

namespace frontend\controllers;

use backend\models\CourseOffering2Detail;
use Yii;
use backend\models\Instructor;
use backend\models\InstructorSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\User;
use Mpdf\Mpdf as mPDF;

/**
 * InstructorController implements the CRUD actions for Instructor model.
 */
class InstructorController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'delete', 'instlist2', 'instlist', 'view', 'send-evaluation-report-email'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Admin'],
                    ],

                    [
                        'actions' => ['index', 'create', 'update', 'instlist2', 'instlist', 'view', 'send-evaluation-report-email'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['DepartmentHead'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all Instructor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InstructorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->user->can('DepartmentHead')) {
            $this->render('index_HoD', ['model' => new Instructor()]);
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSendEvaluationReportEmail() //send-evaluation-report-email
    {
        $offering_detail_id = Yii::$app->request->post('offeringdetailid');
        $ta = Yii::$app->request->post('ta');
        // $to_email = 
        $offeringDetail = CourseOffering2Detail::findOne($offering_detail_id);
        $mpdf = new mPDF([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P'
        ]);
        // $this->layout  = "printLayout";
        // $this->layout = 'evaluationReportPrintLayout';
        $content = $this->renderPartial('/generate-semester-evaluation-report/evaluation-report-print', ['model' => $offeringDetail, 'ta' => $ta]);
        $mpdf->WriteHTML($content); //pdf is a name of view file responsible for this pdf document
        // $pdf = Yii::$app->pdf->exportData('', '', $content);
        $full_name = $offeringDetail->coff2detInstRel->getFullName();
        $filename = 'evaluation_report_' . rand(999, 1000) . '_' . str_replace(['/', '-'], '_', $offeringDetail->getOfferingAcadYearInfo()) . '_' . $full_name . '.pdf';
        $mpdf->Output('reports/mailed_evaluation_results' . '/' . $filename, "F");
        $to_email = $offeringDetail->coff2detInstRel->instUserRel->email;
        // var_dump($to_email);
        // $fname = $mpdf->Output($filename, "I");
        if ($to_email) {
            $message = Yii::$app->mailer->compose('@common/mail/hello', ['params' => $offeringDetail, 'ta' => $ta])
                ->setFrom([Yii::$app->params['adminEmail'] => 'ETDU - Registrar & Alumni Directorate'])
                ->setTo($to_email)
                ->setSubject('Academic Staff Semester Evaluation Report');

            $message->attach('reports/mailed_evaluation_results' . '/' . $filename, ['fileName' => $filename, 'contentType' => 'application/pdf']);

            // message ID header (hide admin panel)
            $msgId = md5(time()) . '@edu.et';
            if ($message->send()) {
                Yii::$app->session->setFlash('success', 'Email Sent!');
            } else {
                Yii::$app->session->setFlash('error', 'Sending Email failed, Please try again');
            }
            return $this->redirect(['/instructor/view', 'id' => $offeringDetail->coff2det_inst_relId]);
        }
    }

    /**
     * Displays a single Instructor model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    // returns the list of instructors in the selected department

    public function actionInstlist()
    {
        if (isset($_POST['deptId'])) {
            $deptId = $_POST['deptId'];

            $insts = Instructor::find()->where(['inst_dep_relId' => $deptId])->all();

            if (!empty($insts)) {
                echo "<option value=''>TBA</option>";
                foreach ($insts as $inst) {
                    $usr = \backend\models\User::findOne(['id' => $inst['inst_user_relId']]);

                    echo "<option value='" . $inst['id'] . "'>" . $usr->first_name . ' ' . $usr->middle_name . "</option>";
                }
            } else {
                echo "<option value='' title='No instructor found in this dept.'> - </option>";
            }
        }
        die();
    }

    public function actionInstlist2()
    {
        if (isset($_POST['deptId'])) {
            $deptId = $_POST['deptId'];
            $instType = isset($_POST['instType']) && !empty($_POST['instType']) ? $_POST['instType'] : NULL;


            $insts = Instructor::find()->where(['inst_dep_relId' => $deptId])->all();

            if (!is_null($instType)) {
                if ($instType == 'TA') {
                    $insts = Instructor::find()
                        ->innerJoin('user', 'user.id = instructor.inst_user_relId')
                        ->where(['inst_dep_relId' => $deptId])
                        ->innerJoin('academic_rank', 'academic_rank.id = instructor.inst_acq_relId')
                        ->andWhere(['acr_name' => ['Technical Assistant', 'Graduate Assistant II', 'Technical Assistant -III', 'Technical Assistant -II', 'Technical Assistant -I']])
                        ->andWhere(['user.activated' => 1])
                        ->andWhere(['user.status' => User::STATUS_ACTIVE])
                        ->all();
                } else {
                    $insts = Instructor::find()
                        ->innerJoin('user', 'user.id = instructor.inst_user_relId')
                        ->where(['inst_dep_relId' => $deptId])
                        ->innerJoin('academic_rank', 'academic_rank.id = instructor.inst_acq_relId')
                        ->andWhere(['user.activated' => 1])
                        ->andWhere(['user.status' => User::STATUS_ACTIVE])
                        ->all();
                }
            }

            if (!empty($insts)) {
                echo "<option value=''> - </option>";
                foreach ($insts as $inst) {
                    $usr = \backend\models\User::findOne(['id' => $inst['inst_user_relId']]);

                    echo "<option  title='{$inst->instDepRel->dept_name}' value='" . $inst['id'] . "'>" . $usr->first_name . ' ' . $usr->middle_name . "</option>";
                }
            } else {
                echo "<option value='' title='No instructor found in this dept.'> - </option>";
            }
        }
        die();
    }

    /**
     * Creates a new Instructor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Instructor();
        $model->instUserRelId = $id;

        if ($model->load(Yii::$app->request->post())) {

            if (Yii::$app->user->can('DepartmentHead')) {
                $instDep = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
                if ($model->inst_dep_relId != $instDep->inst_dep_relId) {
                    Yii::$app->session->setFlash('message', 'You can Only register instructors of your department.');
                    return $this->render('create', [
                        'model' => $model,
                    ]); //Model already contains the instUserRelId/
                }
            }
            // calconv.
            $modelUser = User::findOne($id);

            $model->inst_created_by = Yii::$app->user->identity->id;
            $model->inst_updated_by = $model->inst_created_by;
            $model->inst_created_at = date('Y-m-d');
            $model->inst_updated_at = $model->inst_created_at;
            $model->inst_active = 1;
            if ($model->save()) {
                echo "<script type='text/javascript'>
                        alert('User account create successful, Please collect your OTP to check account activation status. Your OTP is  {$modelUser->OTP}');
                    </script>";
                return $this->redirect('index');
            } else {
                print_r($model->errors);
                die();
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]); //Model already contains the instUserRelId/
        }
    }

    /**
     * Updates an existing Instructor model.
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
     * Deletes an existing Instructor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {

        $model = $this->findModel($id);

        if ($model->courseOffering2Details) {
            Yii::$app->session->setFlash('type', 'error');
            Yii::$app->session->setFlash('message', 'This record has course offering entry, and it can NOT be removed.');
        } else {
            $model->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Instructor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Instructor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Instructor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
