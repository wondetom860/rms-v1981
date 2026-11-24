<?php

namespace frontend\controllers;

use Yii;
use backend\models\WStudentWithdrawalHistory as StudentWithdrawalHistory;
use backend\models\WStudentWithdrawalHistorySearch as StudentWithdrawalHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Student;
use frontend\models\SemesterRegistration2;
use frontend\models\SemesterRegistration2Detail;
use backend\models\Course;
use backend\models\GradeScale;
use yii\db\Query;
use common\models\User;
use backend\models\schoolYear;
/**
 * StudentWithdrawalHistoryController implements the CRUD actions for StudentWithdrawalHistory model.
 */
class StudentWithdrawalHistoryController extends Controller
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
        ];
    }

    /**
     * Lists all StudentWithdrawalHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StudentWithdrawalHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
     public function actionApproveIndex()
    {
        $searchModel = new StudentWithdrawalHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->Where('student_withdrawal_history.swh_request_status=0');
        return $this->render('approveIndex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StudentWithdrawalHistory model.
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
        return $this->render('approveView', [
            'model' => $this->findModel($id),
        ]);
    }
    /**
     * Creates a new StudentWithdrawalHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StudentWithdrawalHistory();
        $modelStudent=new Student();
        $modelSemesterRegistration=new SemesterRegistration2();
        $findStudentId=Student::find()->all();
        $findstdIdFromSemster=SemesterRegistration2::find()->all();
        foreach ($findStudentId as $stdId) {
            if($stdId->std_user_relId==Yii::$app->user->identity->id)
                $model->swh_student_relId=$stdId->id;
            break;
        }
         foreach ($findstdIdFromSemster as $semstdId) {
            if($semstdId->id==$model->swh_student_relId){
            $model->swh_academicyear_relId=$semstdId->semreg_acadYear_relId;
            $model->swh_semester_relId=$semstdId->semreg_semester_relId;
            break;
            }
        }
        //$findAcademicCalender=modelACademicCalender::findOne()->where('')
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    public function actionApproveCreate($id)
    {
        $model = new StudentWithdrawalHistory();
        $modelStudent=new Student();
        $modelSemesterRegistration=new SemesterRegistration2();
        $findStudentId=Student::find()->all();
        $findstdIdFromSemster=SemesterRegistration2::find()->all();
        //$findAcademicCalender=modelACademicCalender::findOne()->where('')
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['approve-view', 'id' => $model->id]);
        }

        return $this->render('approvecreate', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing StudentWithdrawalHistory model.
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
    public function actionApproveUpdate($id)
    {
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
        $semesterReg=new SemesterRegistration2();
        $schoolYear=new schoolYear();
        $stdRegId=SemesterRegistration2::find()->all();
        $semesterRegDetail=new SemesterRegistration2Detail();
        $stdRegIdDetail=SemesterRegistration2Detail::find()->all();
        $course=new Course();
        $courseInfo=Course::find()->all();
        $user=new User();
        $gradeScale=new GradeScale();
        $stdGrade=GradeScale::find()->all();
        $query = new Query();
        $model = $this->findModel($id); // 
        $studentId=$model->swh_student_relId;// 11 student ID from withdrawal table
        $studentStdId = $query->select(['std_user_relId']) 
        ->from('student')
        ->where(['id' => $studentId]) // Specify multiple conditions
        ->one(); // Returns the first row of the result
        foreach ($studentStdId as $std) {
             $userIDFromStudent=$std;
           }
       $SudentNameFromUser=User::find()->all();
       foreach ($SudentNameFromUser as $stdName) {
           if($stdName->id==$userIDFromStudent){
            $firstName=$stdName->first_name;
            $middleName=$stdName->middle_name;
            $lastName=$stdName->last_name;
           }
       }

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
        if ($model->load(Yii::$app->request->post()) && $model->save()) 
        {
            $enrolmentID=[];
            $calenderSemester1=date('Y');
            $calenderSemester11=date('y')+1; 
            $calenderSemester2=date('Y')+1;
            $calenderSemester22=date('y')+2;
             $academicCalender=schoolYear::find('id')
                 ->where(['sch_year_desc'=>$calenderSemester1.'/'.$calenderSemester11])
                 ->orWhere(['sch_year_desc'=>$calenderSemester2.'/'.$calenderSemester22])
                 ->all();

             foreach ($academicCalender as $ID) {
                $academicYearId=$ID->id;
            }
            foreach ($stdRegId as $stdregID) {
                if($stdregID->semreg_acadYear_relId==$academicYearId && $stdregID->semreg_std_relId==$studentId)
                    $enrolmentID[]=$stdregID->id;
            }
            $stdSemesterRegistrationId=max($enrolmentID);
            $registrationDetail=SemesterRegistration2Detail::find()
            ->where(['srd2_semreg_relId'=>$stdSemesterRegistrationId])
            ->all();
            //$registrationDetail->delete();
            return $this->redirect(['approve-view', 'id' => $model->id]);
        }
        return $this->render('approveupdate', [
            'model' => $model,
            //'studentId'=>$studentId,
            'CourseCode'=>$CourseCode,
            'CourseTitle'=>$CourseTitle,
            'CourseCrHr'=>$CourseCrHr,
            'Grade'=>$Grade,
            'Points'=>$Points,
            'firstName'=>$firstName,
            'middleName'=>$middleName,
            'lastName'=>$lastName,
        ]);
    }

    /**
     * Deletes an existing StudentWithdrawalHistory model.
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
     * Finds the StudentWithdrawalHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StudentWithdrawalHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StudentWithdrawalHistory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
