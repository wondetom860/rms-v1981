<?php

namespace frontend\controllers;

use Yii;
use backend\models\MakeupExamReason;
use backend\models\MakeupExamReasonSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MakeupExamReasonController implements the CRUD actions for MakeupExamReason model.
 */
class MakeupExamReasonController extends Controller
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
               'class' => \yii\filters\AccessControl::class,
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::class,
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
     * Lists all MakeupExamReason models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MakeupExamReasonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new MakeupExamReason();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$model,
        ]);
    }


    public function actionMerlists(){
        $dhs = MakeupExamReason::find()->all();
            if (empty($dhs)) {
                echo "<p class='bg-danger'>No entry found.</p>";
            }else{
                echo "<table class='table table-sm table-striped table-condensed table-bordered'><thead>
                    <th>#</th>
                    <th>Makeup Exam Reason</th>
                    <th>Remark</th>                 
                </thead><tbody>";
                $count = 0;
                foreach ($dhs as $dh) {
                    echo "<tr>
                            <td>". ++$count ."</td>
                            <td>". $dh['mer_desc'] ."</td>
                            <td>". $dh['remark'] ."</td>
                        </tr>";
                }
                echo "</tbody></table>";
            }
            die();
    }
    /**
     * Displays a single MakeupExamReason model.
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
     * Creates a new MakeupExamReason model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MakeupExamReason();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MakeupExamReason model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MakeupExamReason model.
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
     * Finds the MakeupExamReason model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MakeupExamReason the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MakeupExamReason::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
