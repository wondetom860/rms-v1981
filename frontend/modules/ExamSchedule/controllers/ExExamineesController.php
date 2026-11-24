<?php

namespace frontend\modules\ExamSchedule\controllers;

use backend\models\CourseOffering2Detail;
use Yii;
use frontend\modules\ExamSchedule\models\ExExaminees;
use frontend\modules\ExamSchedule\models\ExExamineesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ExExamineesController implements the CRUD actions for ExExaminees model.
 */
class ExExamineesController extends Controller
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
                        'roles' => ['Admin','Registrar'],
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all ExExaminees models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExExamineesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExExaminees model.
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
     * Creates a new ExExaminees model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ExExaminees();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionSaveAssignment()
    {
        $examrommId = Yii::$app->request->post('exam_slot_relid');
        $offeringDetailId = Yii::$app->request->post('offering_detail_relid');
        $sectionId = Yii::$app->request->post('section_relid');

        $selecteds = Yii::$app->request->post('selecteds');
        $selecteds = json_decode($selecteds,true);
        $assgneds = 0;
        $total = count($selecteds);
        $message = "";
        if (!empty($selecteds)) {
            foreach ($selecteds as $index => $std) {
                $model = new ExExaminees();
                $model->exam_slot_relId = $examrommId;
                $model->section_relId = $sectionId;
                $model->std_relId = $std;
                $model->offering_detail_relId = $offeringDetailId;

                $assigned = $model->assigned($std);

                if (is_null($assigned)) {
                    $model->created_at = time();
                    $model->created_by = Yii::$app->user->id;
                    $model->getData();
                    if ($model->save()) {
                        $assgneds++;
                        $message .= $model->stdRel->getFullName() . " assigned, ";
                    } else {
                        $message .= $model->stdRel->getFullName() . " not assigned, ";
                    }
                } else {
                    $message .= $model->stdRel->getFullName() . " already assigned, ";
                }
            }
        } else {
            $message .= "No student selected";
        }

        return $assgneds == $total ? 1 : $message;
    }

    /**
     * Creates a new ExExaminees model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate2()
    {
        $model = new ExExaminees();
        $examrommId = Yii::$app->request->post('exromid');
        $offeringDetailId = Yii::$app->request->post('offeringDetailIi');
        $ooferingDetail = CourseOffering2Detail::findOne($offeringDetailId);
        $model->section_relId = $ooferingDetail->coff2detCoff2Rel->coff2_group_relId;

        $model->exam_slot_relId = $examrommId;
        $model->offering_detail_relId = $offeringDetailId;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ExExaminees model.
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
     * Deletes an existing ExExaminees model.
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
     * Finds the ExExaminees model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExExaminees the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExExaminees::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
