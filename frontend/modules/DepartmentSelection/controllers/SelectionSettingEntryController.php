<?php

namespace frontend\modules\DepartmentSelection\controllers;

use Yii;
use frontend\modules\DepartmentSelection\models\SelectionSettingEntry;
use frontend\modules\DepartmentSelection\models\SelectionSettingEntrySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SelectionSettingEntryController implements the CRUD actions for SelectionSettingEntry model.
 */
class SelectionSettingEntryController extends Controller
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
                       'actions' => ['index','create', 'update', 'delete','view'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
                   [
                       'actions' => ['approve-setting','approve-setting-action'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Registrar',],
                   ],
               ]
           ]
        ];
    }

    public function actionApproveSetting(){
        return $this->render('includes/__index-approve');
    }

    public function actionApproveSettingAction(){
        $settingId = isset($_POST['settingId']) && !empty($_POST['settingId']) ? intval($_POST['settingId']) : die('INVALID_REQUEST');
        $option = isset($_POST['option']) && !empty($_POST['option']) ? intval($_POST['option']) : die('INVALID_REQUEST');

        $model = $this->findModel($settingId);

        $model->status = $option == 1 ? $option : NULL;
        $model->approved_at = date("Y-m-d h:i:s");
        $model->approved_by = Yii::$app->user->identity->id;

        $model->data = $model->getData();

        if ($model->update()) {
            return 1;
        }

        var_dump($model->getErrors());
        die();
    }

    /**
     * Lists all SelectionSettingEntry models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SelectionSettingEntrySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SelectionSettingEntry model.
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
     * Creates a new SelectionSettingEntry model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SelectionSettingEntry();

        if ($model->load(Yii::$app->request->post())) {

            $model->created_at = date('Y-m-d h:i:s');
            $model->created_by = Yii::$app->user->identity->id;

            $model->data = $model->getData();

            if ($model->save()) {
                return $this->redirect(['index']);;
            }else{
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message',json_encode($model->getErrors()));
                return $this->redirect(['index']);
            }

            // var_dump($model->getErrors());die();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SelectionSettingEntry model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            if ($model->status == 1) {
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','You can NOT update approved setting entry, Please contact registrar head for the issue.');
                return $this->redirect(['index']);
            }

            $model->updated_at = date('Y-m-d h:i:s');
            $model->updated_by = Yii::$app->user->identity->id;

            $model->data = $model->getData();
            
            if ($model->save()) {
                return $this->redirect(['index']);
            }else{
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message',json_encode($model->getErrors()));
                return $this->redirect(['index']);
            }

            // var_dump($model->getErrors());die();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SelectionSettingEntry model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->status !== $model::STATUS_APPROVED) {
            $model->delete();
            return $this->redirect(['index']);
        }else{
            throw new \yii\web\ForbiddenHttpException(Yii::t('decris', 'Action NOT allowed for Approved Setting.'));
        }
    }

    /**
     * Finds the SelectionSettingEntry model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SelectionSettingEntry the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SelectionSettingEntry::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('decris', 'The requested page does not exist.'));
    }
}
