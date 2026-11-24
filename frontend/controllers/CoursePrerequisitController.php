<?php

namespace frontend\controllers;

use Yii;
use backend\models\CoursePrerequisit;
use backend\models\CoursePrerequisitSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
 
/**
 * CoursePrerequisitController implements the CRUD actions for CoursePrerequisit model.
 */
class CoursePrerequisitController extends Controller
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
                       'actions' => ['index','create', 'update', 'delete','view'],
                       'allow' => true,
                       'roles' => ['Admin',],
                   ],
                   [
                       'actions' => ['index'],
                       'allow' => true,
                       'roles' => ['Registrar','clerk'],
                   ],
                   [
                       'actions' => ['index'],
                       'allow' => true,
                       'roles' => ['DepartmentHead','Instructor'],
                   ],
               ]
           ]
        ];
    }


// returns the record of available pre-requisit details registerd before.
    public  function actionGetrecord(){
        $recs = CoursePrerequisit::find()->all();
        if ($recs) {
            echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
            echo "<caption><p class='bg-success'> <i class='bg-danger'>". count($recs) ."</i> Record(s).</p></caption>";

            echo "<thead>
                <th>#</th>
                <th>MCourse</th>
                <th>Preq. Course</th>
                <th>Dept</th>
                <th>Spec</th>
                </thead><tbody>";
            $c = 0;

            foreach ($recs as $rec) {
                $Mcourse = \backend\models\Course::findOne(['id'=>$rec['preq_main_course_relId']]);
                $Pcourse = \backend\models\Course::findOne(['id'=>$rec['preq_preq_course_relId']]);
                $dept = \backend\models\Department::findOne(['id'=>$rec['preq_dept_relId']]);
                $spec = \backend\models\Specialization::findOne(['id'=>$rec['preq_spec_relId']]);

                $spec = isset($spec)?$spec->spec_name:'ALL';
                $dept = isset($dept)?$dept->dept_name:'ALL';
                $preqCourse = isset($Pcourse)?$Pcourse->course_title:'N/A';

                echo "<tr>
                <td>". ++$c ."</td>
                <td>". $Mcourse->course_title ."</td>
                <td>". $preqCourse ."</td>
                <td>". $dept."</td>
                <td>". $spec."</td>
                </tr>";
            }
            echo "</tbody></table>";
        }else{
            echo "<p class='bg-warning'>No course preq. detail found.</p>";
        }
    }
    /**
     * Lists all CoursePrerequisit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CoursePrerequisitSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new CoursePrerequisit();
        if (Yii::$app->user->can('DepartmentHead') || Yii::$app->user->can('Instructor')) {
            return $this->render('index-report', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'model'=>$model,
            ]);
    }

    /**
     * Displays a single CoursePrerequisit model.
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
     * Creates a new CoursePrerequisit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CoursePrerequisit();

        if ($model->load(Yii::$app->request->post()) ) {

            $model->preq_created_by = $model->preq_updated_by  =Yii::$app->user->identity->id;

            $model->preq_created_at = $model->preq_updated_at = date('Y-m-d h:i:s');

            $model->preq_dept_relId = $model->preq_dept_relId == 0?NULL:$model->preq_dept_relId;
            $model->preq_spec_relId = $model->preq_spec_relId == 0?NULL:$model->preq_spec_relId;
            $model->preq_preq_course_relId = $model->preq_preq_course_relId == 0?NULL:$model->preq_preq_course_relId;

            $model->preq_adm_relId = $model->preq_adm_relId == 0?NULL:$model->preq_adm_relId;
            $model->preq_prog_relId = $model->preq_prog_relId == 0?NULL:$model->preq_prog_relId;

            if ($model->save()) {
                 return $this->redirect(['index']);
            }else{
                // print_r($model->errors);die();
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message',json_encode($model->getErrors()));
                return $this->redirect(['index']);
            }

        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CoursePrerequisit model.
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
     * Deletes an existing CoursePrerequisit model.
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
     * Finds the CoursePrerequisit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CoursePrerequisit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CoursePrerequisit::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

