<?php

namespace frontend\modules\AcStEva\controllers;

use frontend\modules\AcStEva\models\AseEvaluationControlSetting;
use Yii;
use frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule;
use frontend\modules\AcStEva\models\AseDepartmentEvaluationScheduleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AseDepartmentEvaluationScheduleController implements the CRUD actions for AseDepartmentEvaluationSchedule model.
 */
class AseDepartmentEvaluationScheduleController extends Controller
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
                       'actions' => ['index'],
                       'allow' => true,
                       'roles' => ['Admin'],
                   ],[
                       'actions' => ['create'],
                       'allow' => false,
                       'roles' => ['Admin'],
                   ],
                   [
                       'actions' => ['index','create','view','extend-schedule'],
                       'allow' => true,
                       'roles' => ['DepartmentHead'],
                   ],[
                       'actions' => ['index','show-department-plans'],
                       'allow' => true,
                       'roles' => ['Registrar','AcademicDean'],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all AseDepartmentEvaluationSchedule models.
     * @return mixed
     */

    public function actionExtendSchedule(){
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
        if ($model->dept_relId != $inst->inst_dep_relId) {
            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','You cannot extend this record.');
            return $this->redirect('index');
        }
        if ($model->extendSchedule()) {
            Yii::$app->session->setFlash('type','success');
            Yii::$app->session->setFlash('message','Schedule extended successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        Yii::$app->session->setFlash('type','error');
        Yii::$app->session->setFlash('message','Failed to extend schedule.');
        return $this->redirect(['view', 'id' => $model->id]);
    }
    public function actionIndex()
    {
        $searchModel = new AseDepartmentEvaluationScheduleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AseDepartmentEvaluationSchedule model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
        if ($model->dept_relId != $inst->inst_dep_relId) {
            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','Invalid request...');
            return $this->redirect('index');
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new AseDepartmentEvaluationSchedule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($sevid)
    {
        $model = new AseDepartmentEvaluationSchedule();
        $model->header_relId = $sevid;
        $model->getHoD();
        if ($model->load(Yii::$app->request->post())) {

            $ex = $model->checkIfExists();
            if ($ex) {
                // var_dump($ex);die();
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','Such entry already exists , if you want to add new criteria under this type, click <a href="/AcStEva/ase-deaprtment-evaluation-schedule/update?id='.$ex->id.'">here</a<');
                return $this->redirect(['/AcStEva/ase-deaprtment-evaluation-schedule/view','id' => $ex->id]);
            }
            $model->created_at = time();
            $model->created_by = Yii::$app->user->id;
            $model->start_date = strtotime($model->startdate);
            $model->end_date = strtotime($model->enddate);
            if (!$model->validDateInterval()) {
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','Please check schedule dates'.json_encode($model));
                return $this->redirect(['/AcStEva/ase-semester-evaluation/view','id' => $model->header_relId]);
            }
            $model->getData();

            if ($model->save()) {
                (new AseEvaluationControlSetting())->generateControlEntries($model->id,$model);
                Yii::$app->session->setFlash('type','success');
                Yii::$app->session->setFlash('message','Record registered successfully');
                return $this->redirect(['view', 'id' => $model->id]);
            }
            // 
            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','Record registering failed, ERROR: '.json_encode($model->getErrors()));
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AseDepartmentEvaluationSchedule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
        if ($model->dept_relId != $inst->inst_dep_relId) {
            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','You cannot update this record.');
            return $this->redirect('index');
        }
        if ($model->load(Yii::$app->request->post())) {

            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->id;
            $model->start_date = strtotime($model->startdate);
            $model->end_date = strtotime($model->enddate);
            if (!$model->validDateInterval()) {
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','Please check schedule dates'.json_encode($model));
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            $model->getData();
            if ($model->save()) {
                (new AseEvaluationControlSetting())->generateControlEntries($model->id,$model);
                Yii::$app->session->setFlash('type','success');
                Yii::$app->session->setFlash('message','Record Updated successfully');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','Please check schedule dates'.json_encode($model));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AseDepartmentEvaluationSchedule model.
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
     * Finds the AseDepartmentEvaluationSchedule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AseDepartmentEvaluationSchedule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AseDepartmentEvaluationSchedule::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
