<?php

namespace frontend\controllers;

use backend\models\ContinuousAssessment2;
use Yii;
use backend\models\RemovedSrd2;
use backend\models\RemovedSrd2Search;
use backend\models\SemesterRegistration2Detail;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RemovedSrd2Controller implements the CRUD actions for RemovedSrd2 model.
 */
class RemovedSrd2Controller extends Controller
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
                        'actions' => ['index', 'create', 'update', 'delete', 'view'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Admin',],
                    ],
                    [
                        'actions' => ['index', 'view', 'reg-approve'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Registrar',],
                    ],
                    [
                        'actions' => ['index', 'view', 'hod-approve'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['DepartmentHead',],
                    ],
                ]
            ]
        ];
    }

    public function actionRegApprove($id)
    {
        $model = $this->findModel($id);
        // var_dump(unserialize($model->cont_ass_marks));exit;

        $model->reg_approved = $model->reg_approved == 1 ? 0 : 1;
        if ($model->reg_approved == 1) {
            $model->reg_approved_at = time();
            $model->reg_approved_by = Yii::$app->user->identity->id;
        }

        if ($model->save(false)) {
            $model->refresh();
            // remove the course from semester registration details and continues assessments if any;
            if ($model->reg_approved == 1) { // on approval, remove srd2 aand contassessments
                $srd2 = unserialize($model->srd2_serialized);
                if ($srd2) {
                    $srd2Latest = \backend\models\SemesterRegistration2Detail::findOne($srd2->id);
                    if ($srd2Latest) {
                        $model->srd2_serialized = serialize($srd2Latest);
                        $continueAssessments = $srd2Latest->srd2SemregRel->getContinousAssessments()
                            ->andWhere([
                                'ca_semreg_relId' => $srd2Latest->srd2_semreg_relId,
                                'ca_std_relid' => $srd2Latest->srd2SemregRel->semreg_std_relId,
                                'ca_coff_relId' => $model->coff2det_relId,
                            ])
                            ->all();
                        $model->cont_ass_marks = serialize($continueAssessments);
                        foreach ($continueAssessments as $contAss) {
                            $contAss->delete();
                        }
                        $srd2Latest->delete();
                        $model->getData();
                        $model->save(false);
                    }
                }
                Yii::$app->session->setFlash('success', 'Course removal approved by Registrar successfully');
            } else {
                $srd2 = unserialize($model->srd2_serialized);
                $contAsses = unserialize($model->cont_ass_marks);
                $modelSrd2 = new SemesterRegistration2Detail();
                foreach ($srd2->attributes as $key => $value) {
                    $modelSrd2->$key = $value;
                }
                if ($modelSrd2->save()) {
                    foreach ($contAsses as $contAss) {
                        $modelContAss = new ContinuousAssessment2();
                        foreach ($contAss->attributes as $key => $value) {
                            $modelContAss->$key = $value;
                        }
                        if ($modelContAss->save()) {
                            continue;
                        }
                    }
                }
            }
        } else {
            Yii::$app->session->setFlash('error', 'Error while approving course removal by Registrar, Error: ' . json_encode($model->errors));
        }
        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionHodApprove($id)
    {
        $model = $this->findModel($id);
        $model->hod_approved = $model->hod_approved == 1 ? 0 : 1;
        if ($model->hod_approved == 1) {
            $model->hod_approved_at = time();
            $model->hod_approved_by = Yii::$app->user->identity->id;
        }

        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Course removal approved by HOD successfully');
        } else {
            Yii::$app->session->setFlash('error', 'Error while approving course removal by HOD, Error: ' . json_encode($model->errors));
        }
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Lists all RemovedSrd2 models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RemovedSrd2Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RemovedSrd2 model.
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
     * Creates a new RemovedSrd2 model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($srd2Id)
    {
        $model = new RemovedSrd2();
        $srd2 = \backend\models\SemesterRegistration2Detail::findOne($srd2Id);
        if ($srd2) {
            $semreg = $srd2->srd2SemregRel;
            $courseCoff2det = $srd2->srd2CoffRel
                ->getCourseOffering2Details()
                ->andWhere([
                    'coff2det_course_relId' => $srd2->srd2_course_relId
                ])
                ->one();
            if ($courseCoff2det) {
                $continueAssessments = $semreg->getContinousAssessments()
                    ->andWhere([
                        'ca_semreg_relId' => $srd2->srd2_semreg_relId,
                        'ca_std_relid' => $semreg->semreg_std_relId,
                        'ca_coff_relId' => $courseCoff2det->id,
                    ])
                    ->all();
                $model->coff2det_relId = $courseCoff2det->id;
                $model->semreg_relId = $semreg->id;
                $model->std_relId = $semreg->semreg_std_relId;
                $model->srd2_serialized = serialize($srd2);
                $model->grade_relId = $srd2->srd2_grade_relId;
                $model->total_mark = $srd2->srd2_total_mark;
                $model->cont_ass_marks = serialize($continueAssessments);
                // $model->course_relId = $srd2->srd2_course_relId;

                $model->created_at = time();
                $model->created_by = Yii::$app->user->identity->id;

                $model->getData();

                if ($model->save()) {
                    $srd2->srd2_requested_remove = 1;
                    $srd2->save(false);
                    Yii::$app->session->setFlash('success', 'Course removal requested successfully');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    Yii::$app->session->setFlash('error', 'Error while creating request, Error: ' . json_encode($model->errors));
                }
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing RemovedSrd2 model.
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
     * Deletes an existing RemovedSrd2 model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->hod_approved != 1 && $model->reg_approved != 1) {
            $srd2 = unserialize($model->srd2_serialized);
            $srd2Latest = \backend\models\SemesterRegistration2Detail::findOne($srd2->id);
            $srd2Latest->srd2_requested_remove = 0;
            $srd2Latest->save(false);
            $model->delete();
        } else {
            Yii::$app->session->setFlash('error', 'Cannot delete approved removal request.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the RemovedSrd2 model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RemovedSrd2 the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RemovedSrd2::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
