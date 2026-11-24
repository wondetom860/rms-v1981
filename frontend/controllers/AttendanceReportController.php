<?php

namespace frontend\controllers;

use Yii;
use backend\models\Attendance;
use kartik\mpdf\Pdf;
use backend\models\Course;
use backend\models\WGroup;
use common\models\User;
use backend\models\Attendancedetail;
use backend\models\Instructor;
use frontend\models\SemesterRegistration2Detail;
use frontend\models\SemesterRegistration2;
use backend\models\CourseOffering2;
use backend\models\CourseOffering2Detail;
use backend\models\WGroupAssignment;
use backend\models\WGroupSetup;
use backend\models\Student;
use \common\components\CommonControllerFunctions AS CCF;

class AttendanceReportController extends \yii\web\Controller {

    public $acadYearId;
    public $semId;

    protected function __constructor() {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }


    public function actionIndex() {
        $model = new Attendance();
        Yii::$app->session->hasFlash('message')?Yii::$app->session->setFlash('message', Yii::$app->session->hasFlash('message')):0;
        
        return $this->render('index', ['model' => $model]);
    }

    public function actionAttendanceReportForm(){
      $model = new Attendance();

      if ($model->load(Yii::$app->request->post())) {
        
          $attendanceData = Attendance::find()
                  ->where(['attendance.a_course_relId' => $model->a_coff_relId])
                  ->andWhere(['attendance.a_coff_relId'=> $model->courseId])
                  ->andWhere(['attendance.a_group_relId' => $model->groupId]);
          $numberOfClasses = $attendanceData->count();
          if ($numberOfClasses < 1) {
              Yii::$app->session->setFlash('message',"No attendance collected for the selected section yet.");
              return $this->redirect('/attendance-report/printattendancereport')->send();
          }
          $attendanceData = $attendanceData->all();
          $this->__constructor();
          // var_dump($attendanceData);die();
          // get all the students who enrolled that course .

          $semRegs = SemesterRegistration2::find()
            ->innerJoin('semester_registration_2_detail','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where([
              'semreg_acadYear_relId'=>$this->acadYearId,
              'semreg_semester_relId'=>$this->semId,
            ])
            ->andWhere([
              'srd2_course_relId'=>intval($model->a_coff_relId),
              'srd2_group_relId'=>intval($model->groupId),
            ])
            ->all();
            $table = '';
          if ($semRegs && count($semRegs) > 0) {
            $gset = \backend\models\WGroupSetup::findOne($model->groupId);

            $adm = \backend\models\Admission::findOne($gset->gs_adm_relId);
            $prog = \backend\models\Program::findOne($gset->gs_prog_relId);
            $dept = \backend\models\Department::findOne($gset->gs_dept_relId);
            $clslvl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId)->clslvl_num;
            $gname = \backend\models\WGroup::findOne($gset->gs_group_relId)->group_name;
            $spec = is_null($gset->gs_spec_relId)?'-':\backend\models\Specialization::findOne($gset->gs_spec_relId)->spec_name;

            $clslvl = $clslvl == 1?$clslvl.'<sup>st</sup>':($clslvl == 2?$clslvl.'<sup>nd</sup>':($clslvl == 3?$clslvl.'<sup>rd</sup>':($clslvl.'<sup>th</sup>')));
             $clslvl .= 'Year.';
            $acadYear = Yii::$app->params['C']['Y'];
            $sem = Yii::$app->params['C']['S'];

            $table = "<table class='table table-stripped table-hover table-bordered table-condensed' style='font-size:9pt;' > ";
            $table .="<caption style='font-weight:bold;'>
                        <h3 style='text-align:center; '>Student Attendance report</h3>
                        <p style='text-align:center;'>
                        Admission:<u><i>{$adm->admission_name}</i></u>
                        Program:<u><i>{$prog->program_name}</i></u><br>
                        Department:<u><i>{$dept->dept_name}</i></u>
                        Specialization:<u><i>{$spec}</i></u><br>
                        Class Level:<u><i>{$clslvl}</i></u>
                        Group:<u><i>{$gname}</i></u>
                        <br>
                        Semester <u><i>{$sem}</i></u> of <u><i>{$acadYear}</i></u>
                        </p>
                    </caption>";
            $table .= "<thead style='background-color:#8D0;'>
                      <th>#</th>
                      <th>Reg. ID</th>
                      <th>Student Name</th>";
            foreach ($attendanceData as $aData) {
              $date = date_format(date_create($aData->a_createdAt),'M d');
              $table .= "<th>{$date}</th>";
            }

            $table .= "<th>Total (P)</th>";
            $table .= "<th>Total (A)</th>";
            $table .= "<th>Total(%)</th>";

            $table .= "</thead><tbody>";
                    $count = 0;
            foreach ($semRegs as $std) {
              $p = 0;
              $total = 0;
              $student = \backend\models\Student::findOne($std->semreg_std_relId);
              $user = \common\models\User1::findOne($student->std_user_relId);
              $table .= "<tr>
                        <td style='text-align:center;'>".++$count."</td>
                        <td>{$student->std_regId}</td>
                        <td>{$user->first_name} {$user->middle_name}</td>";
                        foreach ($attendanceData as $aData) {
                            $s = $this->getPresentCount($aData->id, $student->id);
                            if ($s == 0) {
                              $status = 'A';
                            }else{
                             $status = 'P';
                             $p += $s;
                            }
                            $total += 1;
                            $table .= "<td style='text-align:center;'>{$status}</td>";
                          }
              $g = round($p / $total *100,2);

              $table .= "<td style='text-align:center;'>{$p}</td>";
              $table .= "<td style='text-align:center;'>".($total - $p)."</td>";
              $table .= "<td style='text-align:center;'>{$g}% (P)</td>";
              $table .= "</tr>";
            }
            $table .= "</tbody></table>";
            echo json_encode([1,$table]);die();
          }else{
            Yii::$app->session->setFlash('message',"No record found.");
            return $this->redirect('/attendance-report/printattendancereport');
          }
      }else{
        // echo "ahhaaaa";die();
        Yii::$app->session->setFlash('message',"Incomplete data provided.");
        return $this->redirect('/attendance-report/printattendancereport')->send();
      }
      // die();
    }

    public function actionPrintattendancereport() { //printattendancereport
        $count = 0;
        $userInfromIns = 0;
        $courseId = array('');
        $courseOfferingID = array('');
        $groupsetupID = array('');
        $groupId = array('');
        $groupSetupfromSemRegDetail = array('');
        $studentID = array('');
        $userId = array('');
        $studentName = array('');
        $assignmentgroupId = array('');
        $group = array('');
        // $findData=array('');
        $model = new Attendance();
        $modelAttendanceDeatil = new Attendancedetail();
        $modelUser = new User();
        $modelInstructor = new Instructor();
        $modelSemRegDetail = new SemesterRegistration2Detail();
        $modelCourseOffering = new CourseOffering2();
        $modelCourseOfferingDetail = new CourseOffering2Detail();
        $course = new course();
        $studentAssignment = new WGroupAssignment();
        $modelgroup = new WGroup();
        $modelgroupSetup = new WGroupSetup();
        $student = new student();
        $stdName = student::find()->all();
        $groupsetup = WGroupSetup::find()->all();
        $courseOfferingid = CourseOffering2::find()->all();
        $GID = WGroup::find()->all();
        $stdAssignment = WGroupAssignment::find()->distinct()->all();
        $IdfromCourse = course::find()->distinct()->all();
        $findFromCourseOff = CourseOffering2Detail::find()->distinct()->all();
        $semRegDetail = SemesterRegistration2Detail::find()->distinct()->all();
        $findInsId = Instructor::find()->distinct()->all();
        $userInfo = User::find()->all();
        foreach ($userInfo as $userinfo) {
            if ($userinfo->id == Yii::$app->user->identity->id) {
                $InstructorName = ucfirst($userinfo->first_name) . ' ' . ucfirst($userinfo->middle_name)[0].'.';
            }
        }
        $attendanceDetailInfo = Attendancedetail::find()->all();
        foreach ($findInsId as $insId) {
            if ($insId->inst_user_relId == Yii::$app->user->identity->id) {
                $userInfromIns = $insId->id; // instructor id=7 and user id=39
            }
        }
        if ($userInfromIns == 0) {                                    
            Yii::$app->session->setFlash('message',"You are not allowed to do this");
            return $this->redirect('/attendance-report/printattendancereport')->send();
        } else {
            foreach ($findFromCourseOff as $offID) {
                if ($offID->coff2det_inst_relId == $userInfromIns) {
                    $courseId[] = $offID->coff2det_course_relId; // course id=13 and 14
                    $courseOfferingID[] = $offID->coff2det_coff2_relId; // Course offering ID=1
                }
            }
            foreach ($courseOfferingid as $offeringID) {
                foreach ($courseOfferingID as $OffID) {
                    if ($offeringID->id == $OffID) {
                        $groupsetupID[] = $offeringID->coff2_group_relId;
                    }
                }
            }
            // foreach ($groupsetupID as $value) {
            //     echo $value."</br>";
            // }
            // die();   
            foreach ($groupsetup as $gsetup) { // Group ID fro groupsetup
                foreach ($groupsetupID as $gsetupid) {
                    if ($gsetup->id == $gsetupid) {
                        $groupId[] = $gsetup->gs_group_relId;
                    }
                }
            }
            foreach ($stdAssignment as $stdAss) {
                foreach ($groupsetupID as $gsID) {
                    if ($stdAss->gas_gset_relId == $gsID) {
                        $studentID[] = $stdAss->gas_std_relId; // Students ID
                        $count++;
                    }
                }
            }
            foreach ($stdName as $stdname) {
                foreach ($studentID as $stdId) {
                    if ($stdname->id == $stdId) {
                        $userId[] = $stdname->std_user_relId;
                    }
                }
            }
            foreach ($userInfo as $uinfo) {
                foreach ($userId as $uid) {
                    if ($uinfo->id == $uid) {
                        $studentName[] = $uinfo->first_name . ' ' . $uinfo->middle_name;
                    }
                }
            }

            if ($model->load(Yii::$app->request->post())) {
                $startDate = $model->fromDate;
                $endDate = $model->toDate;
                $course = Course::findOne($model->courseId);
                $courseTitle = $course->course_title;
                // foreach ($course as $coursevalue) {
                //     if($coursevalue->id==$model->courseId){
                //         $courseTitle=$coursevalue->course_title;
                //     }
                // }
                $group = WGroupSetup::findOne($model->groupId);
                $groupName = WGroup::findone($group->gs_group_relId)->group_name;
                // foreach ($group as $groupvalue) {
                //     if($groupvalue->id==$model->groupId){
                //         $groupName=$groupvalue->group_name;
                //     }
                // }
                $attendanceData = Attendance::find()
                        ->where(['attendance.a_course_relId' => $model->courseId])
                        ->andWhere(['attendance.a_group_relId' => $model->groupId])
                        ->andWhere(['>=', 'attendance.a_createdAt', $model->fromDate])
                        ->andWhere(['<=', 'attendance.a_createdAt', $model->toDate]);
                $numberOfClasses = $attendanceData->count();
                if ($numberOfClasses < 1) {
                    Yii::$app->session->setFlash('message',"Oh ! Sorry please try to insert correct data!!");
                    return $this->redirect('/attendance-report/printattendancereport')->send();
                }
                $attendanceData = $attendanceData->all();
                $ids = []; // To store attendance ID from attendance table 
                $classDates = [];
                $NumberOfPresents = [];
                $NumberOfPresentsInPercent = [];
                $NumberOfabsents = [];
                $NumberOfabsentsInPercent = [];
                $counter = 1;
                // those variables are used for students daily status
                $studentStatus = array(array());
                foreach ($attendanceData as $aData) { // To fetch available classes per given date range
                    $classDates[] = $aData->a_createdAt;
                    $ids[] = $aData->id;
                    $studentAttendanceStatusValue[] = $this->getStudentattendancestatus($aData->id, $studentID);
                }
                // foreach ($attendanceData as $key) {
                //     array_push($ids, $key['id']);
                // }
                // foreach ($attendanceData as $aData) {
                //     $studentAttendanceStatusValue[] = $this->getStudentattendancestatus($aData->id, $studentID);
                // }
                //  $k=0;
                // // $array=[];
                //  foreach ($studentStatus as $value) {
                //      $studentAttendanceStatusValue[$k]=array_column($studentStatus, $k);
                //      $k++;
                //  }
                // for($i=1;$i<count($studentAttendanceStatusValue);$i++) {
                //   echo('<table class=table-bordered>');
                //   echo('<tr>');
                //   for($j=0;$j<count($studentAttendanceStatusValue[$i]);$j++) {
                //     echo('<td>' . $studentAttendanceStatusValue[$i][$j] . '</td>');
                //   } 
                //   echo('</tr>');
                //   echo('</table>');
                // }
                // var_dump($studentStatus);
                // print_r(array_fill_keys($studentStatus[1],$studentStatus[2])); 
                // die();
                foreach ($studentID as $stdID) {
                    $NumberOfPresents[] = $this->getPresentCount($ids, $stdID);
                }

                for ($i = 1; $i < count($studentID); $i++) {
                    $NumberOfabsents[] = $numberOfClasses - $NumberOfPresents[$i];
                    $NumberOfPresentsInPercent[] = $NumberOfPresents[$i] / $numberOfClasses * 100;
                    $NumberOfabsentsInPercent[] = ($numberOfClasses - $NumberOfPresents[$i]) / $numberOfClasses * 100;
                }
                $content = $this->renderPartial('printattendancereport', [
                    // 'findData'=>$findData,
                    'courseTitle' => $courseTitle,
                    'groupId' => $groupId,
                    'groupName' => $groupName,
                    'studentName' => $studentName,
                    'InstructorName' => $InstructorName,
                    //'studentId'=>$studentId,
                    'numberOfClasses' => $numberOfClasses,
                    'NumberOfabsents' => $NumberOfabsents,
                    'NumberOfabsentsInPercent' => $NumberOfabsentsInPercent,
                    'NumberOfPresents' => $NumberOfPresents,
                    'NumberOfPresentsInPercent' => $NumberOfPresentsInPercent,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'classDates' => $classDates,
                    'studentAttendanceStatusValue' => $studentAttendanceStatusValue,
                    'count' => $count,
                ]);
                $pdf = new Pdf([
                    // set to use core fonts only
                    'mode' => Pdf::MODE_UTF8,
                    // 'mode' => Pdf::MODE_CORE,
                    // A4 paper format
                    'format' => Pdf::FORMAT_A4,
                    // portrait orientation 
                    'orientation' => Pdf::ORIENT_PORTRAIT,
                    //'orientation' => Pdf::ORIENT_LANDSCAPE,
                    'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.css',
                    // any css to be embedded if required
                    'cssInline' => '.kv-heading-1{font-size:18px}',
                    // stream to browser inline
                    'destination' => Pdf::DEST_BROWSER,
                    // your html content input
                    'content' => $content,
                    'options' => [
                    //'title' => 'DEC Hospital Referral Sheet',
                    ],
                    'methods' => [
                        'SetHeader' => [' DEC Registrar System Students Attendance Report ||referred On: ' . date("r")],
                    // 'SetFooter' => [Yii::$app->user->identity->firstName.'||Page {PAGENO}|'],
                    //  'SetFooter' => ['Student Attendance Report'],
                    ]
                ]);
                return $pdf->render();
            } else {
                return $this->render('attendancereport', [
                            'courseId' => $courseId,
                            'groupId' => $groupId,
                ]);
            }
        }
    }

    protected function getPresentCount($detailIds, $studentId) {
        //find Number of presents for each day
        $presnts = Attendancedetail::find()
                ->where(['ad_attendance_relId' => $detailIds])
                ->andWhere(['attendancedetail.ad_student_relId' => $studentId])
                ->count();
        return $presnts;
    }

    protected function getStudentattendancestatus($attendanceid, $studentID) {
        $statusValue = [];
        $counter = 0;
        $attendancedetailData = Attendancedetail::find('ad_student_relId')
          ->where(['ad_attendance_relId' => $attendanceid])
          ->all();

        foreach ($studentID as $stdID) {
            foreach ($attendancedetailData as $ID) {
                if ($ID->ad_student_relId == $stdID) {
                    $counter++;
                }
            }
            if ($counter == 1) {
                $statusValue[] =  'P';
                $counter = 0;
            } else {
                $statusValue = 'A';
            }
        }
        return $statusValue;
    }

}
