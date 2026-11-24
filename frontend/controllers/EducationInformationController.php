<?php

namespace frontend\controllers;

use Yii;
use backend\models\EducationInformation;
use backend\models\EducationInformationSeasrch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * This is the model class for table "education_information".
 *
 * @property integer $id
 * @property string $ei_G10_doc_scan
 * @property string $ei_G12_doc_scan
 * @property integer $ei_enteranceMarkPoint
 * @property string $ei_created_at
 * @property string $ei_updated_at
 * @property integer $ei_created_by
 * @property integer $ei_updated_by
 *
 * @property User $eiCreatedBy
 * @property User $eiUpdatedBy
 * @property EnteranceExamMark[] $enteranceExamMarks
 * @property PostSecondaryEducation[] $postSecondaryEducations
 * @property SecondarySchoolAttended[] $secondarySchoolAttendeds
 */
/**
 * EducationInformationController implements the CRUD actions for EducationInformation model.
 */
class EducationInformationController extends Controller
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
            'access' => [
               'class' => yii\filters\AccessControl::class,
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => yii\filters\AccessRule::class,
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
     * Lists all EducationInformation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EducationInformationSeasrch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EducationInformation model.
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
     * Creates a new EducationInformation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EducationInformation();

        if ($model->load(Yii::$app->request->post())) {

            $model->ei_created_at = date('Y-m-d h:i:s');
            // $model->ei_created_by = Yii::$app->user->identity->id;

            if ($model->save()) {
                echo json_encode([1,$model->id]);
            }else{
                echo "<p class='bg-danger'>Error in your Education Information tab, Please review it..</p>";
                die();
            }
            //  && $model->save()
            // return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EducationInformation model.
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
     * Deletes an existing EducationInformation model.
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
     * Finds the EducationInformation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EducationInformation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EducationInformation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
