<?php

namespace frontend\controllers;

use Yii;
use backend\models\GraduateStudentPointHistory;
use backend\models\GraduateStudent;
use backend\models\GraduateStudentRegistrationEntryHistory;
use backend\models\GraduateStudentPointHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

// ALTER TABLE `graduate_student_point_history` CHANGE `gph_credit_hour_enrolled` `gph_credit_hour_enrolled` INT(2) NULL, CHANGE `gph_average_point` `gph_average_point` FLOAT(7,3) NULL;



/**
 * GraduateStudentPointHistoryController implements the CRUD actions for GraduateStudentPointHistory model.
 */
class GraduateStudentPointHistoryController extends Controller
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
                       'actions' => [],
                       'allow' => true,
                       'roles' => ['@'],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all GraduateStudentPointHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GraduateStudentPointHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GraduateStudentPointHistory model.
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
     * Creates a new GraduateStudentPointHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GraduateStudentPointHistory();

        if ($model->load(Yii::$app->request->post())) {
            // var_dump($model);die();
            // $model->gph_gstd_relId = isset($_SESSION['gph_gstd_relId']) ? $_SESSION['gph_gstd_relId'] : NULL;
            $model->created_at = date("Y-m-d H:i:s");
            $model->created_by = Yii::$app->user->id;

            $model->data = $model->getData($model);
            if ($model->save()) {
                return 1;
            }
            var_dump($model->getErrors());die();
            // // $model->save()
            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionGetForm(){
        if (isset($_POST['gstdId']) && !empty($_POST['gstdId'])) {
            $gstdId = intval($_POST['gstdId']);

            $modelGstd = GraduateStudent::findOne($gstdId);

            if ($modelGstd) {
                if (isset($modelGstd->gstd_adm_year) && $modelGstd->gstd_grad_year) {
                    // $_SESSION['gph_gstd_relId'] = $gstdId;
                    // distribute registration history entries. 
                    // I have year of admission in pattern of eg. 2019/20
                    // this is to be splitted as [0] => 2019, [1] => 20
                    // and I want [0] => 2019 as admission year and count up to grad_year[0]-III.

                    $modelRgIds = $this->distributeRegEntries($modelGstd);

                    if (is_string($modelRgIds)) {
                        // throw if the distribution faces any error!
                        return $modelRgIds;
                    }else if (is_array($modelRgIds)) {
                        $forms = "";
                        foreach ($modelRgIds as $key) {
                            $model = new GraduateStudentPointHistory();
                            $model->gph_gstd_relId = $gstdId;
                            $model->gph_registration_entry_relId = $key;

                            $exists = $model->find()
                                ->innerJoin('graduate_student_registration_entry_history','graduate_student_registration_entry_history.id = graduate_student_point_history.gph_registration_entry_relId')
                                ->where([
                                    'graduate_student_registration_entry_history.gphr_acadYear' => $model->gphRegistrationEntryRel->gphr_acadYear,
                                    'gph_registration_entry_relId' => $key,
                                    'gph_gstd_relId' => $gstdId,
                                ])
                                ->one();

                            if($exists){ continue;}

                            $forms .= $this->renderPartial('includes/_gpoint-entry-form',['model' => $model]);
                            unset($model);
                        }
                        return $forms;
                    }
                    return "UN_EXPECTED_ERROR_ENCOUNTERED, Please contact developers!";

                }else{
                    return "Graduation History Entry is NOT complete, Please complete it first!.";
                }
            }else{
                return "Graduation History Entry is EMPTY!.";
            }
        }
    }

    private function distributeRegEntries($modelGstd){
        $ids = [];
        if (strtoupper($modelGstd->gstdAdmRel->admission_name) == 'REGULAR') {
           $semester = ['I','II']; 
        }else{
            $semester = ['I','II','III'];
        }
        
        $admYear = explode('/', $modelGstd->gstd_adm_year);
        $gradYear = explode('/', $modelGstd->gstd_grad_year);
        if (($admYear && is_array($admYear)) && ($gradYear && is_array($gradYear))) {
            $admYear = $admYear[0];
            $gradYear = $gradYear[0];
            if (is_numeric($admYear) && is_numeric($gradYear)) {
                $admYear = min($admYear,$gradYear);
                while ($admYear <= $gradYear) {
                    foreach ($semester as $key) {
                        $modelGphr = new GraduateStudentRegistrationEntryHistory();
                        $modelGphr->gphr_acadYear = $admYear.'-'.$key;
                        $modelGphr->gphr_semester = $key;
                        $exists = $modelGphr->find()->where(['gphr_acadYear' => $admYear.'-'.$key])->one();

                        if ($exists) { $ids[] = $exists->id; unset($modelGphr); continue;}

                        $modelGphr->created_by = Yii::$app->user->id;
                        $modelGphr->created_at = date("Y-m-d H:i:s");

                        $modelGphr->data = $modelGphr->getData($modelGphr);

                        if ($modelGphr->save()) {
                            $ids[] = $modelGphr->id;
                            unset($modelGphr); continue;
                        }
                        var_dump($modelGphr->getErrors());die();
                    }

                    $admYear++;
                }
                return $ids;
            }else{
              return "Graduation Year and Admission Year should be written in the form as 2019/20";  
            }
        }else{
            return "Graduation Year and Admission Year should be written in the form as 2019/20";
        }
    }

    /**
     * Updates an existing GraduateStudentPointHistory model.
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
     * Deletes an existing GraduateStudentPointHistory model.
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
     * Finds the GraduateStudentPointHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GraduateStudentPointHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GraduateStudentPointHistory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
