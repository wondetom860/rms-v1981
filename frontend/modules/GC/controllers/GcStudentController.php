<?php

namespace frontend\modules\GC\controllers;

use frontend\modules\GC\models\GcEntry;
use Yii;
use frontend\modules\GC\models\GcStudent;
use frontend\modules\GC\models\GcStudentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GcStudentController implements the CRUD actions for GcStudent model.
 */
class GcStudentController extends Controller
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
                        'actions' => ['create', 'view', 'index'],
                        'allow' => true,
                        'roles' => ['Student'],
                    ],
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['Admin', 'Registrar'],
                    ],
                    [
                        'actions' => ['index', 'view', 'unapproved-requests'],
                        'allow' => true,
                        'roles' => ['Registrar'],
                    ]
                ]
            ]
        ];
    }

    public function actionApproveGcStudentsBatch()
    {
        $gc_id = Yii::$app->request->post('gc_id');
        $option = Yii::$app->request->post('option');

        $option = $option == 2 ? NULL : 1;
        
        if (Yii::$app->user->can('Registrar')) {
            $model = GcEntry::findOne(['id' => $gc_id]);
            foreach ($model->gcStudents as $gcStd) {
                $gcStd->approve($option);
            }
            return 1;
        }
    }

    public function actionApproveGcStudents()
    { //approve-gc-students
        $gc_id = Yii::$app->request->post('gc_id');
        $option = Yii::$app->request->post('option');

        $option = $option == 2 ? NULL : 1;

        $model = $this->findModel($gc_id);
        $resp = $model->approve($option);
        if ($resp == 1) {
            Yii::$app->session->setFlash('success', 'Approval Status change successfull.');
        }

        return $resp;
    }

    /**
     * Lists all GcStudent models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GcStudentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GcStudent model.
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
     * Creates a new GcStudent model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GcStudent();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing GcStudent model.
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
     * Deletes an existing GcStudent model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->reg_approved == 1) {
            return "You can NOT delete approved record.";
        }
        $student = $model->stdRel;
        $student->std_semester_status = 'ACTIVE';
        if ($student->save(false)) {
            return $model->delete();
        } else {
            return "Deleting Entry failed, Error: " . json_encode($student->errors);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the GcStudent model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GcStudent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GcStudent::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
