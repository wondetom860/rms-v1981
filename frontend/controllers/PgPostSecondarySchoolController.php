<?php

namespace frontend\controllers;

use Yii;
use backend\models\PgPostSecondarySchool;
use backend\models\PgPostSecondarySchoolSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\ModelPGPSSE;

use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * PgPostSecondarySchoolController implements the CRUD actions for PgPostSecondarySchool model.
 */
class PgPostSecondarySchoolController extends Controller
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
         
        ];
    }

    /**
     * Lists all PgPostSecondarySchool models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PgPostSecondarySchoolSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PgPostSecondarySchool model.
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
     * Creates a new PgPostSecondarySchool model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $models = [new PgPostSecondarySchool()];


        $posts = Yii::$app->request->post();

        // var_dump($posts);die();

        $eiId =intval( $posts['PgPostSecondarySchool'][0]['ppss_pei_relId']);


        $models = ModelPGPSSE::createMultiple(PgPostSecondarySchool::classname());
        ModelPGPSSE::loadMultiple($models, Yii::$app->request->post());
        $flag = false;
        // validate all models
        $valid = ModelPGPSSE::validateMultiple($models);

        if ($valid) {
            $transaction = Yii::$app->db->beginTransaction();
            $ids = [];
            try {
                foreach ($models as $post12) {
                    $post12['ppss_pei_relId'] = $eiId;
                    // $post12['pgpss_date_of_award'] = date_format('Y-m-d h:i:s',$post12['pgpss_date_of_award']);
                    if (strtotime($post12['pgpss_date_of_award']) === false) {
                        $transaction->rollBack();
                        $flag = false;
                        die('Invalid year Provided on Secondary School attended tab.');
                    }
                    if (! ($flag = $post12->save())) {
                        $err = json_encode($post12->getErrors());
                        die("{$err}");
                    }
                    $ids[] = $post12->id;
                }

                if ($flag) {
                    echo json_encode([1,$ids]);
                    // $transaction->commit();
                    die();
                }
            } catch (\Exception $e) {
                die($e->getMessage());
            }
        }else{
            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validateMultiple($models);
            }
        }

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->id]);
        // } else {
        //     return $this->render('create', [
        //         'model' => $model,
        //     ]);
        // }
    }

    public function actionValidate(){
        // $model = [new PostSecondaryEducation()];

        $models = ModelPGPSSE::createMultiple(PgPostSecondarySchool::classname());
        ModelPGPSSE::loadMultiple($models, Yii::$app->request->post());
        if (Yii::$app->request->isAjax && $models) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validateMultiple($models);
            }
    }

    /**
     * Updates an existing PgPostSecondarySchool model.
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
     * Deletes an existing PgPostSecondarySchool model.
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
     * Finds the PgPostSecondarySchool model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PgPostSecondarySchool the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PgPostSecondarySchool::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
