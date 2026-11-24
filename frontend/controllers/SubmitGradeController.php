<?php

namespace frontend\controllers;

use Yii;
use backend\models\StudentGroupHistory;
use backend\models\SchoolYear;
use backend\models\Semester;
use backend\models\Student;
use backend\models\CourseOffering;
use frontend\models\SemesterRegistration2;
use frontend\models\SemesterRegistration2Detail;

class SubmitGradeController extends \yii\web\Controller
{
    public function actionIndex()
    {
    	$model = new StudentGroupHistory();
    	$semModel = new SemesterRegistration2Detail();
    	 return $this->render('index', [
                'model' => $model,
                'semModel'=>$semModel,
            ]);
        // return $this->render('index');
    }

    public function actionGetsemestercourses(){
        $semCourses = $this->getMaster();

        if (!empty($semCourses)) {
            $semCourses = $semCourses->all();
            // $list = ArrayHelper::map($semCourses,'id',$semCourses['course_offering.coff_course_relId']);

            foreach ($semCourses as $course) {
                $hjhj = \backend\models\Course::findOne(['id'=>$course['coff_course_relId']])->course_title;
                echo "<option value='". $course['coff_course_relId'] ."'>". $hjhj ."</option>";
            }
        }
    }

    public function actionGetenrolledstudent(){
    	if (isset($_POST['id']) && !empty($_POST['id'])) {
            $courseId = $_POST['id'];

            // Get the enrolled students of this course in this ssemster and academic year.
            $acadYearId = SchoolYear::findOne(['sch_year_desc'=>Yii::$app->params['C']['Y']])->id;
            $semId = Semester::findOne(['semester_name'=>Yii::$app->params['C']['S']])->id;

            $semreg2 = SemesterRegistration2::find()->where([
                        'semester_registration_2.semreg_acadYear_relId'=>$acadYearId,
                        'semester_registration_2.semreg_semester_relId'=>$semId,
                    ])->all();
            if (!empty($semreg2)) {

                echo "<table class='table table-hover table-sm table-bordered'>";
                echo "<thead><th>Option</th><th>RegID</th><th>Full Name</th></thead><tbody>";
                $scount = 0;
                foreach ($semreg2 as $semreg) {
                    $srd2Id = $semreg['id'];


                    $enrolledCourses = \frontend\models\SemesterRegistration2Detail::findOne([
                        'srd2_semreg_relId'=>$srd2Id,
                        'srd2_course_relId'=>$courseId,
                        'srd2_grade_relId'=>NULL
                    ]);

                    if (empty($enrolledCourses)) {
                        continue;
                    }else{
                        $scount++;
                        $regId = \backend\models\Student::findOne(['id'=>$semreg['semreg_std_relId']])->std_regId;

                        $jh = \backend\models\Student::findOne(['id'=>$semreg['semreg_std_relId']])->std_user_relId;
                        $jhjh = \backend\models\User::findOne(['id'=>$jh]);
                        echo "<tr>
                            <td>
                                <input type='checkbox' onchange='addGrade(". $semreg['semreg_std_relId'] .")'>
                            </td>
                            <td>
                                ". $regId ."
                            </td>
                            <td>
                                ". $jhjh->first_name.' '.$jhjh->middle_name ."
                            </td>
                        </tr>";

                    }

                }if ($scount == 0) {
                    echo "<tr style='background-color:#ace;'><td colspan=3>No student enrolled this course in this semsetr. / OR all students are graded for this course.</td></tr>";
                }
                echo "</tbody></table>";
            }
        }
  
    }

    public function actionCreate(){
        if (isset($_POST['stdListS']) && !empty($_POST['stdListS']) && isset($_POST['lgId']) && !empty($_POST['lgId'])) {

            $stdList = $_POST['stdListS'];
            $grade = $_POST['lgId'];
            $courseId=$_POST['courseId'];

            $acadYearId = SchoolYear::findOne(['sch_year_desc'=>Yii::$app->params['C']['Y']])->id;
            $semId = Semester::findOne(['semester_name'=>Yii::$app->params['C']['S']])->id;

            $stdList  =json_decode($stdList);
            if (!empty($stdList)) {
                foreach ($stdList as $std) {
                    $semreg2 = SemesterRegistration2::findOne([
                        'semester_registration_2.semreg_acadYear_relId'=>$acadYearId,
                        'semester_registration_2.semreg_semester_relId'=>$semId,
                        'semester_registration_2.semreg_std_relId' =>$std,
                    ]);



                    if (!empty($semreg2)) {
                        $result = Yii::$app->db->createCommand()
                                 ->update('semester_registration_2_detail', 
                                    [
                                        'srd2_grade_relId' => $grade,

                                    ], 
                                    [
                                        'srd2_semreg_relId'=>$semreg2->id,
                                        'srd2_course_relId'=>$courseId,
                                ])
                                 ->execute();
                            echo($result);die();
                    }else{

                    }


                }
            }else{
                echo "<p>No student selected.</p>";die();
            }

        }
    }

  
    protected function getMaster(){
            $acadYearId = SchoolYear::findOne(['sch_year_desc'=>Yii::$app->params['C']['Y']])->id;
            $semId = Semester::findOne(['semester_name'=>Yii::$app->params['C']['S']])->id;

            $coffs = CourseOffering::find()
                        ->innerJoinWith('coffCocatRel','course_catalog.id = course_offering.coff_cocat_relId','course_catalog.cocat_course_relId = course_offering.coff_course_relId')
                        ->andWhere(['course_offering.coff_acadYear_relId'=>$acadYearId])
                        ->andWhere(['course_catalog.cocat_sem_relId'=>$semId]);
             return $coffs;
        }
   }
