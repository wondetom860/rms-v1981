<?php

namespace frontend\controllers;

use Yii;
use backend\models\ThesisTitle;
use backend\models\ThesisTitleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ThesisTitleController implements the CRUD actions for ThesisTitle model.
 */
class ThesisTitleController extends Controller
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
                       'actions' => ['index','view'],
                       'allow' => true,
                       'roles' => ['DepartmentHead','Registrar'],
                   ],
                   [
                    'actions' => ['index','view','update','delete'],
                    'allow' => true,
                    'roles' => ['Admin'],
                ],
                   [
                       'actions' => ['index'],
                       'allow' => true,
                       'roles' => ['@'],
                   ],
                   [
                       'actions' => ['create','change-status'],
                       'allow' => true,
                       'roles' => ['Instructor','DepartmentHead'],
                   ],
               ]
           ]
        ];
    }

    public function actionChangeStatus(){
        $tkeyId = isset($_POST['tkeyId']) && !empty($_POST['tkeyId']) ? intval($_POST['tkeyId']):die('Invalid request');
        $option = isset($_POST['option']) && !empty($_POST['option']) ? intval($_POST['option']):die('Invalid request');

        $option = $option == 1 ? $option : NULL;

        $model = ThesisTitle::findOne($tkeyId);

        if (Yii::$app->user->can('Instructor') || Yii::$app->user->can('DepartmentHead') ) {
            $instructor = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
            $modelSemAdvObj = new \backend\models\SemesterAdvisor();
            $gsetIds = $modelSemAdvObj->getAdvisingGsetIds($instructor->id);

            if ($model->tt_graded) {
                return 'Thesis Title is graded, No more operation is allowed.';
            }

            if (!in_array($model->ttStdRel->std_group_realId, $gsetIds) && !Yii::$app->user->can('DepartmentHead')) {
                return 'Invalid Operation, You are NOT semester advisor for this student.';
            }else{
                $model->tt_approved = $option;
                if (Yii::$app->user->can('DepartmentHead')) {
                    $model->tt_hod_approved = $option;
                    $model->tt_hod_approved_by = Yii::$app->user->id;
                    $model->tt_hod_approved_at = date("Y-m-d");
                }
                return $model->update();
            }
        }else{
            return 'Invalid Operation, You are NOT semester advisor for this student.';
        }
    }

    /**
     * Lists all ThesisTitle models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ThesisTitleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ThesisTitle model.
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
     * Creates a new ThesisTitle model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ThesisTitle();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ThesisTitle model.
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
     * Deletes an existing ThesisTitle model.
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
     * Finds the ThesisTitle model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ThesisTitle the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ThesisTitle::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
