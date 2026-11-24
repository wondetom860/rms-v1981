<?php

namespace frontend\controllers;

use backend\models\AddressInfo;
use Yii;
use backend\models\EmergencyContact;
use backend\models\EmergencyContactSearch;
use backend\models\Student;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * This is the model class for table "emergency_contact".
 *
 * @property integer $id
 * @property string $ec_first_name
 * @property string $ec_last_name
 * @property string $ec_relationShip
 * @property string $ec_phone_home
 * @property string $ec_phone_mobile
 * @property integer $ec_address_relId
 * @property string $ec_email
 *
 * @property AddressInfo $ecAddressRel
 */
/**
 * EmergencyContactController implements the CRUD actions for EmergencyContact model.
 */
class EmergencyContactController extends Controller
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
     * Lists all EmergencyContact models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmergencyContactSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EmergencyContact model.
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
     * Creates a new EmergencyContact model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EmergencyContact();
        $modelAddress = new AddressInfo();
        // echo "<pre>"; var_dump($_POST);die();
        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            if ($modelAddress->load(Yii::$app->request->post()) && $modelAddress->save()) {
                $model->ec_address_relId = $modelAddress->id;
            }
            if ($model->save()) {
                Student::updateAll(['std_emergency_contact_relId' => $model->id], ['id' => $model->ec_address_student_relId]);
                $transaction->commit();
                echo json_encode([1, $model->id]);
                return;
            } else {
                $transaction->rollBack();
                var_dump($model->getErrors());
                return;
            }
        } else {
            $modelAddress = new AddressInfo();
            $stdId = Yii::$app->request->get('id');
            $model->ec_address_student_relId = $stdId;
            return Yii::$app->request->isAjax ?
                $this->renderAjax('_form2', [
                    'model' => $model,
                    'modelAddress' => $modelAddress,
                    'modelStudent' => Student::findOne($stdId),
                ])
                :
                $this->render('_form2', [
                    'model' => $model,
                    'modelAddress' => $modelAddress,
                    'modelStudent' => Student::findOne($stdId),
                ]);
        }
    }

    public function actionValidate()
    {
        $model = new EmergencyContact();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    /**
     * Updates an existing EmergencyContact model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->ec_address_student_relId = Student::findOne(['std_emergency_contact_relId' => $model->id])->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $student = Student::findOne(['std_emergency_contact_relId' => $model->id]);
            return $this->redirect(['/student/view', 'id' => $student->id]);
        } else {
            return
                Yii::$app->request->isAjax ?
                $this->renderAjax('_form_partial', [
                    'model' => $model,
                ])
                :
                $this->render('update', [
                    'model' => $model,
                ]);
        }
    }

    /**
     * Deletes an existing EmergencyContact model.
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
     * Finds the EmergencyContact model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmergencyContact the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmergencyContact::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
