<?php

namespace frontend\modules\AcStEva\controllers;

use Yii;
use frontend\modules\AcStEva\models\AseEvaluationResult;
use frontend\modules\AcStEva\models\AseEvaluationResultSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AseEvaluationResultController implements the CRUD actions for AseEvaluationResult model.
 */
class AseEvaluationResultController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['AcademicDean'],
                    ],
                    [
                        'actions' => ['index', 'view', 'create'],
                        'allow' => true,
                        'roles' => ['Registrar', 'DepartmentHead', 'Admin'],
                    ],
                    [
                        'actions' => ['index', 'create'],
                        'allow' => true,
                        'roles' => ['Student', 'Instructor','Technical Assistant'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all AseEvaluationResult models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AseEvaluationResultSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AseEvaluationResult model.
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
     * Creates a new AseEvaluationResult model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($settingId)
    {
        $model = new AseEvaluationResult();
        $model->loadPrevResult();
        $model->control_setting_relId = $settingId;
        $model->criteria_bundle_relId = $model->controlSettingRel->deptPlanRel->criteria_bundle_relId;
        $model->dept_plan_relId = $model->controlSettingRel->dept_plan_relId;

        $uid = Yii::$app->user->id;
        if (!in_array($uid, json_decode($model->controlSettingRel->evaluators))) {
            Yii::$app->session->setFlash('type', 'error');
            Yii::$app->session->setFlash('message', 'You are not in the list of elligible evaluators for this candidate staff.');
            return "You are not in the list of elligible evaluators for this candidate staff.";
        }

        if ($model->load(Yii::$app->request->post())) {
            // check if all points rated
            // var_dump(json_decode($model->eval_point_rate), count(json_decode($model->eval_point_rate, 1)), $model->getApplicableBundlesCount());
            // exit;

            // check if all zero/NA evaluated:
            $eavlPoitns = json_decode($model->eval_point_rate, true);
            if (count($eavlPoitns) == 0 || count(array_filter($eavlPoitns, function ($v) {
                return $v != 0;
            })) == 0) {
                // Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('error', 'Please rate all criteria properly.');
                return $this->redirect(['/AcStEva/default/index']);
            }  
            
            
            /**
             * @var \frontend\modules\AcStEva\models\AseEvaluationResult $ex
             */


            if (count(json_decode($model->eval_point_rate, true)) != $model->getApplicableBundlesCount()) { //$model->criteria_bundle_IDS
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'Please rate all criteria.');
                return "Please rate all criteria.";
            }
            // check if such entry exists!
            $ex = $model->checkIfExists();
            if ($ex) {
                // check if the current user has already evaluated the staff
                if (in_array($uid, json_decode($ex->completed_staffs))) {
                    Yii::$app->session->setFlash('type', 'error');
                    Yii::$app->session->setFlash('message', 'You have already evaluated this staff.');
                    return "You have already evaluated this staff.";
                }
                $ex->completed_staffs = json_encode(array_merge(json_decode($ex->completed_staffs), [$uid]));
                $model->computeAverage();
                $ex->average_total += $model->average_total;
                $ee = json_decode($ex->eval_point_rate, true);
                $ex->placeratings($eavlPoitns);
                $ex->eval_point_rate = json_encode(array_merge($ee ? $ee : [], json_decode($model->eval_point_rate, true)));
                // $ex->eval_point_rate .= $model->eval_point_rate;
                $ex->computepercentage();
                $ex->computeAverageLast();
                $ex->updated_by = $uid;
                $ex->updated_at = time();
                // $ex->status = 0//indicates the evaluation process has been started.
                $ex->getData();
                if ($ex->update()) {
                    // set evaluated_flag to one for related semreg_detail
                    $ex->markEvaluated();
                    Yii::$app->session->setFlash('type', 'success');
                    Yii::$app->session->setFlash('message', 'Evaluation rates submitted succesfully.');
                    return $this->redirect(['/AcStEva/default/index']);
                } else {
                    return "Error while storing evaluation information, " . json_encode($ex->getErrors());
                }
            } else { //create entry for the first evluator
                $model->completed_staffs = json_encode([$uid]);
                $model->computeAverage();
                // $model->average_total += $model->average_total;
                $model->computepercentage();
                $model->created_by = Yii::$app->user->id;
                $model->created_at = time();
                $model->status = 0; //indicates the evaluation process has been started.
                $model->getData();

                if ($model->save()) {
                    $model->markEvaluated();
                    $model->placeratings(json_decode($model->eval_point_rate,true));
                    Yii::$app->session->setFlash('type', 'success');
                    Yii::$app->session->setFlash('message', 'Evaluation rates submitted succesfully.');
                    return $this->redirect(['/AcStEva/default/index']);
                } else {
                    return "Error while storing evaluation information, " . json_encode($model->getErrors());
                }
            }
            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AseEvaluationResult model.
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
     * Deletes an existing AseEvaluationResult model.
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
     * Finds the AseEvaluationResult model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AseEvaluationResult the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AseEvaluationResult::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
