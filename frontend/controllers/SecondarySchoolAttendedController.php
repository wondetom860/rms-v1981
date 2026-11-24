<?php

namespace frontend\controllers;

use Yii;
use backend\models\SecondarySchoolAttended;
use backend\models\SecondarySchoolAttendedSearch;
use backend\models\Student;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use frontend\models\ModelSSA;
use yii\widgets\ActiveForm;


/**
 * This is the model class for table "secondary_school_attended".
 *
 * @property integer $id
 * @property integer $ssa_ei_relId
 * @property string $ssa_school_name
 * @property string $ssa_town
 * @property string $ssa_year
 * @property string $ssa_gradeLevels
 * @property integer $ssa_created_by
 * @property string $ssa_created_at
 * @property integer $ssa_updated_by
 * @property string $ssa_updated_at
 *
 * @property User $ssaCreatedBy
 * @property User $ssaUpdatedBy
 * @property EducationInformation $ssaEiRel
 */
/**
 * SecondarySchoolAttendedController implements the CRUD actions for SecondarySchoolAttended model.
 */
class SecondarySchoolAttendedController extends Controller
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
        ];
    }

    /**
     * Lists all SecondarySchoolAttended models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SecondarySchoolAttendedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SecondarySchoolAttended model.
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
     * Creates a new SecondarySchoolAttended model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $models = [new SecondarySchoolAttended()];


        $posts = Yii::$app->request->post();

        $eiId =intval( $posts['SecondarySchoolAttended'][0]['ssa_ei_relId']);


        $models = ModelSSA::createMultiple(SecondarySchoolAttended::class);
        ModelSSA::loadMultiple($models, Yii::$app->request->post());
        $flag = false;
        // validate all models
        $valid = ModelSSA::validateMultiple($models);

        if ($valid) {
            $transaction = Yii::$app->db->beginTransaction();
            $ids = [];
            try {
                foreach ($models as $post12) {
                    $post12['ssa_ei_relId'] = $eiId;
                    $post12['ssa_year'] = intval($post12['ssa_year']);
                    if (strtotime($post12['ssa_year']) === false) {
                        $transaction->rollBack();
                        $flag = false;
                        die('Invalid year Provided on Secondary School attended tab.');
                    }
                    $post12['ssa_gradeLevels'] = json_encode($post12['ssa_gradeLevels']);
                    // echo "<pre>";var_dump($post12);die();
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

        $models = ModelSSA::createMultiple(SecondarySchoolAttended::class);
        ModelSSA::loadMultiple($models, Yii::$app->request->post());
        
        if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validateMultiple($models);
            }
    }

    /**
     * Updates an existing SecondarySchoolAttended model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->user->can('Admin')) {
            $model = $this->findModel($id);
            $model->ar_request = Yii::$app->request->get('ar_request');
            if ($model->load(Yii::$app->request->post())) {
                if ($model->suchExists($model->id)) {
                    Yii::$app->session->setFlash('warning', 'Such record already registered.');
                } else {
                    $model->ssa_updated_at = date('Y-m-d H:i:s');
                    $model->ssa_updated_by = Yii::$app->user->identity->id;
                    if ($model->save()) {
                        Yii::$app->session->setFlash('success', 'Update successful');
                    } else {
                        Yii::$app->session->setFlash('warning', 'Update failed: ' . json_encode($model->errors));
                    }
                }
                $modelStd_id = $model->ssa_ei_relId;
                $modelStd_id = Student::findOne(['st_educ_info_relId' => $modelStd_id])->id;
                return $model->ar_request ?
                    $this->redirect(['/student/view', 'id' => $modelStd_id]) :
                    $this->redirect(['index']);
            } else {
                return $model->ar_request ? $this->renderAjax('update', [
                    'model' => $model,
                ]) : $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    public function actionDeleteByEid(){
        $eid = isset($_POST['eid']) && !empty($_POST['eid'])?intval($_POST['eid']):die('No parameter provided.');

        $result = SecondarySchoolAttended::find()->where(['ssa_ei_relId'=>$eid])->all()->delete();

        echo $result;die();
    }

    /**
     * Deletes an existing SecondarySchoolAttended model.
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
     * Finds the SecondarySchoolAttended model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SecondarySchoolAttended the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SecondarySchoolAttended::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
