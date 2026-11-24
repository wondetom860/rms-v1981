<?php

namespace frontend\modules\Reassessment\controllers;

use backend\models\ReassessmentHistory;
use Yii;
use frontend\modules\Reassessment\models\ReassessorMark;
use frontend\modules\Reassessment\models\ReassessorMarkSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReassessorMarkController implements the CRUD actions for ReassessorMark model.
 */
class ReassessorMarkController extends Controller
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
                        'roles' => ['Admin'],
                    ],
                    [
                        'actions' => ['index', 'view', 'create-batch'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['DepartmentHead'],
                    ],
                    [
                        'actions' => ['approve-changed-mark'],
                        'allow' => true,
                        'roles' => ['Instructor'],
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all ReassessorMark models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReassessorMarkSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReassessorMark model.
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

    public function actionCreateBatch()
    {
        $ras_id = Yii::$app->request->post('ras_id');
        $marks = Yii::$app->request->post('marks');
        $marks = json_decode($marks);
        // $rasModel = ReassessmentHistory::findOne($ras_id);
        if ($marks) {
            $transaction = Yii::$app->db->beginTransaction();
            foreach ($marks as $id => $mark) {
                $model = $this->findModel($id);
                $model->ras_changed_to = $mark ? $mark : $model->ras_original_mark;
                if(!$model->validMarl()){
                    $transaction->rollBack();
                    return "Input mark is invalid.";
                }
                $model->updated_at = time();
                $model->updated_by = Yii::$app->user->id;
                $model->getData();

                if ($model->save()) {
                    continue;
                } else {
                    Yii::$app->db->beginTransaction()
                        ->update('reassessor_mark', ['ras_changed_to' => $mark], ['id' => $id])
                        ->execute();
                }
            }
            $transaction->commit();
            return 1;
        } else {
            return "Invalid data";
        }
    }

    /**
     * Creates a new ReassessorMark model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ReassessorMark();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ReassessorMark model.
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
     * Deletes an existing ReassessorMark model.
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
     * Finds the ReassessorMark model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReassessorMark the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReassessorMark::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
