<?php

namespace frontend\modules\Reassessment\controllers;

use frontend\modules\Reassessment\models\ReassessmentCommittee;
use Yii;
use frontend\modules\Reassessment\models\ReassessmentHistory;
use frontend\modules\Reassessment\models\ReassessmentHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ReassessmentHistoryController implements the CRUD actions for ReassessmentHistory model.
 */
class ReassessmentHistoryController extends Controller
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
                        'roles' => ['Admin', 'Registrar', 'AcademicDean'],
                    ],
                    [
                        'actions' => ['index', 'view', 'accept-mark-change', 'upload-image', 'read-scan-file', 'form-committee', 'submit-ra-mark', 'list-committee-member', 'request-form', 'request-report', 'request-form-print', 'request-report-print'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['DepartmentHead'],
                    ],
                    [
                        'actions' => ['index', 'view','accept-mark-change'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Instructor'],
                    ]
                ]
            ]
        ];
    }

    public function actionAcceptMarkChange(){
        $commitee_id = Yii::$app->request->post('commitee_id');
        $rc_model = ReassessmentCommittee::findOne($commitee_id);
        $model = $rc_model->rasRel;
        $rc_model->approval_status = 1;
        $rc_model->approved_at = time();
        $rc_model->getData();

        if($rc_model->save()){
            return 1;
        }else{
            return json_encode($rc_model->errors);
        }
    }

    public function actionReadScanFile()
    {
        $ras_id = Yii::$app->request->get('ras_id');
        $model = $this->findModel($ras_id);
        return $this->renderPartial('__reports/read-upload-image', ['model' => $model]);
    }

    public function actionUploadImage()
    {
        $ras_id = Yii::$app->request->get('ras_id');
        $model = $this->findModel($ras_id);
        if ($model->load(Yii::$app->request->post())) {
            $model->image = UploadedFile::getInstance($model, 'image');
            $model->ras_report_scan = file_get_contents($model->image->tempName);
            $model->ras_scan_file_ext = $model->image->extension;

            if ($model->validate() && $model->save()) {
                Yii::$app->session->setFlash('success', 'Reassessment report file attached successfully.');
            } else {
                Yii::$app->session->setFlash('danger', 'Report file uploading failed.');
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->renderAjax('/reassessment-history/_upload-image', ['model' => $model]);
    }

    // submit-ra-mark
    public function actionSubmitRaMark()
    {
        $ras_id = Yii::$app->request->get('ras_id');
        $model = $this->findModel($ras_id);

        return $this->renderAjax('/reassessor-mark/_form_batch', ['model' => $model]);
    }
    // request-report-print
    public function actionRequestReportPrint()
    {
        $ras_id = Yii::$app->request->get('ras_id');
        $this->layout = "@frontend/views/layouts/printLayout";
        $model = $this->findModel($ras_id);
        return $this->render('__reports/request-report-print', ['model' => $model, 'print' => 1]);
    }
    public function actionRequestFormPrint()
    {
        $ras_id = Yii::$app->request->get('ras_id');
        $this->layout = "@frontend/views/layouts/printLayout";
        $model = $this->findModel($ras_id);
        return $this->render('__reports/request-form-print', ['model' => $model, 'print' => 1]);
    }
    public function actionRequestForm()
    {
        $ras_id = Yii::$app->request->get('ras_id');
        // $this->layout = "@frontend/views/layouts/printLayout";
        $model = $this->findModel($ras_id);
        return $this->renderPartial('__reports/request-form-print', ['model' => $model]);
    }

    public function actionListCommitteeMember()
    {
        $ras_id = Yii::$app->request->post('ras_id');
        $model = $this->findModel($ras_id);
        return $this->renderPartial('/reassessment-history/__reports/reviewing_committee', ['model' => $model]);
    }

    /**
     * Lists all ReassessmentHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReassessmentHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReassessmentHistory model.
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
     * Creates a new ReassessmentHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ReassessmentHistory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ReassessmentHistory model.
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
     * Deletes an existing ReassessmentHistory model.
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
     * Finds the ReassessmentHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReassessmentHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReassessmentHistory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}