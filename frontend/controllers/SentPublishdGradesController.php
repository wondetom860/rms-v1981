<?php

namespace frontend\controllers;

use backend\models\SemesterRegistration2;
use Mpdf\Mpdf as mPDF;
use Yii;
use backend\models\SentPublishdGrades;
use backend\models\SentPublishdGradesSearch;
use kartik\mpdf\Pdf;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\Response;

/**
 * SentPublishdGradesController implements the CRUD actions for SentPublishdGrades model.
 */
class SentPublishdGradesController extends Controller
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
                // We will override the default rule config with the new AccessRule class
                'ruleConfig' => [
                    'class' => \yii\filters\AccessRule::class,
                ],
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Registrar'],
                    ],
                    [
                        'actions' => ['index','view','update','delete','generate-semester-grade-report','refresh-email','import-student-record'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Admin'],
                    ],
                ]
            ]
        ];
    }

    public function actionGenerateSemesterGradeReport()
    {
        $this->layout = "printLayout";
        $sgp_id = Yii::$app->request->get('id');
        $model = $this->findModel($sgp_id);
        $this->generateGradeReportStatementPDF($model, $model->semregRel, $model->semregRel->semesterRegistration2Details);
        return $this->render('__report/semreg-details', ['model' => $model->semregRel, 'srd2s' => $model->semregRel->semesterRegistration2Details]);
    }

    public function actionSendEmail()
    {
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        $semreg = $model->semregRel;
        $srd2s = $semreg->semesterRegistration2Details;

        $to_email = $model->recipient_email;
        if ($model->grade_report_path && file_exists($model->grade_report_path)) {
            $filePath = $model->grade_report_path;
        } else {
            $filePath = $this->generateGradeReportStatementPDF($model, $semreg, $srd2s);
        }
        Yii::$app->response->on(Response::EVENT_AFTER_SEND, function ($event) use ($to_email, $model, $filePath) {
            if ($to_email) {
                $model->sendPublishedSemesterreportEmail($to_email, $filePath, $model->semregRel->semregStdRel->getFullName(), $model->semregRel->semregAcadYearRel->sch_year_desc, $model->semregRel->semregSemesterRel->semester_name);
                $model->mail_sent = 1;
                $model->date_sent = time();
                $model->grade_report_path = $filePath;
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Email sent successfully');
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to update the record after sending email, Error: ' . json_encode($model->getErrors()));
                }
            } else {
                Yii::$app->session->setFlash('error', 'Recipient email is not available for this student');
            }
        });
    }

    public function actionRefreshEmail()
    {
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        $semreg = $model->semregRel;
        $ecInfo = $semreg->semregStdRel->emergencyContactInfo;
        if ($ecInfo && $ecInfo->ec_email) {
            $model->recipient_email = $ecInfo->ec_email;
            $model->relation = $ecInfo->ec_relationShip;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Recipient email refreshed successfully');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to refresh recipient email, Error: ' . json_encode($model->getErrors()));
            }
        } else {
            Yii::$app->session->setFlash('error', 'Emergency contact email is not available for this student');
        }
    }

    public function generateGradeReportStatementPDF($model, $semreg, $srd2s)
    {
        $saveDir = Yii::getAlias('@webroot/uploads/mailed_grade_statements');
        FileHelper::createDirectory($saveDir);
        $full_name = $semreg->semregStdRel->getFullName();
        $acy = "{$semreg->semregAcadYearRel->sch_year_desc}-{$semreg->semregSemesterRel->semester_name}";
        $filePath = $saveDir . '/grade_stmt_' . '_' . strtolower(str_replace(['/', '-'], '_', $acy . '_' . $full_name)) . '.pdf';

        $content = $this->renderPartial('__report/semreg-details', ['model' => $model->semregRel, 'srd2s' => $srd2s]);

        $watermarkPath = Yii::getAlias('@webroot/logo/etdu.png');

        $headerHtml = '
            <div style="border-bottom: 1px solid #cccccc; padding-bottom: 5px; font-family: sans-serif;">
                <table width="100%">
                    <tr>
                        <td width="50%" style="font-size: 14px; font-weight: bold; color: #333333;">
                            ' . strtoupper(Yii::$app->params['client']) . '
                        </td>
                        <td width="50%" style="text-align: right; font-size: 11px; color: #666666;">
                            Official Document #' . md5($model->id) . '
                        </td>
                    </tr>
                </table>
            </div>';
        // 4. Configure the PDF component to save to a file
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_FILE,
            'filename' => $filePath,
            'content' => $content,
            'marginTop' => 25,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Document #' . md5($model->id), 'showWatermarkImage' => true,],
            'methods' => [
                'SetHeader' => ['Generated on ' . date('dMY')],
                'SetHTMLHeader' => [$headerHtml],
                'SetWatermarkImage' => [
                    $watermarkPath, // Absolute path to image
                    0.22,           // Transparency (0.0 to 1.0)
                    [100, 100],            // Size: 'D' (default), 'P' (page), 'F' (fit), or [width, height] in mm
                    [60, 60],            // Position: 'P' (page center) or [x, y] in mm
                ],
                'SetFooter' => ['Page {PAGENO}'],
            ]
        ]);

        // 5. Generate and save the PDF to disk
        $pdf->render();

        $model->grade_report_path = $filePath;
        $model->save(false, ['grade_report_path']);
        return $filePath;
    }

    // approve-registration-toggle
    public function actionApproveRegistrationToggle()
    {
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        if ($model->reg_approved == 1) {
            $model->reg_approved = 0;
            Yii::$app->session->setFlash('success', 'Registration approval revoked successfully');
        } else {
            $model->reg_approved = 1;
            Yii::$app->session->setFlash('success', 'Registration approved successfully');
        }
        if ($model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            Yii::$app->session->setFlash('error', 'Failed to update registration approval status');
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    public function actionImportStudentRecord()
    {
        $semregIds = Yii::$app->request->post('ids');
        $semregIds = json_decode($semregIds, true);
        if ($semregIds && is_array($semregIds)) {
            foreach ($semregIds as $semregId) {
                $semreg = \backend\models\SemesterRegistration2::findOne($semregId);
                $semregs = SemesterRegistration2::find()
                    ->where(['semreg_std_relId' => $semreg->semreg_std_relId])
                    ->all();
                foreach ($semregs as $sreg) {
                    if ($sreg) {
                        $model = new SentPublishdGrades();
                        $model->semreg_relId = $sreg->id;
                        $model->date_published = time();
                        $ecInfo = $sreg->semregStdRel->emergencyContactInfo;
                        if ($ecInfo && $ecInfo->ec_email) {
                            $model->recipient_email = $ecInfo->ec_email;
                            $model->relation = $ecInfo->ec_relationShip;
                        } else {
                            $model->recipient_email = null;
                            $model->relation = null;
                        }
                        if ($model->save()) {
                            // record saved successfully
                        } else {
                            Yii::$app->session->setFlash('error', 'Failed to save record for student: ' . $sreg->semregStdRel->getFullName());
                        }
                    }
                }
            }
            Yii::$app->session->setFlash('success', 'Selected student records imported successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'No student records selected for import.');
        }

        return $this->redirect(['index']);
    }

    public function actionLoadPublishedGrades()
    {
        $model = new SentPublishdGrades();
        $result = $model->loadPublishedGrades();

        // if ($result['success']) {
        //     Yii::$app->session->setFlash('success', $result['message']);
        // } else {
        //     Yii::$app->session->setFlash('error', $result['message']);
        // }

        return $this->renderPartial('__report/published-grades', ['response' => $result]);
    }

    /**
     * Lists all SentPublishdGrades models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SentPublishdGradesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SentPublishdGrades model.
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

    /**
     * Creates a new SentPublishdGrades model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SentPublishdGrades();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SentPublishdGrades model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SentPublishdGrades model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SentPublishdGrades model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SentPublishdGrades the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SentPublishdGrades::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
