<?php

namespace frontend\controllers;
use yii;
use yii\base\ErrorException;
use backend\models\AcadProfileChange;
use \common\components\CommonControllerFunctions as CCF;


/**
 * This is the model class for table "acad_profile_change".
 *
 * @property integer $id
 * @property integer $apc_std_relId
 * @property string $apc_changed
 * @property integer $apc_prevDept_relId
 * @property integer $apc_prevSpec_relId
 * @property integer $apc_newDept_relId
 * @property integer $apc_newSpec_relId
 * @property integer $apc_registrarApproved
 * @property string $apc_registrarApproved_at
 * @property integer $apc_registrarApproved_by
 * @property string $apc_created_at
 * @property integer $apc_created_by
 * @property string $apc_updated_at
 * @property integer $apc_updated_by
 *
 * @property User $apcCreatedBy
 * @property Department $apcNewDeptRel
 * @property Specialization $apcNewSpecRel
 * @property Department $apcPrevDeptRel
 * @property Specialization $apcPrevSpecRel
 * @property User $apcRegistrarApprovedBy
 * @property User $apcUpdatedBy
 */

class AcadProfileChangeController extends \yii\web\Controller
{

    public $acadYearId;
    public $semId;

    protected function __constructor() {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    public function actionIndex()
    {
        if (Yii::$app->user->can('Admin')) {
            return $this->render('index_Admin');
        }
        return $this->render('index');
    }

    private function getEnrolledCourses($std,$request){
        $cs = \frontend\models\SemesterRegistration2Detail::getEnrolledCourses($std,$request->apcPrevGroupRel);

        if ($cs && count($cs) > 0) {
            $css = "<table class='table table-striped table-hover table-condensed' style='font-size:8pt;'><tbody>";
            $totalCrdt = 0;
            foreach ($cs as $key) {
                $course = $key->srd2CourseRel;
                $css .= "<tr>
                        <td style='border:1px solid black;'>{$course->course_code}</td>
                        <td style='border:1px solid black;'>{$course->course_title}</td>
                        <td style='border:1px solid black;'>{$course->course_crdthr}</td>
                        </tr>";
                        $totalCrdt += $course->course_crdthr;
            }
            $css .= "<tr>
                <td colspan=2 style='border:1px solid black; text-align:center; font-style:italic; font-weight:bold'>Total Cedit Hr.</td>
                <td style='border:1px solid black; text-align:center; font-style:italic; font-weight:bold'><u>{$totalCrdt}</u></td>
            </tr>";
            $css .= "</tbody></table>";
        return $css;
        }
        return "<p class='bg-warning'>No Course Enrolled.</p>";
    }

    private function getCoursesToEnroll($gsetObject){
        $cs = \backend\models\CourseOffering2Detail::getOfferedCourses($gsetObject);

        if ($cs && count($cs) > 0) {
            $css = "<table class='table table-striped table-hover table-condensed' style='font-size:8pt;'><tbody>";
            $totalCrdt = 0;
            foreach ($cs as $key) {
                $course = $key->coff2detCourseRel;
                $css .= "<tr>
                        <td style='border:1px solid black;'>{$course->course_code}</td>
                        <td style='border:1px solid black;'>{$course->course_title}</td>
                        <td style='border:1px solid black;'>{$course->course_crdthr}</td>
                        </tr>";
                        $totalCrdt += $course->course_crdthr;
            }
            $css .= "<tr>
                <td colspan=2 style='border:1px solid black; text-align:center; font-style:italic; font-weight:bold'>Total Cedit Hr.</td>
                <td style='border:1px solid black; text-align:center; font-style:italic; font-weight:bold'><u>{$totalCrdt}</u></td>
            </tr>";
            $css .= "</tbody></table>";
        return $css;
        }
        return "<p class='bg-warning'>No Course Offered OR course offering NOT yet completed.</p>";
    }

    public function actionShowMore(){
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']):die('Invalid Entry...');

        $request = AcadProfileChange::findOne($id);

        if ($request && count($request) > 0) {
            $std = $request->apcStdRel;
            $user = $std->stdUserRel;

            $newGset = $request->apcNewGroupRel;

            $courseEnrolled = $this->getEnrolledCourses($std,$request);
            $coursesToEnroll = $this->getCoursesToEnroll($newGset);

            echo "<table class='table table-condensed table-striped table-bordered'>";
            echo "<caption style='font-weight:normal; '> 
                    Student Academic Profile change Request
                    <br>Requested to change <b><u>{$request->apc_changed}</u></b>
                    <br>Student Full Name : <b><u>{$user->first_name} {$user->middle_name} {$user->last_name}</u></b>
                </caption>";
            echo "<thead>
                    <th>Prev. Histroy</th>
                    <th>New Profile Histroy</th>
                    <th>Option</th>
                </thead><tbody>";
            echo "<tr>
                    <td>
                        <br>Prev. Department <b><u>{$std->stdDeptRel->dept_name}</u></b>
                        <br>Prev. Group: <b><u>{$request->apcPrevGroupRel->gsGroupRel->group_name}</u></b>
                        <br>Enrolled Courses: {$courseEnrolled}
                    </td>
                    <td>
                        <br>New Department <b><u>{$request->apcNewDeptRel->dept_name}</u></b>
                        <br>New. Group: <b><u>{$request->apcNewGroupRel->gsGroupRel->group_name}</u></b>
                        <br>Courses To Enroll: {$coursesToEnroll}
                    </td>
                    <td></td>
                </tr>";

            echo "</tbody></table>";
        }else{
            die('Such request doesn\'t exists!');
        }
    }

    public function actionChangeStatus(){
        $status = isset($_POST['status']) && !empty($_POST['status'])?intval($_POST['status']):die('Uknown status change');
        $reqId = isset($_POST['reqid']) && !empty($_POST['reqid'])?intval($_POST['reqid']):die('Invalid request entry, Please correct and retry.');

        $req = AcadProfileChange::find()->where(['id'=>$reqId,'apc_registrarApproved'=>NULL])->one();

        if (!(Yii::$app->user->can('Registrar'))) {//Only registrar head is allowed fr this operation..
           echo "<p class='bg-danger'>Unsupported Operation.</p>";die(); 
        }

        if ($req && count($req) > 0) {
            // var_dump($req);die();
            // update
            $transaction = Yii::$app->db->beginTransaction();
            try{
                $result = Yii::$app->db->createCommand()
                    ->update('acad_profile_change',
                        [
                            'apc_registrarApproved'=>$status,
                            'apc_registrarApproved_at'=>date('Y-m-d h:i:s'),
                            'apc_registrarApproved_by'=>Yii::$app->user->identity->id,
                        ],
                        ['id'=>$reqId])
                    ->execute();

                if ($result) {

                    $std = NULL;
                    $updateGroup = 0;
                    // $changed = ?'std_dept_relId':($req->apc_changed == 'spec'?'std_spec_relId')
                    // update student profile and status similarly.
                    if ($req->apc_changed == 'dept') {
                        $std = Yii::$app->db->createCommand()
                            ->update('student',
                                [
                                    'std_dept_relId'=>$req->apc_newDept_relId,
                                    'std_spec_relId'=>NULL,
                                    'std_group_realId'=>$req->apc_newGroup_relId
                                ],
                                ['id'=>$req->apc_std_relId]);
                            $updateGroup = 1;
                    }else if ($req->apc_changed == 'spec') {
                        $std = Yii::$app->db->createCommand()
                            ->update('student',
                                [
                                    'std_spec_relId'=>$req->apc_newSpec_relId,
                                    'std_group_realId'=>$req->apc_newGroup_relId
                                ],
                                ['id'=>$req->apc_std_relId]);
                            $updateGroup = 1;
                    }else if ($req->apc_changed == 'spon') {
                        $std = Yii::$app->db->createCommand()
                            ->update('student',
                                [
                                    'std_schlrshpType_relId'=>$req->apc_newSpon_relId
                                ],
                                ['id'=>$req->apc_std_relId]);
                    }else{
                        throw new ErrorException("Uknown status change.", 1);
                    }

                    if (!is_null($std)) {
                        if ($std->execute()) {
                            if ($updateGroup == 1) {
                                $ressss = Yii::$app->db->createCommand()
                                    ->update(
                                        'w_group_assignment',
                                        ['gas_gset_relId'=>$req->apc_newGroup_relId],
                                        [
                                            'gas_std_relId'=>$req->apc_std_relId,
                                            'gas_gset_relId'=>$req->apc_prevGroup_relId,
                                        ])
                                    ->execute();
                                if ($ressss) {
                                    // remove all enrolled courses for this semester.
                                    $ress2 = \frontend\models\SemesterRegistration2Detail::deleteEnrolledCourses($req->apc_prevGroup_relId,$req->apc_std_relId);
                                    $transaction->commit();
                                    echo "1";
                                    die();
                                }else{
                                   throw new ErrorException("Error Processing Request, Please try again...", 1);
                                }
                            }else{
                                $transaction->commit();
                                echo "1";
                                die();
                            }
                        }else{
                            throw new ErrorException("Error Processing Request, Please try again...", 1);
                        }
                    }
                }
            }catch(Exception $e){
                $transaction->rollBack();
                echo "<p class='bg-danger'>{$e->getMessage()}</p>";
                die();
            }            
        }else{
            echo "<p class='bg-danger'>Such request not found. Any cahnge made?</p>";die();
        }
    }

    public function actionChangeIt(){
        $changed = isset($_POST['changed']) && !empty($_POST['changed'])?intval($_POST['changed']):die('Uknown change option....');
        $stdId = isset($_POST['stdId']) && !empty($_POST['stdId'])?intval($_POST['stdId']):die('Uknown change option....');

        // create student change report and display it back

        $std = \backend\models\Student::findOne($stdId);

        $user = \backend\models\User::findOne($std->std_user_relId);

        $table  ="<table class='table table table-striped' style='font-size:10pt; width:100%;'>";
        if ($changed == 1) {//dept
            $this->__constructor();
            $ay = NULL;
            $sem = NULL;

            // going to request dept change to student.

            $table .= "<caption style='text-align:center;'><h4>Change Student Department</h4></caption>";

            $table .= "<thead style='background-color:#8d0;'>
                    <th>Previous profile</th>
                    <th colspan=2>New Profile</th>
                    </thead><tbody>";

            $prevGroup = $std->stdGroupReal;

            $ay = $prevGroup->gs_acadYear_relId;
            $sem = $prevGroup->gs_sem_relId;
            if ($this->acadYearId != $prevGroup->gs_acadYear_relId || $this->semId != $prevGroup->gs_sem_relId) {
                $ay = $this->acadYearId;
                $sem = $this->semId;
            }

            $spec = is_null($prevGroup->gs_spec_relId)?'0':$prevGroup->gs_spec_relId;
            // var_dump($std->stdClslvlRel->id);

            $prevDept = \backend\models\Department::findOne($std->std_dept_relId);
            $newDept = "<select class='form-control' data-prevgroup='{$prevGroup->id}' data-clslvl='{$std->stdClslvlRel->id}' title='select new Department of the student.' id='new_dept_select' onchange='cont($(this).val(),{$std->std_admission_relId},{$std->std_prog_relId},{$spec},{$ay},{$sem},{$std->stdClslvlRel->id})'>";

            $newGroup = "
                <select class='form-control' title='select new Department of the student.' id='new_group_select' onchange='$(\".btn\").show()'>
                    <option value=''> - </option>
                </select>";
            $depts = \backend\models\Department::find()->where(['<>','id',$prevDept->id])->all();
            // var_dump($depts);die();

            if ($depts && count($depts) > 0) {
                $newDept .= "<option value=''> - </option>";
                foreach ($depts as $key) {
                    $newDept .= "<option value='{$key->id}'>{$key->dept_name}</option>";
                }
            }
            $newDept .= "</select>";

            $table .= "<tr  style='text-align:left;'>
                        <td>
                            <b>Reg. ID:</b> <u>{$std->std_regId}</u><br>
                            <b>Student Full Name:</b> <u>{$user->first_name} {$user->middle_name}</u><br>
                            <b>Previous Department:</b> <u>{$prevDept->dept_name}</u><br>
                            <b>Previous Group:</b> <u>{$prevGroup->gsGroupRel->group_name}</u><br>
                        </td>
                        <td>
                            <label for='new_dept_select'>Select new Department</label>
                            {$newDept}
                        </td>
                        <td>
                            <label for='new_group_select'>Select new Group</label>
                            {$newGroup}
                        </td>
                    </tr>
                    <tr>
                        <td id='message_port'></td>
                        <td colspan=2><button class='btn btn-success btn-xs' id='svvvv' onclick='save12({$changed},{$std->id},{$prevDept->id})' style='float:right; display:none'>Save</button></td>
                    </tr>";
            $table .= "</tbody>";
        }else if ($changed == 2) {//spec
            
            $table .= "<caption style='text-align:center;'><h4>Change Student Specialization</h4></caption>";

            $table .= "<thead style='background-color:#8d0;'><th>Previous profile</th><th>New Profile</th></thead><tbody>";

            $prevSpecNmae = '<i style="color:red;">Not Assigned yet.</i>';
            $prevSpecId = NULL;
            if (!is_null($std->std_spec_relId)) {
                $prevSpec = \backend\models\Specialization::findOne($std->std_spec_relId);
                $prevSpecId = $prevSpec->id;
                $prevSpecNmae = $prevSpec->spec_name;
            }
            $ddept = \backend\models\Department::findOne($std->std_dept_relId);
            $newDept = "<select class='form-control' title='select new Specialization of the student.' id='new_dept_select' onchange='$(\".btn\").show()'>";

            $depts = \backend\models\Specialization::find()
                ->where(['IS NOT','id',$prevSpecId])
                ->andWhere(['spec_dep_relId'=>$std->std_dept_relId])
                ->andWhere(['spec_prog_relId'=>$std->std_prog_relId])
                ->andWhere(['spec_adm_relId'=>$std->std_admission_relId])
                ->all();
            // var_dump($depts);die();

            if ($depts && count($depts) > 0) {
                $newDept .= "<option value=''> - </option>";
                foreach ($depts as $key) {
                    $newDept .= "<option value='{$key->id}'>{$key->spec_name}</option>";
                }
            }
            $newDept .= "</select>";

            $table .= "<tr  style='text-align:left;'>
                        <td>
                            <b>Reg. ID:</b> <u>{$std->std_regId}</u><br>
                            <b>Student Full Name:</b> <u>{$user->first_name} {$user->middle_name}</u><br>
                            <b>Department:</b> <u>{$ddept->dept_name}</u><br>
                            <b>Previous Specialization:</b> <u>{$prevSpecNmae}</u><br>
                        </td>
                        <td>
                        <label for='new_dept_select'>Select new Specialization</label>
                        {$newDept}</td>
                    </tr>
                    <tr>
                        <td id='message_port'></td>
                        <td><button class='btn btn-success btn-xs' id='svvvv' onclick='save12({$changed},{$std->id},{$prevSpecId})' style='float:right; display:none'>Save</button></td>
                    </tr>";
            $table .= "</tbody>";
        }else if ($changed == 3) {//sponsorship
            $table .= "<caption style='text-align:center;'><h4>Change Student Sponsorship type</h4></caption>";

            $table .= "<thead style='background-color:#8d0;'><th>Previous Sponsorship information</th><th>New Profile</th></thead><tbody>";

            $prevDept = \backend\models\ScholarshipType::findOne($std->std_schlrshpType_relId);
            $newDept = "<select class='form-control' title='select new sponsorship type of the student.' id='new_dept_select' onchange='$(\".btn\").show()'>";

            $depts = \backend\models\ScholarshipType::find()->where(['<>','id',$prevDept->id])->all();
            // var_dump($depts);die();

            if ($depts && count($depts) > 0) {
                $newDept .= "<option value=''> - </option>";
                foreach ($depts as $key) {
                    $newDept .= "<option value='{$key->id}'>{$key->schType_name}</option>";
                }
            }
            $newDept .= "</select>";

            $table .= "<tr  style='text-align:left;'>
                        <td>
                            <b>Reg. ID:</b> <u>{$std->std_regId}</u><br>
                            <b>Student Full Name:</b> <u>{$user->first_name} {$user->middle_name} {$user->last_name}</u><br>
                            <b>Previous Sponsoreship:</b> <u>{$prevDept->schType_name}</u><br>
                        </td>
                        <td>
                        <label for='new_dept_select'>Select new Scholarship Type</label>
                        {$newDept}</td>
                    </tr>
                    <tr>
                        <td id='message_port'></td>
                        <td><button class='btn btn-success btn-xs' id='svvvv' onclick='save12({$changed},{$std->id},{$prevDept->id})' style='float:right; display:none'>Save</button></td>
                    </tr>";
            $table .= "</tbody>";
        }

        $table .= "</table>";
        echo "<div class='container' style=''>{$table}</div>";die();
    }
    public function actionGetRecord(){

        $requests = AcadProfileChange::find()->where(['apc_registrarApproved'=>NULL])->orderBy('apc_created_at')->all();

        $table = "";
        if ($requests && count($requests) > 0) {

            $table = "<table class='table table-hover table-condensed table-sm table-bordered' style='background-color: #999'>";
            $table .= "<caption style='text-align:center;'> Student Academic Profile Change Requests</caption>";
            $table .= "<thead style='background-color:#8D0'>
                    <th>#</th>
                    <th>REG. ID</th>
                    <th>Student Full Name</th>
                    <th>Change Made</th>
                    <th>FROM</th>
                    <th>TO</th>
                    <th>More</th>
                    <th>Approval</th>
                    </thead><tbody>";
                    $count = 0;
            foreach ($requests as $key) {
                $std = \backend\models\Student::findOne($key->apc_std_relId);
                $user = \backend\models\User::findOne($std->std_user_relId);
                
                $changed = NULL;
                $from = NULL;
                $new = NULL;
                $show = NULL;
                if ($key->apc_changed == 'dept') {
                   $changed = 'Department';
                   $from = \backend\models\Department::findOne($key->apc_prevDept_relId)->dept_name;
                   $new = \backend\models\Department::findOne($key->apc_newDept_relId)->dept_name;
                   $show = "<button class='btn btn-xs btn-primary' onclick='showMore({$key->id})'>Show</button>";
                }else if ($key->apc_changed == 'spec') {
                    $changed = 'Specialization';
                    $from = is_null($key->apc_prevDept_relId)?'-':\backend\models\Specialization::findOne($key->apc_prevSpec_relId)->spec_name;
                    $new = is_null($key->apc_newSpec_relId)?'-':\backend\models\Specialization::findOne($key->apc_newSpec_relId)->spec_name;
                }else if ($key->apc_changed == 'spon') {
                     $changed = 'Sponsorship type';
                     $from = \backend\models\ScholarshipType::findOne($key->apc_prevSpon_relId)->schType_name;
                    $new = \backend\models\ScholarshipType::findOne($key->apc_newSpon_relId)->schType_name;
                }else{
                    $changed = 'Uknown';
                }
                // $changed = $key->apc_changed == 'dept'?'Department':(?'Specialization':(?'':'Uknown'));
                $table .= "
                        <tr>
                            <td>".++$count."</td>
                            <td>{$std->std_regId}</td>
                            <td>{$user->first_name} {$user->middle_name} {$user->last_name}</td>
                            <td>{$changed}</td>
                            <td>{$from}</td>
                            <td>{$new}</td>
                            <td>{$show}</td>
                            <td style='text-align:center;'>
                                <button class='btn btn-success btn-xs' onclick='changeStatus(1,{$key->id})' >Approve</button>
                            </td>
                        </tr>";
            }
            $table .= "</tbody></table>";

            echo $table;
        }else{
            echo "<p class='bg-danger'>No request.</p>";
        }
        die();
    }

    public function actionGetApproveReport(){
        $requests = AcadProfileChange::find()
            ->where(['OR',
                ['apc_registrarApproved'=>1],
                ['apc_registrarApproved'=>0]
            ])
            ->orderBy('apc_created_at DESC')
            ->all();

        $table = "";
        if ($requests && count($requests) > 0) {

            $table = "<table class='table table-striped table-hover table-condensed table-sm table-bordered'>";
            $table .= "<caption style='text-align:center; background-color:#6FD'> Student Academic Profile Change Approve Report</caption>";
            $table .= "<thead style='background-color:#8D0'>
                    <th>#</th>
                    <th>REG. ID</th>
                    <th>Student Full Name</th>
                    <th>Change Made</th>
                    <th>FROM</th>
                    <th>TO</th>
                    <th style='text-align:center;'>Approval</th>";
            if (Yii::$app->user->can('Registrar')) {
                $table .= "<th style='text-align:center;'>Change Back</th>";
            }
            $table .= "
                    </thead><tbody>";
                    $count = 0;
            foreach ($requests as $key) {
                $std = \backend\models\Student::findOne($key->apc_std_relId);
                $user = \backend\models\User::findOne($std->std_user_relId);
                
                $changed = NULL;
                $from = NULL;
                $new = NULL;

                $approveStatus = $key->apc_registrarApproved == 1?'Approved':'Rejected';
                if ($key->apc_changed == 'dept') {
                   $changed = 'Department';
                   $from = \backend\models\Department::findOne($key->apc_prevDept_relId)->dept_name;
                   $new = \backend\models\Department::findOne($key->apc_newDept_relId)->dept_name;
                }else if ($key->apc_changed == 'spec') {
                    $changed = 'Specialization';
                    $from = is_null($key->apc_prevDept_relId)?'-':\backend\models\Specialization::findOne($key->apc_prevSpec_relId)->spec_name;
                    $new = is_null($key->apc_newSpec_relId)?'-':\backend\models\Specialization::findOne($key->apc_newSpec_relId)->spec_name;
                }else if ($key->apc_changed == 'spon') {
                     $changed = 'Sponsorship type';
                     $from = \backend\models\ScholarshipType::findOne($key->apc_prevSpon_relId)->schType_name;
                    $new = \backend\models\ScholarshipType::findOne($key->apc_newSpon_relId)->schType_name;
                }else{
                    $changed = 'Uknown';
                }
                // $changed = $key->apc_changed == 'dept'?'Department':(?'Specialization':(?'':'Uknown'));
                $table .= "
                        <tr>
                            <td>".++$count."</td>
                            <td>{$std->std_regId}</td>
                            <td>{$user->first_name} {$user->middle_name} {$user->last_name}</td>
                            <td>{$changed}</td>
                            <td>{$from}</td>
                            <td>{$new}</td>
                            <td style='text-align:center;'>
                                {$approveStatus}
                            </td>";
                if (Yii::$app->user->can('Registrar')) {
                    $table .= "<td style='text-align:center;'><button class='btn btn-warning btn-xs' title='change back the request approval status' onclick='changeBack({$key->id})'>Change</button></td>";
                }
                $table .= "</tr>";
            }
            $table .= "</tbody></table>";

            echo $table;
        }else{
            echo "<p class='bg-danger'>No request.</p>";
        }
        die();
    }


    public function actionCreate(){
        $model = new AcadProfileChange();

        $model->apc_std_relId = isset($_POST['stdId']) && !empty($_POST['stdId'])? intval($_POST['stdId']):die('Uknown Student ID provided.');
        $changed = isset($_POST['changed']) && !empty($_POST['changed'])? intval($_POST['changed']):die('Uknown Status Change.');
        // var_dump($changed);die();
        if ($changed==1) {
            $model->apc_changed ='dept';
            $model->apc_newDept_relId = $_POST['new2'] == '0'?NULL:intval($_POST['new2']);
            $model->apc_prevDept_relId = $_POST['prev'] == '0'?NULL:intval($_POST['prev']) ;
            $model->apc_prevGroup_relId = $_POST['prevg'] == '0'?NULL:intval($_POST['prevg']);
            $model->apc_newGroup_relId = $_POST['newg'] == '0'?NULL:intval($_POST['newg']);
        }else if ($changed == 2) {
            $model->apc_changed = 'spec';
            $model->apc_prevSpec_relId = $_POST['prev'] == '0'?NULL:intval($_POST['prev']) ;
            $model->apc_newSpec_relId = $_POST['new2'] == '0'?NULL:intval($_POST['new2']);
            $model->apc_prevGroup_relId = $_POST['prevg'] == '0'?NULL:intval($_POST['prevg']);
            $model->apc_newGroup_relId = $_POST['newg'] == '0'?NULL:intval($_POST['newg']);
        }else if ($changed == 3) {
            $model->apc_changed = 'spon';
            $model->apc_prevSpon_relId = $_POST['prev'] == '0'?NULL:intval($_POST['prev']) ;
            $model->apc_newSpon_relId = $_POST['new2'] == '0'?NULL:intval($_POST['new2']);
        }else{
            die('Uknown Status change');
        }
        // $changed==1?'dept':($changed == 2?'spec':($changed == 3?'spon':));
        // $model->apc_prevGroup_relId = $_POST['prevGroup'] == '0'?NULL:intval($_POST['prevGroup']);
        $model->apc_created_at = date('Y-m-d h:i:s');
        $model->apc_created_by = Yii::$app->user->identity->id;


        // 'apc_changed'=>$changed,
        $exists = \backend\models\AcadProfileChange::find()
            ->where([
                'apc_std_relId'=>$model->apc_std_relId
            ])
            ->andWhere(['apc_registrarApproved'=>NULL])
            ->one();

        if ($exists && count($exists) > 0) {
            $btn = "<button class='btn btn-link btn-xs' onclick='deleteRequest({$exists->id})'> Here </button>";
            echo "<p class='bg-danger'>You have academic profile change request waiting for approval from registrar.You can Cance request by Clicking {$btn}</p>";
            die();
        }
        if ($model->save()) {
            echo "<p class='bg-success'>Profile change request sent succesfully, Please wait for registrar approval..</p>";
        }else{
            var_dump($model->getErrors());die();
        }

    }

    public function actionDeleteRequest(){
        $id = isset($_POST['id']) && !empty($_POST['id'])?intval($_POST['id']):die('Invalid request Entry');
        echo AcadProfileChange::findOne($id)->delete();
    }

    public function actionChangeStudentSemesterStatusBack(){
        $reqId = isset($_POST['recordId']) && !empty($_POST['recordId'])?intval($_POST['recordId']):die('Invalid request status. Please correct it and retry again');

        // roll back

        $req = AcadProfileChange::find()
            ->where(['id'=>$reqId])
            ->andWhere(['OR',['apc_registrarApproved'=>0],['apc_registrarApproved'=>1]])
            ->one();
        if ($req && count($req) > 0) {
            $transaction = Yii::$app->db->beginTransaction();
            try{

               $result = Yii::$app->db->createCommand()
                    ->update('acad_profile_change',
                        [
                            'apc_registrarApproved'=>NULL,
                            'apc_registrarApproved_at'=>NULL,
                            'apc_registrarApproved_by'=>NULL,
                        ],
                        ['id'=>$reqId])
                    ->execute();

                if ($result) {

                    $std = NULL;
                    // $changed = ?'std_dept_relId':($req->apc_changed == 'spec'?'std_spec_relId')
                    // update student profile and status similarly.
                    if ($req->apc_changed == 'dept') {
                        $std = Yii::$app->db->createCommand()
                            ->update('student',
                                [
                                    'std_dept_relId'=>$req->apc_prevDept_relId,
                                    'std_spec_relId'=>NULL,
                                    'std_group_real_id'=>$req->apc_prevGroup_relId
                                ],
                                ['id'=>$req->apc_std_relId]);
                    }else if ($req->apc_changed == 'spec') {
                        $std = Yii::$app->db->createCommand()
                            ->update('student',
                                [
                                    'std_spec_relId'=>$req->apc_prevSpec_relId,
                                    'std_group_real_id'=>$req->apc_prevGroup_relId
                                ],
                                ['id'=>$req->apc_std_relId]);
                    }else if ($req->apc_changed == 'spon') {
                        $std = Yii::$app->db->createCommand()
                            ->update('student',
                                [
                                    'std_schlrshpType_relId'=>$req->apc_prevSpon_relId
                                ],
                                ['id'=>$req->apc_std_relId]);
                    }else{
                        throw new ErrorException("Uknown status change.", 1);
                    }

                    if (!is_null($std)) {
                        if ($std->execute()) {
                            $transaction->commit();
                            echo "1";
                            die();
                        }else{
                            throw new ErrorException("Error Processing Request, Please try again...", 1);
                        }
                    }
                }else{
                    throw new ErrorException("Error Processing Request, Please try again...", 1);
                }
            }catch(Exception $e){
                $transaction->rollBack();

                echo "<p class='bg-danger'>{$e->getMessage()}</p>";die();
            }
        }else{
            echo "<p class='bg-danger'>Invalid Request , any change made?</p>";die();
        }
    }
}
