<?php

namespace frontend\modules\ExamSchedule\controllers;

use Yii;
use frontend\modules\ExamSchedule\models\ExInvigilators;
use frontend\modules\ExamSchedule\models\ExInvigilatorsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ExInvigilatorsController implements the CRUD actions for ExInvigilators model.
 */
class ExInvigilatorsController extends Controller
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

    public function actionRemoveInvigilator()
    {
        $exroomslotid = Yii::$app->request->post('exroomslotid');
        $satffid = Yii::$app->request->post('satffid');

        $inv = ExInvigilators::find()
            ->where([
                'exam_room_slot_relId' => $exroomslotid,
                'staff_relId' => $satffid
            ])
            ->all();
        if (count($inv) > 1) {
            return "Found more than one assignment...";
        }else{
            foreach ($inv as $invigilator) {
                $invigilator->delete();
            }

            return 1;
        }
    }

    /**
     * Lists all ExInvigilators models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExInvigilatorsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExInvigilators model.
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

    public function actionCreate2()
    {
        $model = new ExInvigilators();
        $model->exam_room_slot_relId = Yii::$app->request->post('roomSlotId');
        if ($model->load(Yii::$app->request->post())) {
            $totalCount = count(json_decode($model->staffs, true));
            $message = $model->createInvigilators();
            $assigned = count($model->examRoomSlotRel->exInvigilators);
            if ($totalCount == $message[0]) {
                Yii::$app->getSession()->setFlash('success', 'All staff assigned as invigilator for this exam room slot.');
            } else {
                Yii::$app->getSession()->setFlash('error', 'Assigning for some staff failed, Reason: ' . $message[1]);
            }
            return $this->redirect(['/ExamSchedule/ex-exam-room-slot/view', 'id' => $model->exam_room_slot_relId]);
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new ExInvigilators model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ExInvigilators();
        $model->exam_room_slot_relId = Yii::$app->request->post('roomSlotId');
        if ($model->load(Yii::$app->request->post())) {
            var_dump($model);
            exit;
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'Added invigilator(s) for exam room slot.');
            } else {
                Yii::$app->getSession()->setFlash('error', 'Adding invigilators failed, Error: ' . json_encode($model->getErrors()));
            }
            return $this->redirect(['/ExamSchedule/ex-exam-room-slot', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ExInvigilators model.
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
     * Deletes an existing ExInvigilators model.
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
     * Finds the ExInvigilators model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExInvigilators the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExInvigilators::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
