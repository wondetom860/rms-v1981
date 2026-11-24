<?php

namespace frontend\modules\AcStEva\controllers;

use Yii;
use frontend\modules\AcStEva\models\AseCriteriaBundle;
use frontend\modules\AcStEva\models\AseCriteriaBundleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AseCriteriaBundleController implements the CRUD actions for AseCriteriaBundle model.
 */
class AseCriteriaBundleController extends Controller
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
                       'roles' => ['Admin'],
                   ],
                   [
                       'actions' => ['index','show-criteria-detail'],
                       'allow' => true,
                       'roles' => ['Registrar','AcademicDean','DepartmentHead','Student','Technical Assistant'],
                   ],
               ]
           ]
        ];
    }

    public function actionShowCriteriaDetail($bundleId){
        return $this->renderPartial('__report/criteria-in-bundle',['model' => $this->findModel($bundleId)]);
    }

    /**
     * Lists all AseCriteriaBundle models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AseCriteriaBundleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AseCriteriaBundle model.
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
     * Creates a new AseCriteriaBundle model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($typeid)
    {
        $model = new AseCriteriaBundle();

        $model->eval_type_relId = $typeid;
        if ($model->load(Yii::$app->request->post())) {
            $ex = $model->checkIfExists();
            if ($ex && count($ex) > 0) {
                // var_dump($ex);die();
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','Such entry already exists with code ('.$ex->code.'), if you want to add new criteria under this type, click <a href="/AcStEva/ase-criteria-bundle/update?id='.$ex->id.'">here</a<');
                return $this->redirect(['/AcStEva/ase-evaluation-type/view','id' => $model->eval_type_relId]);
            }
            $model->created_by = Yii::$app->user->id;
            $model->getBundleCode();
            $model->created_at = time();
            $model->getData();

            if ($model->save()) {
                Yii::$app->session->setFlash('type','success');
                Yii::$app->session->setFlash('message','Record registered successfully');
                return $this->redirect(['/AcStEva/ase-evaluation-type/view','id' => $model->eval_type_relId]);
            }

            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','Record registering failed, ERROR: '.json_encode($model->getErrors()));
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AseCriteriaBundle model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $ex = $model->checkIfExists($model->id);
            if ($ex && count($ex) > 0) {
                // var_dump($ex);die();
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','Such entry already exists with code ('.$ex->code.'), if you want to add new criteria under this type, click <a href="/AcStEva/ase-criteria-bundle/update?id='.$ex->id.'">here</a<');
                return $this->redirect(['/AcStEva/ase-evaluation-type/view','id' => $model->eval_type_relId]);
            }
            $model->updated_by = Yii::$app->user->id;
            // $model->getBundleCode();
            $model->updated_at = time();
            $model->getData();

            if ($model->save()) {
                Yii::$app->session->setFlash('type','success');
                Yii::$app->session->setFlash('message','Record updated successfully');
                return $this->redirect(['/AcStEva/ase-evaluation-type/view','id' => $model->eval_type_relId]);
            }

            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','Record registering failed, ERROR: '.json_encode($model->getErrors()));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AseCriteriaBundle model.
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
     * Finds the AseCriteriaBundle model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AseCriteriaBundle the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AseCriteriaBundle::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
