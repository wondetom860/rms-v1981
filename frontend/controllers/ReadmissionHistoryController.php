<?php

namespace frontend\controllers;

use Yii;
use frontend\models\ReadmissionHistory;
use frontend\models\ReadmissionHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Student;
use frontend\models\StudentWithdrawalHistory;
use frontend\models\SemesterRegistration2;
use frontend\models\SemesterRegistration2Detail;
use backend\models\Course;
use backend\models\GradeScale;
use common\models\User;
/**
 * ReadmissionHistoryController implements the CRUD actions for ReadmissionHistory model.
 */
class ReadmissionHistoryController extends Controller
{
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
            'access' => [
               'class' => yii\filters\AccessControl::className(),
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => yii\filters\AccessRule::className(),
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
     * Lists all ReadmissionHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReadmissionHistorySearch();
        $StudentWithdrawalHistory=new StudentWithdrawalHistory();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
     public function actionApproveIndex()
    {
        $searchModel = new ReadmissionHistorySearch();
       // $StudentWithdrawalHistory=new StudentWithdrawalHistory();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->Where(' readmission_history.rh_status=0');
        return $this->render('approveindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReadmissionHistory model.
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
      public function actionApproveView($id)
    {
        return $this->render('approveview', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ReadmissionHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $count=0;
        $academicCalId;
        //For allowing and deneying of re-admission
            $registrationID=array('');
            $userIDFromStudent=0;
            //about course
            $CourseID=array('');
            $CourseCode=array('');
            $CourseTitle=array('');
            $CourseCrHr=array('');
            //about semester
            $semester=array('');
            $semesterID=array('');
            //about grade
            $GradeID=array('');
            $Grade=array('');
            $Points=array('');
            $studentId=0;
        //For allowing and deneying of re-admission
         $SGPA_11=0;$SGPA_12=0;$CrHr_11=0;$S_points_11=0;$C_points_11=0;
                                // first year first semester
        $CGPA_11=0;$CGPA_12=0;$CrHr_12=0; $S_points_12=0;$C_points_12=0;
                    // first year second semester
        $model = new ReadmissionHistory();
        $modelStudent=new Student();
        $stdId=Student::find()->all();
        $modelWithdrawal=new StudentWithdrawalHistory();
        $withdrawalStdId=StudentWithdrawalHistory::find()->all();
        $semesterReg=new SemesterRegistration2();
        $stdRegId=SemesterRegistration2::find()->all();
        $semesterRegDetail=new SemesterRegistration2Detail();
        $stdRegIdDetail=SemesterRegistration2Detail::find()->all();
        $course=new Course();
        $courseInfo=Course::find()->all();
        $user=new User();
        $gradeScale=new GradeScale();
        $stdGrade=GradeScale::find()->all();
        foreach ($stdId as $stdid) {
            if($stdid->std_user_relId==Yii::$app->user->identity->id){
                $studentId=$stdid->id;
            }
        }
        foreach ($withdrawalStdId as $withstdId) {
            if($withstdId->swh_student_relId==$studentId){
            $model->rh_student_relId=$studentId;
            $model->rh_academicCal_relId=$withstdId->swh_academicyear_relId;
            $model->rh_semester_reiId=$withstdId->swh_semester_relId;
                $count++;
            }
        }
        if($count!=0)
        {
                foreach ($stdRegId as $stdregid) {//finding student iD from SemesterRegistration2
                if($stdregid->semreg_std_relId==$studentId){
                    $registrationID[]=$stdregid->id; //11 Student ID in student table
                    $semesterID[]=$stdregid->semreg_semester_relId;
                }
            }
            foreach ($stdRegIdDetail as $stdregdetail) {
                foreach ($registrationID as $regID) {
                if($stdregdetail->srd2_semreg_relId==$regID){
                    $CourseID[]=$stdregdetail->srd2_course_relId;
                    $GradeID[]=$stdregdetail->srd2_grade_relId;// Retrive Grade Scaleid from registration detail
                }
              }
            }
            foreach ($courseInfo as $course) { // Finding list of cources
                foreach ($CourseID as $courseid) {
                    if($course->id==$courseid){
                       $CourseCode[]=$course->course_code;
                        $CourseTitle[]=$course->course_title;
                        $CourseCrHr[]=$course->course_crdthr;
                    }
                }
            }   
            foreach ($stdGrade as $stdG) {// Finding list of grades
                foreach ($GradeID as $gid) {
                    if($stdG->id==$gid){
                       $Grade[]=$stdG->gs_ltr_grade;
                        $Points[]=$stdG->gs_gradePoint;
                    }
                }
            }
            // First Year First Semester
            for ($i=1;$i<=5;$i++) {
                      $S_points_11=$S_points_11+$CourseCrHr[$i]*$Points[$i];
                      $CrHr_11+=$CourseCrHr[$i];
                  }
            for ($i=6;$i<=10;$i++) {
                      $S_points_12=$S_points_12+$CourseCrHr[$i]* $Points[$i];
                            $CrHr_12+=$CourseCrHr[$i]; 
                        }
            if(round(($S_points_11/$CrHr_11),2)<1.25){
                header("refresh:1;url=http://decregistrar.local/index.php?r=readmission-history%2Findex");
                    echo "<script type='text/javascript'>";
                    echo "alert('You grade will not allowed you to fill re-admission !!');";
                    echo "</script>";
            }
        else if(round((($S_points_11+$S_points_12)/($CrHr_11+$CrHr_12)),2)>=1.25 && round((($S_points_11+$S_points_12)/($CrHr_11+$CrHr_12)),2)<1.5 ){
                  header("refresh:1;url=http://decregistrar.local/index.php?r=readmission-history%2Findex");
                    echo "<script type='text/javascript'>";
                    echo "alert('You are in warnning status !!');";
                    echo "</script>";
            }
            else{
            // First year second semester
                 if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
                  }
                return $this->render('create', [
                'model' => $model,
                ]);
          }
        }
        else
        {
             header("refresh:1;url=http://decregistrar.local/index.php?r=readmission-history%2Findex");
                        echo "<script type='text/javascript'>";
                        echo "alert('You are not allowed to fill re-admission !!');";
                        echo "</script>";
        }
    }
      public function actionApproveReadmission($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['approve-view', 'id' => $model->id]);
        }

        return $this->render('approveupdate', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing ReadmissionHistory model.
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
     * Deletes an existing ReadmissionHistory model.
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
     * Finds the ReadmissionHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReadmissionHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReadmissionHistory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
