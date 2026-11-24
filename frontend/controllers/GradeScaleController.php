<?php

namespace frontend\controllers;

use yii;
use backend\models\GradeScale;
use backend\models\SettingHistoryEntry;
use backend\models\GradeScaleSearch;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use \common\components\CommonControllerFunctions as CCF;
use yii\web\NotFoundHttpException;

class GradeScaleController extends \yii\web\Controller
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
                'class' => yii\filters\AccessControl::class,
                // We will override the default rule config with the new AccessRule class
                'ruleConfig' => [
                    'class' => yii\filters\AccessRule::class,
                ],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Admin'],
                    ],
                    [
                        'actions' => ['index', 'get-report'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'get-report', 'change-lock-status', 'change-lock-status-single'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Registrar'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all GradeScale models.
     * @return mixed
     */
    public function actionIndex()
    {

        // if (Yii::$app->user->can('Registrar')) {
        //     
        // }
        if (Yii::$app->user->can('Admin') || Yii::$app->user->can('Registrar')) {
            $searchModel = new GradeScaleSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $model = new GradeScale();

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'model' => $model,
            ]);
        } else {
            return $this->render('index_report');
        }
    }

    /**
     * Displays a single GradeScale model.
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
     * Creates a new GradeScale model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GradeScale();

        if ($model->load(Yii::$app->request->post())) {

            $model->gs_created_by = $model->gs_updated_by = Yii::$app->user->identity->id;
            $model->gs_created_at = $model->gs_updated_at = date('Y-m-d h:i:s');

            $model->data = $model->getData();

            if ($model->save()) {
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('message', json_encode($model->getErrors()));
                Yii::$app->session->setFlash('type', 'error');
            }
        }

        return $this->redirect('index');
    }

    /**
     * Updates an existing GradeScale model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->gs_locked == 1) {
            Yii::$app->session->setFlash('danger', 'You Can NOT update locked grade scale!, Please contact registrar to unlock it.');
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post())) {

            $model->gs_updated_at = date('Y-m-d h:i:s');
            $model->gs_updated_by = Yii::$app->user->identity->id;
            $model->data = $model->getData();
            if ($model->save()) {
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('message', json_encode($model->getErrors()));
            Yii::$app->session->setFlash('type', 'error');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing GradeScale model.
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
     * Finds the GradeScale model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GradeScale the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GradeScale::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionChangeLockStatus()
    {
        if (Yii::$app->user->can('Registrar')) {
            $nstatus = isset($_POST['option']) && !empty($_POST['option']) ? intval($_POST['option']) : 0;

            $transaction = Yii::$app->db->beginTransaction();

            if (in_array($nstatus, [0, 1])) {
                // backup the setting entry:

                // check if it is unlock option
                if ($nstatus == 0) { //un-loack case:
                    $allGradeSetting = GradeScale::find()->all();

                    $modelSettingHistoryEntry = new SettingHistoryEntry();

                    $modelSettingHistoryEntry->created_by = Yii::$app->user->identity->id;
                    $modelSettingHistoryEntry->created_at = time();

                    try {
                        $oldFile = serialize($allGradeSetting);
                        $acadYear = $this->getAcadYear();
                        $acadYear = $acadYear[0] . '_' . $acadYear[1];
                        $filename = "reports/removed_files/grade_setting_" . $acadYear . '.wt'; //has special extension of .wt
                        $handle = fopen($filename, 'wb');
                        fwrite($handle, $oldFile);

                        $modelSettingHistoryEntry->file_path = $filename;
                        $modelSettingHistoryEntry->setting_used_upto = $acadYear;

                        $modelSettingHistoryEntry->data = $modelSettingHistoryEntry->getData();

                        if ($modelSettingHistoryEntry->save()) {
                            Yii::$app->db->createCommand()
                                ->update('grade_scale', ['gs_locked' => $nstatus])
                                ->execute();
                            $transaction->commit();
                            Yii::$app->session->setFlash('type', 'success');
                            Yii::$app->session->setFlash('message', 'Operation completed successfully');
                            return 1;
                        } else {
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('type', 'error');
                            Yii::$app->session->setFlash('message', json_encode($modelSettingHistoryEntry->getErrors()));
                            return 1;
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('type', 'error');
                        Yii::$app->session->setFlash('message', json_encode($e->getMessage()));
                        return 1;
                    }
                } else { //it is for lock operation
                    Yii::$app->db->createCommand()
                        ->update('grade_scale', ['gs_locked' => $nstatus])
                        ->execute();
                    $transaction->commit();
                    Yii::$app->session->setFlash('type', 'success');
                    Yii::$app->session->setFlash('message', 'Grade Scale Setting Locked successfully');
                    return 1;
                }
            } else {
                return "InvlalId OperTaion SelecTed";
            }
        } else {
            return "Un-privilleged Access!";
        }
    }

    public function actionChangeLockStatusSingle()
    {
        if (Yii::$app->user->can('Registrar')) {
            $nstatus = isset($_POST['option']) && !empty($_POST['option']) ? intval($_POST['option']) : 0;
            $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : 0;

            $transaction = Yii::$app->db->beginTransaction();

            if (in_array($nstatus, [0, 1])) {
                // backup the setting entry:

                // check if it is unlock option
                if ($nstatus == 0) { //un-loack case:
                    $allGradeSetting = GradeScale::findOne($id);

                    $modelSettingHistoryEntry = new SettingHistoryEntry();

                    $modelSettingHistoryEntry->created_by = Yii::$app->user->identity->id;
                    $modelSettingHistoryEntry->created_at = time();

                    try {
                        $oldFile = serialize($allGradeSetting);
                        $acadYear = $this->getAcadYear();
                        $acadYear = $acadYear[0] . '_' . $acadYear[1] . '_' . rand(999, 10000);
                        $filename = "reports/removed_files/grade_setting_" . $acadYear . '.wt'; //has special extension of .wt
                        $handle = fopen($filename, 'wb');
                        fwrite($handle, $oldFile);

                        $modelSettingHistoryEntry->file_path = $filename;
                        $modelSettingHistoryEntry->setting_used_upto = $acadYear;

                        $modelSettingHistoryEntry->data = $modelSettingHistoryEntry->getData();

                        if ($modelSettingHistoryEntry->save()) {
                            Yii::$app->db->createCommand()
                                ->update('grade_scale', ['gs_locked' => $nstatus], ['id' => $id])
                                ->execute();
                            $transaction->commit();
                            Yii::$app->session->setFlash('type', 'success');
                            Yii::$app->session->setFlash('message', 'Operation completed successfully');
                            return 1;
                        } else {
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('type', 'error');
                            Yii::$app->session->setFlash('message', json_encode($modelSettingHistoryEntry->getErrors()));
                            return 1;
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('type', 'error');
                        Yii::$app->session->setFlash('message', json_encode($e->getMessage()));
                        return 1;
                    }
                } else { //it is for lock operation
                    Yii::$app->db->createCommand()
                        ->update('grade_scale', ['gs_locked' => $nstatus], ['id' => $id])
                        ->execute();
                    $transaction->commit();
                    Yii::$app->session->setFlash('type', 'success');
                    Yii::$app->session->setFlash('message', 'Grade Scale Setting Locked successfully');
                    return 1;
                }
            } else {
                return "InvlalId OperTaion SelecTed";
            }
        } else {
            return "Un-privilleged Access!";
        }
    }

    private function getAcadYear()
    {
        $this->__constructor();

        $acadYear = \backend\models\SchoolYear::findOne($this->acadYearId);
        $semester = \backend\models\Semester::findOne($this->semId);

        return [$acadYear ? str_replace('/', '_', $acadYear->sch_year_desc) : 'AcadYear', $semester ? $semester->semester_name : 'semester'];
    }


    //change-lock-status
    public function actionGetReport()
    {
        $gs = \backend\models\GradeScale::find()
            ->where(['gs_valid' => 1])
            ->orderBy([
                'gs_forProgram' => SORT_ASC,
                'gs_gradePoint' => SORT_DESC,
                'gs_pass_status' => SORT_DESC
            ])
            ->all();

        return $this->renderPartial('__report/grade-scale-setting-report', ['gs' => $gs]);
    }
}
