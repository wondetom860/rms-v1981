<?php

namespace frontend\modules\CourseAssessment\controllers;

use Yii;
use frontend\modules\CourseAssessment\models\ContAssessCatagory;
use frontend\modules\CourseAssessment\models\ContAssessCatagorySearch;
use frontend\modules\CourseAssessment\models\CourseOffering2Detail;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ContAssessCatagoryController implements the CRUD actions for ContAssessCatagory model.
 */
class ContAssessCatagoryController extends Controller
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
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Instructor', 'DepartmentHead', 'Admin'],
                    ],
                ]
            ]
        ];
    }

    public function actionGetFaculties($coff2detId)
    {
        $coffdetModel = \backend\models\CourseOffering2Detail::findOne(intval($coff2detId));
        return $this->renderPartial(
            '__report/faculty-list',
            [
                'model' => $coffdetModel
            ]
        );
    }

    public function actionGetCoordinator($coff2detId)
    {
        $model = \backend\models\CourseOffering2Detail::findOne(intval($coff2detId));
        $CourseCordinator = \backend\models\CourseCordinator::getCordinator($model->coff2det_course_relId, $model->coff2detCoff2Rel->coff2AcadYearRel->id, $model->coff2detCoff2Rel->coff2SemRel->id);
        $cc = '-';
        if ($CourseCordinator) {
            $cc = $CourseCordinator->ccInstRel->getFullName();
        }

        return $cc;
    }

    public function actionGetCategories($coff2detId)
    {
        $coffdetModel = CourseOffering2Detail::findOne(intval($coff2detId));
        if (!$coffdetModel) {
            return null;
        }
        return $this->renderPartial(
            '__report/categories',
            [
                'model' => ContAssessCatagory::find()
                    ->where([
                        'cat_acadYear_relId' => $coffdetModel->coff2detCoff2Rel->coff2_acadYear_relId,
                        'cat_sem_relId' => $coffdetModel->coff2detCoff2Rel->coff2_sem_relId,
                        'cat_course_relId' => $coffdetModel->coff2det_course_relId,

                    ])
                    ->orderBy(['cat_maxPoint' => SORT_ASC])
                    ->all(),
                'coff2detModel' => $coffdetModel
            ]
        );
    }
    /**
     * Lists all ContAssessCatagory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContAssessCatagorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ContAssessCatagory model.
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
     * Creates a new ContAssessCatagory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($coff2det_id = NULL)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($coff2det_id == NULL) {
            return ['status' => 'error', 'message' => 'Invalid request'];
        }
        $coff2det_id = intval($coff2det_id);
        $model = new ContAssessCatagory();
        $coff2detModel = \backend\models\CourseOffering2Detail::findOne(intval($coff2det_id));
        $model->cat_acadYear_relId = $coff2detModel->coff2detCoff2Rel->coff2_acadYear_relId;
        $model->cat_sem_relId = $coff2detModel->coff2detCoff2Rel->coff2_sem_relId;
        $model->cat_coff_relId = $coff2detModel->id;
        $model->cat_course_relId = $coff2detModel->coff2det_course_relId;
        $model->cat_created_by = Yii::$app->user->id;
        $model->cat_created_at = date('Y-m-d H:i:s');
        // $model->cat_maxPoint = 100;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->cat_maxPoint > $model->getRemainingPoint()) {
                return "The maximum point is greater than the remaining point";
            }
            if ($model->save()) {
                return 1;
            } else {
                return $model->errors;
            }
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ContAssessCatagory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if(($model->cat_maxPoint) > ($model->_old_cat_maxPoint + $model->getRemainingPoint() )){
                return 'The possible point can NOT be more than the remaining point: '. $model->_old_cat_maxPoint;
            }
            if($model->cat_maxPoint < $model->instantaitedPoint()){
                return 'The possible point can NOT be less than Instantiated point: '. $model->instantaitedPoint();
            }
            $model->cat_updated_by = Yii::$app->user->id;
            $model->cat_updated_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                return 1;
            } else {
                return $model->errors;
            }
        }

        $model->_old_cat_maxPoint = $model->cat_maxPoint;
        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ContAssessCatagory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format =  \yii\web\Response::FORMAT_JSON;
        return $this->findModel($id)->delete();
    }

    /**
     * Finds the ContAssessCatagory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ContAssessCatagory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContAssessCatagory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
