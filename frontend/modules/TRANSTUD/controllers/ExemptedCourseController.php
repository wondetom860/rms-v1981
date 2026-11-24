<?php

namespace frontend\modules\TRANSTUD\controllers;

use backend\models\Course;
use Yii;
use frontend\modules\TRANSTUD\models\ExemptedCourse;
use frontend\modules\TRANSTUD\models\ExemptedCourseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ExemptedCourseController implements the CRUD actions for ExemptedCourse model.
 */
class ExemptedCourseController extends Controller
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
                        'roles' => ['Admin', 'Registrar'],
                    ],
                    [
                        'actions' => ['index', 'view', 'unapproved-requests', 'approve-exempted-course'],
                        'allow' => true,
                        'roles' => ['Registrar'],
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all ExemptedCourse models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExemptedCourseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRemoveEquivCourse(){
        $courseId = Yii::$app->request->get('eCourseId');
        $equiv_course_id = Yii::$app->request->get('eqCourseId');
        $model = ExemptedCourse::findOne($courseId);
        if (!$model) {
            return json_encode(['status' => 'error', 'message' => 'Course not found']);
        }
        $equiv_course_ids = json_decode($model->equivalent_course_ids, true);
        if (!is_array($equiv_course_ids) || !in_array($equiv_course_id, $equiv_course_ids)) {
            return json_encode(['status' => 'error', 'message' => 'Equivalent course not found']);
        }
        $model->equivalent_course_ids = json_encode(array_diff($equiv_course_ids, [$equiv_course_id]));
        $model->updated_at = time();
        $model->updated_by = Yii::$app->user->identity->id;
        $model->getdata();

        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Equivalent courses removed successfully');
            return $this->redirect(['/TRANSTUD/transfer-student/view', 'id' => $model->ts_relid]);
        } else {
            // var_dump($model->getErrors());exit;
            return json_encode(['status' => 'error', 'message' => 'Failed to remove equivalent courses','errors' => $model->getErrors()]);
        }
    }

    public function actionSaveEquivalent()
    {
        $courseId = Yii::$app->request->post('courseId');
        $list = Yii::$app->request->post('list');

        if (empty($courseId) || empty($list)) {
            return json_encode(['status' => 'error', 'message' => 'Invalid request']);
        }

        $model = ExemptedCourse::findOne($courseId);
        if (!$model) {
            return json_encode(['status' => 'error', 'message' => 'Course not found']);
        }

        $equiv_course_ids = json_decode($model->equivalent_course_ids, true);

        $model->equivalent_course_ids = json_encode(array_merge($equiv_course_ids ?? [], $list));
        $model->updated_at = time();
        $model->updated_by = Yii::$app->user->identity->id;
        $model->getdata();

        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Equivalent courses saved successfully');
            return $this->redirect(['/TRANSTUD/transfer-student/view', 'id' => $model->ts_relid]);
            // return json_encode(['status' => 'success', 'message' => 'Equivalent courses saved successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to save equivalent courses']);
        }
    }

    public function actionAddEquivalent($tCourseId){
        $model = $this->findModel($tCourseId);
        
        // get all courses that are enrolled by the section
        $courses = $model->getCoursesBySection();

        return $this->renderPartial('__report/all-list',['courses' => $courses, 'model' => $model]);
    }

    /**
     * Displays a single ExemptedCourse model.
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
     * Creates a new ExemptedCourse model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($tsId)
    {
        // $modelTs = $this->findModel($tsId);
        $model = new ExemptedCourse();
        $model->ts_relid = $tsId;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->checkIfExists()) {
                return "Exempted course already exists for this transfer student.";
            }
            $model->created_at = time();
            $model->created_by = Yii::$app->user->id;
            $model->getData();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('rms', 'Exempted course created successfully.'));
                return $this->redirect(['/TRANSTUD/transfer-student/view', 'id' => $model->ts_relid]);
            } else {
                return "Failed to create exempted course. Error: " . json_encode($model->getErrors());
            }
            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ExemptedCourse model.
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
     * Deletes an existing ExemptedCourse model.
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
     * Finds the ExemptedCourse model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExemptedCourse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExemptedCourse::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
