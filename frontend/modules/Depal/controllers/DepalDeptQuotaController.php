<?php

namespace frontend\modules\Depal\controllers;

use Yii;
use frontend\modules\Depal\models\DepalDeptQuota;
use frontend\modules\Depal\models\DepalSetting;
use frontend\modules\Depal\models\DepalDeptQuotaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DepalDeptQuotaController implements the CRUD actions for DepalDeptQuota model.
 */
class DepalDeptQuotaController extends Controller
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
    public function actionUpdateQuota(){
        // if request to update data_set
        if (isset($_POST['settingid']) && !empty($_POST['settingid']) && isset($_POST['data_set'])) {
            // update entries:
            $settingId = intval($_POST['settingid']);
            $data_set = json_decode($_POST['data_set']);

            $settingModel = DepalSetting::findOne($settingId);
            $qoutaIDs = $settingModel->getDepalDeptQoutas()->select('depal_dept_quota.id')->asArray()->all();

            $qoutadis = [];
            foreach ($qoutaIDs as $key => $value) {
                $qoutadis[] = $value['id'];
            }
            $rep = "";
            $result = 1;
            foreach ($data_set->$settingId as $qoid => $percentage) {
                if (in_array($qoid, $qoutadis)) {
                    $qoutaEntry = DepalDeptQuota::findOne($qoid);
                    $qoutaEntry->auto_allocated = 0;
                    $qoutaEntry->q_percentage = $percentage;

                    $qoutaEntry->updated_by = \Yii::$app->user->id;
                    $qoutaEntry->updated_at = time();

                    $qoutaEntry->getData();
                    if ($qoutaEntry->update()) {
                        // var_dump($qoutaEntry);
                    }else{
                        var_dump($qoutaEntry->getErrors());
                        $result .= "Error: ".json_encode($qoutaEntry->getErrors());
                    }
                }else{
                    $result = "Error: {$qoid} not found in the qouta entries list. ";
                }
            }
            return $result;
        }

        // if form request
        $settingId = isset($_GET['settingid']) && !empty($_GET['settingid']) ? intval($_GET['settingid']) : die("Invalid request");
        // returns list of available dept quota distribution...
        $setting = DepalSetting::findOne($settingId);

        if ($setting) {
            // $this->layout = "../../../../views/layouts/printLayout";
            return $this->renderPartial("/depal-dept-quota/update-entries",['setting' => $setting]);
        }else{
            return "No such setting entry found.";
        }
    }

    /**
     * Lists all DepalDeptQuota models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepalDeptQuotaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DepalDeptQuota model.
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
     * Creates a new DepalDeptQuota model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DepalDeptQuota();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DepalDeptQuota model.
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
     * Deletes an existing DepalDeptQuota model.
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
     * Finds the DepalDeptQuota model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DepalDeptQuota the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DepalDeptQuota::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
