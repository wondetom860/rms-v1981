<?php

namespace frontend\controllers;

use Yii;
use backend\models\ProjectGroupMembers;
use backend\models\ProjectGroupMembersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use \common\components\CommonControllerFunctions as CCF;

/**
 * ProjectGroupMembersController implements the CRUD actions for ProjectGroupMembers model.
 */
class ProjectGroupMembersController extends Controller
{

    public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ProjectGroupMembers models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectGroupMembersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new ProjectGroupMembers();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$model,
        ]);
    }

    public function actionStudentList(){
        $pgsetId = isset($_POST['pgsetId']) && !empty($_POST['pgsetId'])?intval($_POST['pgsetId']):die('Uknown project group. Please select one.');
        // $courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])?intval($_POST['courseId']):die('Uknown project course. Please select one.');

        $pgset = \backend\models\ProjectCourseSetup::find()->where(['id'=>$pgsetId])->one();

        if ($pgset && count($pgset) > 0) {
            $this->__constructor();
            $studentGroup = $pgset->pcs_gset_relId;

            $course = \backend\models\Course::findOne(['id'=>$pgset->pcs_course_relId]);

            $semreg = \frontend\models\SemesterRegistration2::find()
                ->where(['semreg_acadYear_relId'=>$this->acadYearId,'semreg_semester_relId'=>$this->semId])
                ->innerJoin('semester_registration_2_detail','semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
                ->andWhere(['semester_registration_2_detail.srd2_course_relId'=>$pgset->pcs_course_relId])
                ->andWhere(['srd2_group_relId'=>$studentGroup])
                ->all();
            if ($semreg && count($semreg) > 0) {
                $ckbx = "<table class='table table-sm table-condensed table-striped>";
                $ckbx .= "<thead>
                            <th>#</th>
                            <th>Reg.ID</th>
                            <th>Student Name</th>
                            <th>Normal Group</th>
                            <th>Action</th>
                        </thead><tbody>";
                        $count = 0;
                foreach ($semreg as $key) {
                    $student = \backend\models\Student::findOne(['id'=>$key->semreg_std_relId]);
                    $stdUser = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
                    $gs = \backend\models\WGroupSetup::findOne(['id'=>$studentGroup]);
                    $gname = \backend\models\Wgroup::findOne(['id'=>$gs->gs_group_relId]);
                    $ckbx .= "<tr> 
                                <td>".++$count."</td>
                                <td>{$student->std_regId}</td>
                                <td>{$user->first_name} {$user->middle_name}</td>
                                <td>{$gname}</td>
                                <td>
                                    <input type='checkBox' onchange='addToList({$student->id})' />
                                </td>
                            </tr>";
                }
                $ckbx .="</tbody></table>";
            }else{
                echo "<p class='bg-danger'>No student enrolled this course in this semester. The course is <i><b>{$course->course_title}</b></i></p>";die();
            }
        }else{
            echo "<p class='bg-danger'>Such project group not found.</p>";die();
        }

    }

    /**
     * Displays a single ProjectGroupMembers model.
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
     * Creates a new ProjectGroupMembers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('DepartmentHead')) {
             throw new NotFoundHttpException('You are not allowed for this action.');
        }
        $model = new ProjectGroupMembers();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ProjectGroupMembers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
       if (!Yii::$app->user->can('DepartmentHead')) {
             throw new NotFoundHttpException('You are not allowed for this action.');
        } 
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ProjectGroupMembers model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('DepartmentHead')) {
             throw new NotFoundHttpException('You are not allowed for this action.');
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProjectGroupMembers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectGroupMembers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProjectGroupMembers::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
