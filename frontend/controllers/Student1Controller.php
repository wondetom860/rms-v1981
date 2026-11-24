<?php

namespace frontend\controllers;

use Yii;
use backend\models\Student1;
use backend\models\Student1Search;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use \common\components\CommonControllerFunctions as CCF;
use yii\widgets\ActiveForm;
use common\models\User1;

/**
 * Student1Controller implements the CRUD actions for Student1 model.
 */
class Student1Controller extends Controller
{

    public $acadYearId;
    public $semId;

    protected function __constructor() {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
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
                       'actions' => [],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
                   [
                       'actions' => ['create','index','view'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Student',],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all Student1 models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new Student1Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Student1 model.
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
     * Creates a new Student1 model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Student1();
        $modelUser = new User1();

        if ($model->load(Yii::$app->request->post())) {
            $this->__constructor();

            $model->std_regId = $model->getStdRegId($model->std_admission_relId,$model->std_prog_relId);
            $model->std_acadYear_relId = $this->acadYearId;

            $p = \backend\models\Program::findOne($model->std_prog_relId);
            if (strtoupper($p->program_name) == 'MASTERS') {
                $model->std_clslvl_relId = 6;
            }else{
                $model->std_clslvl_relId = 1;
                $model->std_dept_relId = NULL;
            }
            $model->std_created_at = date('Y-m-d h:i:s');
            // $model->std_created_by = Yii::$app->user->identity->id;
            if (!is_null($model->std_dept_relId)) {
                if ($modelUser->UploadAttachments($model) && $model->save()) {
                    echo json_encode([1,$model->id]);
                    return;
                }else{
                    echo "<pre>";
                    var_dump($model->getErrors());
                    die();
                }
            }else{
                if ($model->save()) {
                    echo json_encode([1,$model->id]);
                    return;
                }else{
                    echo "<pre>";
                    var_dump($model->getErrors());
                    die();
                }
            }  
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Student1 model.
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
     * Deletes an existing Student1 model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Student1 model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Student1 the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Student1::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
