<?php

namespace common\components;

use \common\components\CommonControllerFunctions as CCF;
use yii\helpers\ArrayHelper;
use yii;

/**
 * controller class for alert / notification
 */
class NotificationController extends \yii\web\Controller {

    public static $acadYearId;
    public static $semId;

    protected static function __constructor() {

        $cs = CCF::currentTime();
        static::$acadYearId = $cs['Y'];
        static::$semId = $cs['S'];
    }

    public static function addToList($title,$link,$count){
        if (!ArrayHelper::keyExists($title,Yii::$app->params['notifications'],false)) {
            Yii::$app->params['notifications']['count'] += $count;
            Yii::$app->params['notifications'][$title] = [
                'title'=>$title,
                'count'=>$count,
                'link'=>$link
            ];
        }
        
    }

    public static function alert() {
        static::__constructor();
        if (Yii::$app->user->can('Registrar')) {
            static::getRegistrarAlert();
            // static::getAdminAlert();
        } else if (Yii::$app->user->can('DepartmentHead')) {
            static::getHoDAlert();
            static::getHoDAlert2();
            static::getInstAlert();
        } else if (Yii::$app->user->can('Instructor')) {
            static::getInstAlert();
            static::getInstAlert2();
        } else if (Yii::$app->user->can('Student')) {
            static::getStudentAlert();
        } else if (Yii::$app->user->can('CEP Clerk')) {
            static::getCEPClerkAlert();
        }else if (Yii::$app->user->can('Admin')) {
            static::getAdminAlert();
        }
        // echo "<img src='/logo/red.gif' height='20' width='20' title='default Alert' alt='' class='img-thumbnail' />";
    }

