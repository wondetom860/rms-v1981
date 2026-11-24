<?php

namespace frontend\controllers;

use Yii;
use backend\models\PostSecondaryEducation;
use backend\models\PostSecondaryEducationSeasrch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\ModelPSSE;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * PostSecondaryEducationController implements the CRUD actions for PostSecondaryEducation model.
 */
class PostSecondaryEducationController extends Controller
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
               'class' => yii\filters\AccessControl::className(),
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => yii\filters\AccessRule::className(),
               ],
               'rules' => [
                   [
                       'actions' => ['index','create', 'update', 'delete'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all PostSecondaryEducation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PostSecondaryEducationSeasrch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PostSecondaryEducation model.
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
     * Creates a new PostSecondaryEducation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $models = [new PostSecondaryEducation()];

        // 
        // 
        $posts = Yii::$app->request->post();

        $eiId =isset($posts['PostSecondaryEducation'][0]['pse_ei_relId'])?intval($posts['PostSecondaryEducation'][0]['pse_ei_relId']):die('Invalid form, Please review...');

        // echo "<pre>";
        // var_dump($_POST);
        // die();

        if (isset($posts['EnteranceExamMark']['attendedPSE']) && intval($posts['EnteranceExamMark']['attendedPSE']) == 1) {
           
        }else{
            echo json_encode([1,NULL]);die();
        }


        $models = ModelPSSE::createMultiple(PostSecondaryEducation::classname());
        ModelPSSE::loadMultiple($models, Yii::$app->request->post());
        $flag = false;
        // validate all models
        $valid = ModelPSSE::validateMultiple($models);


        // echo "<pre>"; var_dump($_POST);die();

        if ($valid) {
            $transaction = Yii::$app->db->beginTransaction();
            $ids = [];
            try {
                foreach ($models as $post12) {
                    $post12['pse_ei_relId'] = $eiId;
                    $post12['pse_to_year'] = intval($post12['pse_to_year']);
                    $post12['pse_from_year'] = intval($post12['pse_from_year']);
                    $post12['pse_CGPA_obtained'] = floatval($post12['pse_CGPA_obtained']);
                    $post12['pse_nationality_relId'] = intval($post12['pse_nationality_relId']);
                    $post12['pse_credit_earned'] = floatval($post12['pse_credit_earned']);
                    // $post12['pse_created_by'] = Yii::$app->user->identity->id;
                    $post12['pse_created_at'] = date('Y-m-d h:i:s');
                    
                    if (strtotime($post12['pse_to_year']) === false || strtotime($post12['pse_from_year']) === false) {
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
        // $model = [new PostSecondaryEducation()];

        $models = ModelPSSE::createMultiple(PostSecondaryEducation::classname());
        ModelPSSE::loadMultiple($models, Yii::$app->request->post());
        if (Yii::$app->request->isAjax && $models) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validateMultiple($models);
            }
    }

    /**
     * Updates an existing PostSecondaryEducation model.
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
     * Deletes an existing PostSecondaryEducation model.
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
     * Finds the PostSecondaryEducation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PostSecondaryEducation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PostSecondaryEducation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
