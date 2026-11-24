<?php

namespace frontend\controllers;

use Yii;
use backend\models\PgSecondarySchoolsAttended;
use backend\models\PgSecondarySchoolsAttendedSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use frontend\models\ModelPGSSA;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * PgSecondarySchoolsAttendedController implements the CRUD actions for PgSecondarySchoolsAttended model.
 */
class PgSecondarySchoolsAttendedController extends Controller
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
     * Lists all PgSecondarySchoolsAttended models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PgSecondarySchoolsAttendedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PgSecondarySchoolsAttended model.
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
     * Creates a new PgSecondarySchoolsAttended model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $models = [new PgSecondarySchoolsAttended()];


        $posts = Yii::$app->request->post();

        // var_dump($posts);die();

        $eiId =intval( $posts['PgSecondarySchoolsAttended'][0]['pssa_pei_relId']);


        $models = ModelPGSSA::createMultiple(PgSecondarySchoolsAttended::classname());
        ModelPGSSA::loadMultiple($models, Yii::$app->request->post());
        $flag = false;
        // validate all models
        $valid = ModelPGSSA::validateMultiple($models);

        if ($valid) {
            $transaction = Yii::$app->db->beginTransaction();
            $ids = [];
            try {
                foreach ($models as $post12) {
                    $post12['pssa_pei_relId'] = $eiId;
                    $post12['pgssa_years_from'] = intval($post12['pgssa_years_from']);
                    $post12['pgssa_years_to'] = intval($post12['pgssa_years_to']);
                    if (strtotime($post12['pgssa_years_from']) === false || strtotime($post12['pgssa_years_to']) === false) {
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
                    $transaction->commit();
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
    }

    public function actionValidate(){

        $models = ModelPGSSA::createMultiple(PgSecondarySchoolsAttended::classname());
        ModelPGSSA::loadMultiple($models, Yii::$app->request->post());
       if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validateMultiple($models);
            }
    }

    /**
     * Updates an existing PgSecondarySchoolsAttended model.
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
     * Deletes an existing PgSecondarySchoolsAttended model.
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
     * Finds the PgSecondarySchoolsAttended model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PgSecondarySchoolsAttended the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PgSecondarySchoolsAttended::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