    private static function getHoDAlert2(){
        $functionName = "getHodAlertNotification";
        $moduleModels = ['../../frontend/models','../../backend/models'];

        foreach (Yii::$app->modules as $keyModule => $class) {

            $moduleModels[] = "../../frontend/modules/".$keyModule."/models";
            $moduleModels[] = "../../backend/modules/".$keyModule."/models";
        }

        // loop over those models
        foreach ($moduleModels as $modelString) {
            if (!file_exists($modelString)) {
                continue;
            }
            foreach (array_diff( scandir($modelString.'/'), array(".", "..") ) as $keyModels) {
                $className = str_replace(".php", "", $keyModels);
                $className = str_replace("../", "", $modelString.'/'.$className);
                $className = str_replace("/", "\\", $className);
                if (class_exists($className)) {
                    try {
                        if (method_exists($className::ClassName(), $functionName)) {
                            $className::ClassName()::getHodAlertNotification();
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }
    }
    
    private static function getInstAlert2(){
        $functionName = "getInstAlertNotification";
        $moduleModels = ['../../frontend/models','../../backend/models'];

        foreach (Yii::$app->modules as $keyModule => $class) {

            $moduleModels[] = "../../frontend/modules/".$keyModule."/models";
            $moduleModels[] = "../../backend/modules/".$keyModule."/models";
        }

        // loop over those models
        foreach ($moduleModels as $modelString) {
            if (!file_exists($modelString)) {
                continue;
            }
            foreach (array_diff( scandir($modelString.'/'), array(".", "..") ) as $keyModels) {
                $className = str_replace(".php", "", $keyModels);
                $className = str_replace("../", "", $modelString.'/'.$className);
                $className = str_replace("/", "\\", $className);
                if (class_exists($className)) {
                    try {
                        if (method_exists($className::ClassName(), $functionName)) {
                            $className::ClassName()::getInstAlertNotification();
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }
    }

    private static function getAdminAlert(){
        $functionName = "getAlertNotification";
        $moduleModels = ['../../frontend/models','../../backend/models'];

        foreach (Yii::$app->modules as $keyModule => $class) {

            $moduleModels[] = "../../frontend/modules/".$keyModule."/models";
            $moduleModels[] = "../../backend/modules/".$keyModule."/models";
        }

        // loop over those models
        foreach ($moduleModels as $modelString) {
            if (!file_exists($modelString)) {
                continue;
            }
            foreach (array_diff( scandir($modelString.'/'), array(".", "..") ) as $keyModels) {
                $className = str_replace(".php", "", $keyModels);
                $className = str_replace("../", "", $modelString.'/'.$className);
                $className = str_replace("/", "\\", $className);
                if (class_exists($className)) {
                    try {
                        if (method_exists($className::ClassName(), $functionName)) {
                            $className::ClassName()::getAlertNotification();
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }
    }

    private static function getRegistrarAlert() {
        // static::__constructor();
        $reqs = \backend\models\ReassessmentHistory::find()
                ->where([
                    'ras_registrarApprove_status' => NULL,
                ])
                ->count();
        if ($reqs > 0) {
            static::addToList('Reassessment requests','/approve-reassessment-request',2);
            // echo "<a href='/approve-reassessment-request'  target='_blank'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$reqs} re-assessment requests for your dept. courses in this semester' alt='' /></a>";
        }

        $wdReqsCount = new \backend\models\TouchedGrades();
        $wdReqsCount = $wdReqsCount->getRegistrarCAlert();
        
        if ($wdReqsCount > 0) {
            static::addToList('Grade change request','/chaged-grades',$wdReqsCount);
        }
        

        $wdReqsCount = new \backend\models\SchoolYear();
        $wdReqsCount = $wdReqsCount->alertBatchNumber(static::$acadYearId);
        
        if ($wdReqsCount) {
            static::addToList('Batch Information','/batch-info',$wdReqsCount);
        }
    

        $wdReqsCount = new \backend\models\GraduateStudent();
        $wdReqsCount = $wdReqsCount->generateRegistrarRequestAlert();
        
        if ($wdReqsCount) {
            static::addToList('Graduate Student','/approve-graduate-student-information',$wdReqsCount);
        }

        $wdReqsCount = new \backend\models\WStudentWithdrawalHistory();
        $wdReqsCount = $wdReqsCount->checkregistrarRequest();
        
        if ($wdReqsCount > 0) {
            static::addToList('Withdrawal requests','/request-withdrawal/request-approve',$wdReqsCount);
            // echo "<a href='/request-withdrawal/request-approve'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} withdrawal requests' alt='' /></a>";
        }

        $wdReqsCount = new \backend\models\WStudentReadmissionHistory();
        $wdReqsCount = $wdReqsCount->checkregistrarRequest();
        
        if ($wdReqsCount > 0) {
            static::addToList('Readmission requests','/approve-readmission',$wdReqsCount);
            // echo "<a href='/approve-readmission'  target='_blank'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} Readmission requests' alt=''/></a>";
        }

        $wdReqsCount = new \common\models\User1();
        $wdReqsCount = $wdReqsCount->checkregistrarRequest(NULL);
        
        if ($wdReqsCount > 0) {
            static::addToList('Account approve requests','/approve-user-signup',$wdReqsCount);
            // echo "<a href='/approve-user-signup'  target='_blank'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} ' alt=''/></a>";
        }

        $wdReqsCount = new \backend\models\SelfSponsoredPaymentHistory();
        $wdReqsCount = $wdReqsCount->getRegistrarAlert();
        
        if ($wdReqsCount > 0) {
            static::addToList('payment approve request','/payment-request',$wdReqsCount);
            // echo "<a href='/payment-request'  target='_blank'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} ' alt=''/></a>";
        }

        $wdReqsCount = new \backend\models\PaymentRedemption();
        $wdReqsCount = $wdReqsCount->getRegistrarAlert();
        
        if ($wdReqsCount > 0) {
            static::addToList('payment redemption request','/payment-redemption',$wdReqsCount);
            // echo "<a href='/payment-request'  target='_blank'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} ' alt=''/></a>";
        }

        $wdReqsCount = new \frontend\models\SemesterRegistration2Detail();
        $wdReqsCount = $wdReqsCount->RegistrarGradeLockNotification();
        
        if ($wdReqsCount > 0) {
            static::addToList('Un-Locked Student Grade','/grade-submission-summery',$wdReqsCount);
            // echo "<a href='/grade-submission-summery'  target='_blank'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} Un-Locked Student Grade' alt='' /></a>";
        }

        // $wdReqsCount = new \backend\models\CourseOffering2Detail();
        // $wdReqsCount = $wdReqsCount->RegistrarApproveNotification();
        
        // if ($wdReqsCount > 0) {
        //     static::addToList('Un-approved Course offering - ext','/course-offering2-ext',$wdReqsCount);
        //     // echo "<a href='/course-offering2-ext'  target='_blank'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} Un-approved Course offering' alt='' /></a>";
        // }

        $wdReqsCount = new \backend\models\CourseOfferingTemp();
        $wdReqsCount = $wdReqsCount->getRegistrarApproveRequest();
        
        if ($wdReqsCount > 0) {
            static::addToList('Un-approved Course offering','/approve-course-offering',$wdReqsCount);
            // echo "<a href='/approve-course-offering' target='_blank'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} Un-approved Course offering' alt='' /></a>";
        }

        $wdReqsCount = new \backend\models\AcadProfileChange();
        $wdReqsCount = $wdReqsCount->getRegistrarAlert();
        
        if ($wdReqsCount > 0) {
            static::addToList('Academic Profile Change Request','/acad-profile-change',$wdReqsCount);
            // echo "<a href='/acad-profile-change' target='_blank'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} Academic Profile Change Request' alt='' /></a>";
        }

        $wdReqsCount = new \frontend\models\SemesterRegistration2Detail();
        $wdReqsCount = $wdReqsCount->getRegistrarCAlert();
        
        if ($wdReqsCount > 0) {
            static::addToList('Course Alergic Student','/course-alergic',$wdReqsCount);
            // echo "<a href='/course-alergic'  target='_blank'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} Course Alergic Student record found.' alt='' /></a>";
        }

        $wdReqsCount = new \backend\models\StudentSemesterStatusHistory();
        $wdReqsCount = $wdReqsCount->getRegistrarAlert();
        
        if ($wdReqsCount > 0) {
            static::addToList('Semester Status Change request','/change-student-semester-status',$wdReqsCount);
            // echo "<a href='/change-student-semester-status'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} Student Semester Status Change request.' alt='' /></a>";
        }

        $wdReqsCount = new \backend\models\SupplementaryExam();
        $wdReqsCount = $wdReqsCount->generateRegistrarRequestAlert();
        
        if ($wdReqsCount > 0) {
            static::addToList('Suplementary Exam request','/request-sublimintary-exam-grade',$wdReqsCount);
            // echo "<a href='/request-sublimintary-exam-grade'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} Suplementary Exam request.' alt='' /></a>";
        }

        $wdReqsCount = new \backend\models\Student();
        $wdReqsCount = $wdReqsCount->generateRegistrarRequestAlert();
        
        if ($wdReqsCount > 0) {
            static::addToList('New Student Admission Entry','/student/approve-admission',$wdReqsCount);
            // echo "<a href='/request-sublimintary-exam-grade'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} Suplementary Exam request.' alt='' /></a>";
        }

        // $wdReqsCount = new \frontend\modules\DepartmentSelection\models\SelectionSettingEntry();
        // $wdReqsCount = $wdReqsCount->generateRegistrarRequestAlert();
        
        // if ($wdReqsCount > 0) {
        //     static::addToList('Department Selection Setting','/DepartmentSelection/selection-setting-entry/approve-setting',$wdReqsCount);
        //     // echo "<a href='/request-sublimintary-exam-grade'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} Suplementary Exam request.' alt='' /></a>";
        // }

        // $wdReqsCount = new \frontend\modules\DepartmentSelection\models\DepartmentSelectEntry();
        // $wdReqsCount = $wdReqsCount->generateRegistrarRequestAlert();
        
        // if ($wdReqsCount > 0) {
        //     static::addToList('Department Selection','/DepartmentSelection/department-select-entry/approve-selection',$wdReqsCount);
        //     // echo "<a href='/request-sublimintary-exam-grade'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} Suplementary Exam request.' alt='' /></a>";
        // }
    }

    private static function getHoDAlert() {
        // approves
        // course Grades
        // course re-assessment
        // 
        // reports
        static::__constructor();
        $ghInst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
        $instDept = $ghInst->inst_dep_relId;


        $reqs = \backend\models\ReassessmentHistory::find()
                ->where([
                    'ras_acadYear_relId' => static::$acadYearId,
                    'ras_sem_relId' => static::$semId,
                    'ras_status' => NULL,
                ])
                ->innerJoin('course', 'reassessment_history.ras_course_relId = course.id')
                ->andWhere(['course.course_dept_relId' => $instDept])
                ->count();
        if ($reqs > 0) {
            static::addToList('Re-assessment requests','/approve-reassessment-request',$reqs);
            // echo "<a href='/approve-reassessment-request'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$reqs} re-assessment requests for your dept. courses in this semester' alt='' /></a>";
        }

        $wdReqsCount = new \backend\models\WStudentWithdrawalHistory();
        $wdReqsCount = $wdReqsCount->checkHodRequest();
        
        if ($wdReqsCount > 0) {
            static::addToList('Withdrawal requests','/request-withdrawal/request-approve',$wdReqsCount);
            // echo "<a href='/request-withdrawal/request-approve'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} withdrawal requests' alt=''/></a>";
        }

        $wdReqsCount = new \backend\models\WStudentReadmissionHistory();
        $wdReqsCount = $wdReqsCount->checkHodRequest();
        
        if ($wdReqsCount > 0) {
            static::addToList('Readmission requests','/approve-readmission',$wdReqsCount);
            // echo "<a href='/approve-readmission'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} Readmission requests' alt='' /></a>";
        }

        // $wdReqsCount = new \frontend\models\SemesterRegistration2Detail();
        // $wdReqsCount = $wdReqsCount->getHodGradeApprovalRequest();
        
        // if ($wdReqsCount > 0) {
        //     static::addToList('Cours Grade Approval requests','/approve-grade',$wdReqsCount);
        //     // echo "<a href='/approve-grade'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} Cours Grade Approval requests' alt='' /></a>";
        // }

        // getHodAlert
        $wdReqsCount = new \backend\models\CourseOffering2Detail();
        $wdReqsCount = $wdReqsCount->getHodAlert();
        
        if ($wdReqsCount > 0) {
            static::addToList('Instructor Assignement','/assign-instructor',$wdReqsCount);
            // echo "<a href='/assign-instructor' style='margin-left:5px; cursor:pointer;' target='_blank'><img src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} instructor Assignement requests' alt='' /></a>";
        }

        // getHodAlert
        $wdReqsCount = new \backend\models\SemesterAdvisor();
        $wdReqsCount = $wdReqsCount->getHodNotification();
        
        if ($wdReqsCount > 0) {
            static::addToList('Semester Advisor','/semester-advisor',$wdReqsCount);
            // echo "<a href='/semester-advisor' target='_blank'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='Semester Advisor Assignement NOT completed.' alt='' /></a>";
        }

        // getHodAlert
        $wdReqsCount = new \backend\models\SupplementaryExam();
        $wdReqsCount = $wdReqsCount->generateHodRequestAlert();
        
        if (is_bool( $wdReqsCount) && $wdReqsCount == true) {
            static::addToList('Suplementary Exam','/request-sublimintary-exam-grade',$wdReqsCount);
            // echo "<a href='/request-sublimintary-exam-grade' target='_blank'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='Suplementary Exam Request available.' alt='' /></a>";
        }
    }

    private static function getInstCourses(){
        if (Yii::$app->user->can('Instructor') || Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findone(['inst_user_relId'=>Yii::$app->user->identity->id]);

            static::__constructor();
            // get courses assigned to this instructor.

           return \backend\models\CourseOffering2Detail::find()
                ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                ->where([
                    'course_offering2_detail.coff2det_inst_relId'=>$inst->id,
                ])
                ->andWhere(['course_offering2.coff2_sem_relId'=>static::$semId,'course_offering2.coff2_acadYear_relId'=>static::$acadYearId])
                ->groupBy('coff2det_coff2_relId')
                ->all();
        }
    }

    private static function getInstAlert() {
        // course add requests for the courses assigned for the instructor in this semseter.
        static::__constructor();

        if (Yii::$app->user->can('Instructor') || Yii::$app->user->can('DepartmentHead')) {

            $reqs = new \backend\models\AddHistory();
            $reqs = $reqs->getCourseInstNotification();
            if ($reqs > 0) {
                static::addToList('Course add requests','/approve-course-add',$reqs);
                // echo "<a href='/approve-course-add' target='_blank'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$reqs} course add requests.' alt=''/></a>";
            }
        }
    }

    private static function getStudentAlert() {
        $functionName = "getStudentlert";
        $moduleModels = ['../../frontend/models','../../backend/models'];

        foreach (Yii::$app->modules as $keyModule => $class) {

            $moduleModels[] = "../../frontend/modules/".$keyModule."/models";
            $moduleModels[] = "../../backend/modules/".$keyModule."/models";
        }

        // loop over those models
        foreach ($moduleModels as $modelString) {
            if (!file_exists($modelString)) {
                continue;
            }
            foreach (array_diff( scandir($modelString.'/'), array(".", "..") ) as $keyModels) {
                $className = str_replace(".php", "", $keyModels);
                $className = str_replace("../", "", $modelString.'/'.$className);
                $className = str_replace("/", "\\", $className);
                if (class_exists($className)) {
                    try {
                        if (method_exists($className::ClassName(), $functionName)) {
                            $className::ClassName()::getStudentlert();
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }
    }

    private static function getCEPClerkAlert() {
        $wdReqsCount = new \backend\models\SelfSponsoredPaymentHistory();
        $wdReqsCount = $wdReqsCount->getCEPAlert();
        
        if ($wdReqsCount > 0) {
            static::addToList('Payment approve request','/payment-request',$wdReqsCount);
            // echo "<a href='/payment-request'><img style='margin-left:5px; cursor:pointer;' src='/logo/red.gif' height='20' width='20' title='{$wdReqsCount} payment approve request(s)' alt=''/></a>";
        }
    }

    protected static function currentStudentGroup($stdObject) {
        static::__constructor();
        $gset = \backend\models\WGroupSetup::find()
                ->where([
                    'gs_acadYear_relId' => static::$acadYearId,
                    'gs_sem_relId' => static::$semId,
                    'gs_adm_relId' => $stdObject->std_admission_relId,
                    'gs_prog_relId' => $stdObject->std_prog_relId,
                ])
                ->innerJoin('w_group_assignment', 'w_group_setup.id = w_group_assignment.gas_gset_relId')
                ->andWhere(['w_group_assignment.gas_std_relId' => $stdObject->id])
                ->with('wGroupAssignments')
                ->one();
        return $gset;
    }

    // returns the Action of selected activity.
    protected static function getCont($id) {
        $not = \backend\models\AcademicCalanderAction::findOne(['id' => $id]);
        return $not;
    }

}
