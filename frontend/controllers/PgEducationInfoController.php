<?php

namespace frontend\controllers;

use Yii;
use backend\models\PgEducationInfo;
use backend\models\PgEducationInfoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * This is the model class for table "pg_education_info".
 *
 * @property integer $id
 * @property string $pgei_UG_university_name
 * @property double $pgei_CGPA
 * @property string $pgei_field_of_study
 * @property string $pgei_major
 * @property string $pgei_minor
 * @property string $pgei_work_experience
 * @property string $pgei_reserches
 * @property string $pgei_awards
 *
 * @property PgPostSecondarySchool[] $pgPostSecondarySchools
 * @property PgSecondarySchoolsAttended[] $pgSecondarySchoolsAttendeds
 */

// use frontend\models\ModelPGEI;
// use yii\web\Response;
// use yii\widgets\ActiveForm;
/**
 * PgEducationInfoController implements the CRUD actions for PgEducationInfo model.
 */
class PgEducationInfoController extends Controller
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
     * Lists all PgEducationInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PgEducationInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PgEducationInfo model.
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
     * Creates a new PgEducationInfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PgEducationInfo();

        if ($model->load(Yii::$app->request->post())) {

            $transaction = Yii::$app->db->beginTransaction();
            $workExperience = $model->pgei_reserches;

            $_SESSION['WORK_EXPERIENCE']  = $model->pgei_work_experience;
            $_SESSION['RESEARCHES'] = $model->pgei_reserches;
            $_SESSION['AWARDS'] = $model->pgei_awards;

            $model->pgei_awards  ='pgei_awards_'.date('Y-m-d @ hiI');
            $model->pgei_work_experience  ='pgei_work_experience_'.date('Y-m-d @ hiI');
            $model->pgei_reserches  ='pgei_research_'.date('Y-m-d @ hiI');
            // save experiences to the file.

            if ($model->save()) {
                $transaction->commit();
                echo json_encode([1,$model->id]);
                die();
            }else{
                var_dump($model->getErrors());
                die();
            }
        }else{
            var_dump(Yii::$app->request->post());
        }
    }

    /**
     * Updates an existing PgEducationInfo model.
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
     * Deletes an existing PgEducationInfo model.
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
     * Finds the PgEducationInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PgEducationInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PgEducationInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
