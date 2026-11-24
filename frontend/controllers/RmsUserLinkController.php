<?php

namespace frontend\controllers;

use Yii;
use backend\models\RmsUserLink;
use backend\models\Person;
use backend\models\User;
use backend\models\RmsUserLinkSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RmsUserLinkController implements the CRUD actions for RmsUserLink model.
 */
class RmsUserLinkController extends Controller
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
        ];
    }

    /**
     * Lists all RmsUserLink models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RmsUserLinkSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RmsUserLink model.
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

    public function actionGetRmsUserDetail(){
        $IdNo = isset($_POST['IdNo']) && !empty($_POST['IdNo']) ? intval($_POST['IdNo']) : die('Invlaid request');

        return $this->renderPartial('rms-user-detail',['model' => User::findOne($IdNo)]);
    }

    public function actionGetHrmsUser(){
        $IdNo = isset($_POST['IdNo']) && !empty($_POST['IdNo']) ? intval($_POST['IdNo']) : die('Invlaid request');
        $user_name = isset($_POST['user_name']) && !empty($_POST['user_name']) ? $_POST['user_name'] : NULL;

        $model = null;
        if (Yii::$app->user->can('Admin')) {
            // allow search only by ID_NO
            $model = Person::find()->where(['OR',['like','MIL_ID_NO',$IdNo],['like','ID_NO',$IdNo]])->all();
        }else{
            if (is_null($user_name)) {
                $model = NULL;
            }else{
                $model = Person::find()
                    ->innerJoin('user','user.id=person.person_user_relId')
                    ->where(['OR',['like','MIL_ID_NO',$IdNo],['like','ID_NO',$IdNo]])
                    ->andWhere(['user.username' => $user_name])
                    ->all();
            }
        }

        return $this->renderPartial('__report/person-info',['model' => $model]);
    }

    /**
     * Creates a new RmsUserLink model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        $model = new RmsUserLink();

        if ($model->load(Yii::$app->request->post())) {

            $model->created_at = date("Y-m-d H:i:s");
            $model->created_by = Yii::$app->user->id;
            // $model->created_by = $model->personRel->person_user_relId;

            $model->data = $model->getData();

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message',json_encode($model->getErrors()));
            }
        }

        if (Yii::$app->user->can('Admin') && is_null($id)) {
            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','Please select a person.');
            return $this->redirect(['/user/index']);
        }

        $model->rms_user_id = $id;

        // check if already linked

        $exists = $model->find()->where(['rms_user_id' => $model->rms_user_id])->one();

        if ($exists) {
            Yii::$app->session->setFlash('type','success');
            Yii::$app->session->setFlash('message','Account already linked');
            return $this->redirect(['view', 'id' => $exists->id]);
        }

        // if (!Yii::$app->user->can('Admin')) {
            
        // }

        $instructor = \backend\models\Instructor::findOne(['inst_user_relId' => $id]);

        if ($instructor) {
            $model->rms_inst_id = $instructor->id;
        }

        $student = \backend\models\Student::findOne(['std_user_relId' => $id]);

        if ($student) {
            $model->rms_std_id = $student->id;
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing RmsUserLink model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = date("Y-m-d H:i:s");
            $model->updated_by = Yii::$app->user->id;
            $model->created_by = Yii::$app->user->id;

            $model->data = $model->getData();

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message',json_encode($model->getErrors()));
            }
            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing RmsUserLink model.
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
     * Finds the RmsUserLink model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RmsUserLink the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RmsUserLink::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
