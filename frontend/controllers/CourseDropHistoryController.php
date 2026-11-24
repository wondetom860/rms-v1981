<?php

namespace frontend\controllers;

use Yii;
use backend\models\CourseDropHistory;
use backend\models\CourseDropHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CourseDropHistoryController implements the CRUD actions for CourseDropHistory model.
 */
class CourseDropHistoryController extends Controller
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
                       'actions' => ['index','delete','change-status','get-report-print-backlog'],
                       'allow' => true,
                       'roles' => ['Admin'],
                   ],
                   [
                       'actions' => ['index','change-status','delete','get-report-print-backlog'],
                       'allow' => true,
                       'roles' => ['Registrar'],
                   ],
                   [
                       'actions' => ['index','create', 'delete','get-courses-for-add','change-status'],
                       'allow' => true,
                       'roles' => ['Student'],
                   ],
                   
                   [
                        'actions' => ['index','get-report-print-backlog'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
               ]
           ]
        ];
    }

    /**
     * Lists all CourseDropHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CourseDropHistorySearch();
        

        if (Yii::$app->user->can('Instructor')) {
            $modelSemAdvObj = new \backend\models\SemesterAdvisor();

            $instId = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);

            $gsetIds = $modelSemAdvObj->getAdvisingGsetIds($instId->id);

            if (empty($gsetIds)) {
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','You are not assigned as semester advisor in this semester.');
            }
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CourseDropHistory model.
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
     * Creates a new CourseDropHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CourseDropHistory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CourseDropHistory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CourseDropHistory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {

        $model = $this->findModel($id);

        try {
            $model->restoreBackRegistration();
            $model->delete();

        } catch (\Exception $e) {
            $model->delete();
           Yii::$app->session->setFlash('type','error'); 
           Yii::$app->session->setFlash('message',json_encode($e->getMessage())); 
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the CourseDropHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CourseDropHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CourseDropHistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
