<?php

namespace frontend\controllers;

use Yii;
use backend\models\WithdrawalReason;
use backend\models\WithdrawalReasonSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WithdrawalReasonController implements the CRUD actions for WithdrawalReason model.
 */
class WithdrawalReasonController extends Controller
{
    /**
     * @inheritdoc
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
            'access' => [
               'class' => \yii\filters\AccessControl::className(),
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::className(),
               ],
               'rules' => [
                   [
                       'actions' => ['index','create', 'update', 'delete','clslvllists','get-types','view'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin','DepartmentHead'],
                   ],
                    [
                       'actions' => ['clslvllists','clslvllistss','get-types'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['?'],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all WithdrawalReason models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WithdrawalReasonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WithdrawalReason model.
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
     * Creates a new WithdrawalReason model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WithdrawalReason();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionGetTypes(){
        $catagoryName = isset($_POST['catagoryName']) && !empty($_POST['catagoryName'])?intval($_POST['catagoryName']):die('Invalid input.');

        $repType = WithdrawalReason::findOne($catagoryName);
        $catagory = WithdrawalReason::find()->where(['withdrawalres_type'=>$repType->withdrawalres_type])->all();

        $options = "";
        if ($catagory && count($catagory) > 0) {
            $options .= "<option value=''> - </option>";
            foreach ($catagory as $key) {
                $options .= "<option value='{$key->id}'> {$key->withdrawalres_description} </option>";
            }
        }

        return $options;
    }

    /**
     * Updates an existing WithdrawalReason model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing WithdrawalReason model.
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
     * Finds the WithdrawalReason model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WithdrawalReason the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WithdrawalReason::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
