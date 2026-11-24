<?php

namespace frontend\modules\AuthOrg8n\controllers;

use Yii;
use frontend\modules\AuthOrg8n\models\AuthOrg8n;
use common\models\AuthOrg8nSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\web\UploadedFile;

/**
 * AuthOrg8nController implements the CRUD actions for AuthOrg8n model.
 */
class AuthOrg8nController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all AuthOrg8n models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthOrg8nSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AuthOrg8n model.
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
     * Creates a new AuthOrg8n model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AuthOrg8n();

        if ($model->load(Yii::$app->request->post())) {

            // check if any duplicate entry exists:
            if ($model->find()->count() > 0) {
                throw new \yii\web\ForbiddenHttpException("Duplicate Entry for Organization License : Please contact developers", 1);
            }

            // upload File : 

            $model->logo_file = UploadedFile::getInstance($model,'logo_file');

            $fileName = md5('logo');

            $model->logo_file->saveAs( 'uploads/logos/'.$fileName.'.'.$model->logo_file->extension );

            $model->logo = $fileName.'.'.$model->logo_file->extension;
            $model->created_at = date("Y-m-d h:i:s");
            $model->created_by = Yii::$app->user->identity->id;

            $model->data = $model->getData();

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
            throw new \Exception(json_encode($model->getErrors()), 1);
            
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AuthOrg8n model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            
            if (isset($model->logo_file) && !empty($model->logo_file)) {
                $model->logo_file = UploadedFile::getInstance($model,'logo_file');

                $fileName = md5('logo');

                $model->logo_file->saveAs( 'uploads/logos/'.$fileName.'.'.$model->logo_file->extension );

                $model->logo = $fileName.'.'.$model->logo_file->extension;
            }
            $model->updated_at = date("Y-m-d h:i:s");
            $model->updated_by = Yii::$app->user->identity->id;

            $model->data = $model->getData();

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AuthOrg8n model.
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
     * Finds the AuthOrg8n model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AuthOrg8n the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthOrg8n::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('decris', 'The requested page does not exist.'));
    }
}
