<?php

namespace frontend\modules\Reassessment\controllers;

use Yii;
use frontend\modules\Reassessment\models\ReassessmentCommittee;
use frontend\modules\Reassessment\models\ReassessmentCommitteeSearch;
use yii\web\Controller;
use backend\models\Instructor;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReassessmentCommitteeController implements the CRUD actions for ReassessmentCommittee model.
 */
class ReassessmentCommitteeController extends Controller
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
                        'roles' => ['Admin', 'Registrar', 'AcademicDean'],
                    ],
                    [
                        'actions' => ['index', 'view', 'form-committee','delete'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['DepartmentHead'],
                    ]
                ]
            ]
        ];
    }

    public function actionFormCommittee($ras_id) //form-committee
    {
        $ras_id = Yii::$app->request->get('ras_id');
        $model = new ReassessmentCommittee();
        $insts = [];
        if (Yii::$app->user->can('DepartmentHead')) {
            $inst = Instructor::find()->where(['inst_user_relId' => Yii::$app->user->id])->one();
            $insts = $inst->instDepRel ? $inst->instDepRel->instructors : [];
        }
        $model->ras_relId = $ras_id;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->exists()) {
                return "Already in the member list!";
            }
            $model->created_at =  time();
            $model->created_by = Yii::$app->user->id;
            $model->getData(); //serialize

            if ($model->save()) {
                return 1;
            } else {
                return json_encode($model->getErrors());
            }
        }
        return $this->renderPartial(
            '_form_batch',
            ['model' => $model, 'insts' => $insts]
        );
    }

    /**
     * Lists all ReassessmentCommittee models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReassessmentCommitteeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReassessmentCommittee model.
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
     * Creates a new ReassessmentCommittee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ReassessmentCommittee();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ReassessmentCommittee model.
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
     * Deletes an existing ReassessmentCommittee model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        if (Yii::$app->request->isAjax) {
            return 1;
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the ReassessmentCommittee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReassessmentCommittee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReassessmentCommittee::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
