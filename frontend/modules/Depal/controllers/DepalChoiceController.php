<?php

namespace frontend\modules\Depal\controllers;

use Yii;
use frontend\modules\Depal\models\DepalChoice;
use frontend\modules\Depal\models\DepalCategorizedCandidates;
use frontend\modules\Depal\models\DepalSetting as setting;
use frontend\modules\Depal\models\DepalChoiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DepalChoiceController implements the CRUD actions for DepalChoice model.
 */
class DepalChoiceController extends Controller
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
                       'actions' => ['index', 'update', 'view', 'run-allotting','delete'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
                   [
                       'actions' => ['index', 'update', 'delete','view','run-allotting'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Registrar','AcademicDean'],
                   ],
                   [
                       'actions' => ['create','index','get-dept-selection-form','update','view'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Student'],
                   ]
               ]
           ]
        ];
    }

    public function actionGetDeptSelectionForm(){
        $categorizedId = isset($_POST['categorizedId']) && !empty($_POST['categorizedId']) ? intval($_POST['categorizedId']) : die('Invalid request');
        $categorizedModel = DepalCategorizedCandidates::findOne($categorizedId);
        $model = new DepalChoice();
        $std = \backend\models\Student::findOne(['std_semester_status' => 'Active','std_user_relId' => Yii::$app->user->id]);
        if ($std) {
            $model->std_relId = $std->id;
            $model->setting_relId = $categorizedModel->setting_relId;
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
        
    }

    public function actionRunAllotting(){
        $settingId = isset($_GET['settingid']) && !empty($_GET['settingid']) ? intval($_GET['settingid']) : die('Invalid request');
        $settingModel = setting::findOne($settingId);

        if ($settingModel->getScheduleStatus(true) == 'Closed') {
            $depalChoice = new DepalChoice();
            return $depalChoice->runnAllign($settingModel);
        }else{
            echo " Schedule is not closed";
            return;
        }
    }

    /**
     * Lists all DepalChoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepalChoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DepalChoice model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->user->can('Student')) {
            $std = \backend\models\Student::find()->where(['std_semester_status' => 'Active','std_user_relId' => Yii::$app->user->id])->one();
            if ($std && ($model->std_relId == $std->id)) {

            }else{
                \Yii::$app->session->setFlash('type','error');
                \Yii::$app->session->setFlash('message','Detail informtion is available only to the owner.');
                return $this->redirect('index');
            }
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new DepalChoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DepalChoice();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $model->created_by = \Yii::$app->user->id;
            $model->getData();
            if ($model->save()) {
                \Yii::$app->session->setFlash('type','success');
                \Yii::$app->session->setFlash('message',"Department choice registered successfully");
                return $this->redirect('index');
            }else{
                \Yii::$app->session->setFlash('type','error');
                \Yii::$app->session->setFlash('message',json_encode($model->getErrors()));
                return $this->redirect('index');
            }
            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DepalChoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        // var_dump($model->settingRel->getScheduleStatus());die();
        if ($model->finalizad || ($model->settingRel->status != 0)) {
            \Yii::$app->session->setFlash('type','error');
            \Yii::$app->session->setFlash('message','Modification is not allowed on finalized/expired record.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if (Yii::$app->user->can('Student')) {
            $std = \backend\models\Student::find()->where(['std_semester_status' => 'Active','std_user_relId' => Yii::$app->user->id])->one();

            if ($std && ($model->std_relId == $std->id)) {
                
            }else{
                \Yii::$app->session->setFlash('type','error');
                \Yii::$app->session->setFlash('message','Modification is not allowed, record does not belong to you.');
                return $this->redirect('index');
            }
        }

        if ($model->load(Yii::$app->request->post())) {

            $model->updated_at = time();
            $model->updated_by = \Yii::$app->user->id;
            $model->getData();
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }            
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DepalChoice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->finalizad || ($model->settingRel->status != 0)) {
            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','Deleting is not allowed on finalized/expired record.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DepalChoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DepalChoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DepalChoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
