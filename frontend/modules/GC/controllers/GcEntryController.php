<?php

namespace frontend\modules\GC\controllers;

use frontend\modules\AcademicCalendar\models\AcActivePrograms;
use Yii;
use frontend\modules\GC\models\GcEntry;
use frontend\modules\GC\models\GcEntrySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GcEntryController implements the CRUD actions for GcEntry model.
 */
class GcEntryController extends Controller
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

    public function actionGetStudentsList()
    {
        $gc_id = Yii::$app->request->post('gc_id');
        $model = $this->findModel($gc_id);

        return $this->renderPartial('__report/student_list', ['model' => $model]);
    }

    public function actionCreateGcStudents()
    {
        $gc_entry_id = Yii::$app->request->post('gc_entry_id');
        $gc_list = Yii::$app->request->post('gc_list');

        $model = $this->findModel($gc_entry_id);

        $resp = $model->createGcStudents($gc_list);
        if ($resp == 1) {
            Yii::$app->session->setFlash('success', 'Successfully Imported');
        }

        return $resp;
    }

    public function actionImportStudentsFromActiveProgram()
    {
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);

        return $this->renderPartial('__report/students-from-active-program', ['model' => $model, 'students' => $model->getStudents()]);
    }

    public function actionImportFromAcp()
    {
        $acp_id = Yii::$app->request->post('acp_id');
        $gc_acp = AcActivePrograms::find()
            ->where([
                'graduate_class' => 1,
                'gc_students_exported' => NULL,
                'id' => $acp_id,
            ])
            ->one();
        if ($gc_acp) {
            $model = new GcEntry();
            $model->acad_year_relId = $gc_acp->academic_year_relId;
            $model->admission_relId = $gc_acp->admission_relId;
            $model->program_relId = $gc_acp->program_relId;
            $model->dept_relId = $gc_acp->dept_relId;
            $model->spec_relId = $gc_acp->spec_relId;

            $model->senate_approve_date = $gc_acp->academicYearRel->end_date;
            $model->header_title = 'Graduates of ' . $gc_acp->academicYearRel->getAcHeader();

            $model->created_at = time();
            $model->created_by = Yii::$app->user->identity->id;

            $model->getData();

            if ($model->save()) {
                $gc_acp->gc_students_exported = 1;
                $gc_acp->save();
                Yii::$app->session->setFlash('success', 'GC ntry created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return json_encode($model->erorrs);
            }
        } else {
            return "Program entry NOT found";
        }
    }

    public function actionImportFromActivePrograms()
    {
        $gc_acps = AcActivePrograms::find()
            ->where([
                'graduate_class' => 1,
                'gc_students_exported' => NULL
            ])
            ->orderBy([
                'academic_year_relId' => SORT_DESC,
            ])
            ->all();
        return $this->renderPartial('__report/gc-active-programs', ['model' => $gc_acps]);
    }

    /**
     * Lists all GcEntry models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GcEntrySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GcEntry model.
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
     * Creates a new GcEntry model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GcEntry();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing GcEntry model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->senate_approve_date = strtotime($model->senate_approve_date_str);
            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->identity->id;
            $model->getData();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Updated sucessfully');
            } else {
                Yii::$app->session->setFlash('error', json_encode($model->errrors));
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return Yii::$app->request->isAjax ? $this->renderAjax('update', [
            'model' => $model,
        ]) : $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing GcEntry model.
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
     * Finds the GcEntry model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GcEntry the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GcEntry::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
