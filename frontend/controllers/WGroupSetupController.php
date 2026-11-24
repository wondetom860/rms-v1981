<?php

namespace frontend\controllers;

use Yii;
use backend\models\WGroupSetup;
use backend\models\WGroup;
use backend\models\User;
use backend\models\Semester;
use backend\models\Student;
use backend\models\SchoolYear;
use backend\models\WGroupAssignment;
use backend\models\WGroupSetupSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use backend\models\SecSpecRecomend;
use \frontend\models\SemesterRegistration2Detail;
use \common\components\CommonControllerFunctions as CCF;

/**
 * WGroupSetupController implements the CRUD actions for WGroupSetup model.
 */
class WGroupSetupController extends Controller
{

    public $acadYearId;
    public $semId;

    protected function __constructor()
    {

        $activeAcademicYear = null;
        $ac = Yii::$app->AcademicCalendar->getActiveProgramModels(null);
        if ($ac && count($ac) > 0) {
            $activeAcademicYear = $ac[0];
        } else {
            return "Academic year is not active";
        }
        $thisSem = $activeAcademicYear->semesterRel;
        $this->acadYearId = $activeAcademicYear->schoolYearRel->id;
        $this->semId = $thisSem->id;
    }

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
                        'actions' => [],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Admin'],
                    ],
                    [
                        'actions' => ['get-assignments2'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['DepartmentHead', 'Registrar'],
                    ],
                    [
                        'actions' => ['index', 'get-gsets', 'get-group', 'get-students', 'get-batch', 'getlist-report', 'mark-internship-group', 'get-assignments', 'get-group-lists', 'print-student-list', 'get-group-lists-peryear'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all WGroupSetup models.
     * @return mixed
     */
    public function actionIndex()
    {

        $this->__constructor();
        $searchModel = new WGroupSetupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->acadYearId, $this->semId);
        $model = new WGroupSetup();

        if (Yii::$app->user->can('DepartmentHead')) {
            return $this->render('__report/index-for-hod', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'model' => $model,
            ]);
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    public function actionGetAssignments2()
    { //get-assignments?wt_gsetUp_entry=
        $gsetId = isset($_GET['wt_gsetUp_entry']) && !empty($_GET['wt_gsetUp_entry']) ? intval($_GET['wt_gsetUp_entry']) : die('Invalid ReqUest');
        $pts = isset($_GET['pts']) && !empty($_GET['pts']) ? intval($_GET['pts']) : null;

        $gsetModel = $this->findModel($gsetId);

        // sort student list
        // $this->layout = 'printLayout';
        $sortedList = $gsetModel->getWGroupAssignments()
            ->innerJoin('student', 'student.id=w_group_assignment.gas_std_relId')
            ->innerJoin('user', 'user.id = student.std_user_relId')
            ->orderBy(['user.first_name' => SORT_ASC, 'user.middle_name' => SORT_ASC])
            ->all();

        $_SESSION['report'] = 'Student List';

        return $this->renderPartial('__report/assignments', ['model' => $gsetModel, 'assignments' => $sortedList, 'pts' => 1]);
    }

    public function actionGetAssignments()
    { //get-assignments?wt_gsetUp_entry= 
        $gsetId = isset($_GET['wt_gsetUp_entry']) && !empty($_GET['wt_gsetUp_entry']) ? intval($_GET['wt_gsetUp_entry']) : die('Invalid ReqUest');

        $gsetModel = $this->findModel($gsetId);

        // sort student list

        $sortedList = $gsetModel->getWGroupAssignments()
            ->innerJoin('student', 'student.id=w_group_assignment.gas_std_relId')
            ->innerJoin('user', 'user.id = student.std_user_relId')
            ->orderBy(['user.first_name' => SORT_ASC, 'user.middle_name' => SORT_ASC])
            ->all();

        return $this->renderPartial('__report/assignments', ['model' => $gsetModel, 'assignments' => $sortedList]);
    }

    public function actionGetGsets()
    {
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('Academic Year NOT Selected.');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : NULL;
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('Please Select Semester');

        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Admission NOT Selected.');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Program NOT Selected.');

        $gsets = WGroupSetup::find()
            ->where([
                'gs_adm_relId' => $admId,
                'gs_prog_relId' => $progId,
                'gs_acadYear_relId' => $acadYearId,
                'gs_sem_relId' => $semId,
            ]);
        if (!is_null($clslvlId)) {
            $gsets->andWhere(['gs_clslvl_relId' => $clslvlId]);
        }

        return $this->renderPartial('internship-group/gset-list', ['gsetModels' => $gsets->all()]);
    }

    public function actionUnmarkInternship()
    {
        $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : die('INVAlid ReqUest');

        if (\Yii::$app->user->can('Admin')) {
            return Yii::$app->db->createCommand()
                ->update('w_group_setup', ['gs_internship' => NULL], ['id' => $gsetId])
                ->execute();
        }
    }

    public function actionMarkGsetLast()
    {
        $gsetList = isset($_POST['internshipGsets']) && !empty($_POST['internshipGsets']) ? (string)$_POST['internshipGsets'] : die('INVAliD ReqUest');

        $gsetList = json_decode($gsetList);

        $totalCount = count($gsetList);

        if ($gsetList > 0 && \Yii::$app->user->can('Admin')) {
            return Yii::$app->db->createCommand()
                ->update('w_group_setup', ['gs_internship' => 1], ['id' => $gsetList])
                ->execute();
        }
    }

    public function actionMarkInternshipGroup()
    {
        return $this->render('internship-group/mark-internship-group', ['model' => new WGroupSetup()]);
    }

    private function getImmidiatePrevGset($clslvlObj, $stdObj, $cSemObj)
    {
        $semObj = Semester::findOne($this->semId);
        $ps = $semObj->semester_name == 'I' ?
            ($stdObj->stdAdmissionRel->admission_name == 'Extension' ? ('Summer') : ('II')) : ($semObj->semester_name == 'Summer' ? 'II' : 'I');
        $cs = $semObj->semester_name == 'I' ? $clslvlObj->clslvl_num - 1 : ($stdObj->stdAdmissionRel->admission_name == 'Extension' && $ps == 'II' ? ($clslvlObj->clslvl_num - 1) : ($clslvlObj->clslvl_num));
        $prevSemObj = Semester::findOne(['semester_name' => $ps]);
        $prevClslvlbj = \backend\models\ClassLevel::findOne(['clslvl_num' => $cs]);

        return CCF::getStudentGroupHistoryPublic($prevClslvlbj->id, $prevSemObj->id, $stdObj->id);
    }

    // returns the list of students / groups WRT user selection  get-group-list
    public function actionGetGroupList()
    {
        $this->__constructor();

        // returns the list of students if it is for the first time.
        // $deptId = Yii::$app->request->post('deptId');
        // $progId = Yii::$app->request->post('progId');
        // $clslvl = Yii::$app->request->post('clslvlId');
        // $admId = Yii::$app->request->post('admId');
        // $specId =Yii::$app->request->post('specId');

        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Admission NOT Selected.');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Program NOT Selected.');
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId']) ? intval($_POST['deptId']) : die('Department NOT Selected.');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Class Level NOT Selected.');
        $specId = isset($_POST['specId']) && !empty($_POST['specId']) ? intval($_POST['specId']) : NULL;

        // var_dump(is_null($specId));
        // var_dump($admId,$progId,$deptId, $clslvlId,$specId);die();


        $entryType = Yii::$app->request->post('entryId');


        if ($entryType == 0) {
            // New entry students class level is 1:
            $clsObj = \backend\models\Classlevel::findOne($clslvlId);
            // var_dump($clsObj->clslvl_num);
            if ($clsObj->clslvl_num != 1 && $clsObj->clslvl_num != 6) {
                return "Invalid Selection";
            }

            if (!is_null($specId)) {
                $stds = Student::find()
                    ->innerJoin('user', 'user.id = student.std_user_relId')
                    ->where([
                        'std_admission_relId' => $admId,
                        'std_prog_relId' => $progId,
                        'std_dept_relId' => $deptId,
                        'std_spec_relId' => $specId,
                        'std_acadYear_relId' => $this->acadYearId,
                        'std_group_realId' => NULL
                    ])
                    ->andWhere(['IS NOT', 'std_admission_approved_by', NULL])
                    ->orderBy(['user.first_name' => SORT_ASC, 'user.middle_name' => SORT_ASC])
                    ->all();

                // $stds = $stds->andWhere(['std_spec_relId'=>]);
            } else {
                $stds = Student::find()
                    ->innerJoin('user', 'user.id = student.std_user_relId')
                    ->where([
                        'std_admission_relId' => $admId,
                        'std_prog_relId' => $progId,
                        'std_dept_relId' => $deptId,
                        'std_clslvl_relId' => $clslvlId,
                        'std_acadYear_relId' => $this->acadYearId,
                        'std_group_realId' => NULL
                    ])
                    ->andWhere(['IS NOT', 'std_admission_approved_by', NULL])
                    ->orderBy(['user.first_name' => SORT_ASC, 'user.middle_name' => SORT_ASC])
                    ->all();
            }

            // $stds = $stds->all();

            if (empty($stds) && count($stds) < 1) {
                echo "<p class='bg-danger'>No Un-grouped new entry student found / all the new entry students are grouped. </p>";
                die();
            } else {
                $setUp = WGroupSetup::findOne([
                    'gs_acadYear_relId' => $this->acadYearId,
                    'gs_sem_relId' => $this->semId,
                    'gs_dept_relId' => $deptId,
                    'gs_adm_relId' => $admId,
                    'gs_prog_relId' => $progId,
                    'gs_clslvl_relId' => $clslvlId,
                ]);

                echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
                echo "<caption><p class='bg-success'>Ungrouped students.</p></caption>";
                echo "<thead>
                        <th>#</th>
                        <th>Select</th>
                        <th>Reg. ID.</th>
                        <th>Student Name</th>
                        <th>Year Of Adm.</th>
                        <th>Spec.</th>
                    </thead><tbody>";
                $count2 = 0;
                foreach ($stds as $std) {
                    // $id = empty($setUp)?$$setUp->id:NULL;
                    if ($setUp) {
                        $ass = WGroupAssignment::findOne(['gas_gset_relId' => $setUp->id, 'gas_std_relId' => $std['id']]);
                        if (!empty($ass)) {
                            continue;
                            // echo "Assigned First.";die();
                        }
                    }

                    $usr = User::findOne(['id' => $std['std_user_relId']]);
                    $spec = $std['std_spec_relId'] ? \backend\models\Specialization::findOne(['id' => $std['std_spec_relId']])->spec_name : 'N/A';
                    $grp = 'N/A';
                    if (!is_null($std['std_group_realId'])) {
                        $gset = WGroupSetup::findOne(['id' => $std['std_group_realId']]);

                        if ($gset) {
                            $grp = WGroup::findOne(['id' => $gset->gs_group_relId])->group_name;
                        }
                    }
                    echo "<tr>
                        <td>" . ++$count2 . "</td>
                        <td><input type='checkbox' onchange='addToGroup(" . $std['id'] . ")' /></td>
                        <td>" . $std['std_regId'] . "</td>
                        <td>{$std->getFullName()}</td>
                        <td>" . SchoolYear::findOne(['id' => $std['std_acadYear_relId']])->sch_year_desc . "</td>
                        <td>" . $spec . "</td>
                </tr>";
                }
                echo "</tbody></table>";
            }
        } else if ($entryType == 2) { //load readmitted students for this academic year.

            $prevSemId = Yii::$app->request->post('prevSemId');
            $prevAcadYearId = Yii::$app->request->post('prevAcadYearId');

            $wrtObject = new \backend\models\WStudentWithdrawalHistory();
            $wrt = $wrtObject->find()
                ->innerJoin('student', 'student.id = w_student_withdrawal_history.swh_std_relid')
                ->where([
                    'swh_lastApproved' => 1,
                    'swh_HOD_approved' => 1,
                    'swh_Approved' => 1,
                    'swh_acadYear_relId' => $prevAcadYearId,
                    'swh_sem_relId' => $prevSemId,
                ])
                ->andWhere([
                    'std_admission_relId' => $admId,
                    'std_prog_relId' => $progId,
                    'std_dept_relId' => $deptId,
                ])
                ->all();

            // ->andWhere(['IS NOT','swh_returnedBack',NULL,])
            // ->andWhere([
            //     'student.std_semester_status'=>'Readmission in-proccess',
            // ])
            if ($wrt) {
                echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
                echo "<caption><p class='bg-success'>Readmitted Students.</p></caption>";
                echo "<thead style='background-color:#8D0;'>
                        <th>Select</th>
                        <th>Reg. ID.</th>
                        <th>Student Name</th>
                        <th>Prev. Grp.</th>
                        <th>Spec.</th>
                    </thead>
                    <tbody>";
                foreach ($wrt as $WRTkey) {
                    // $validSWObj = $wrtObject->hasValidWithdrawal($WRTkey->swhStdRel);
                    $yearObj = SchoolYear::findOne($prevAcadYearId);
                    $semObj = Semester::findOne($prevSemId);
                    $readMit = $wrtObject->readmitScheduleCheck($semObj, $yearObj, $WRTkey);
                    if (is_bool($readMit)) {
                        $option = "<input type='checkbox' onchange='addToGroup({$WRTkey->swh_std_relId})' />";
                    } else {
                        $option = "Not Time for readmission";
                    }

                    if ($WRTkey) {
                        $std = $WRTkey->swhStdRel;
                        $gggg = CCF::currentStudentGroup($std,1);
                        if ($gggg) {
                            continue;
                        }
                        $spec  = $std->stdSpecRel ? $std->stdSpecRel->spec_name : '-';
                        $option = "<input type='checkbox' onchange='addToGroup({$std->id})' />";
                        echo "<tr>
                                <td>{$option}</td>
                                <td>{$std->std_regId}</td>
                                <td>{$std->getFullName()}</td>
                                <td>WD</td>
                                <td>{$spec}</td>
                            </tr>";
                    }
                }
                echo "</tbody></table>";
            } else {
                die("No Readmitted Students fouund yet.");
            }
        } else if ($entryType == 3) { //prev_DEPT_CHANGED
            // $prevSemId;  
            // $prevAcadYearId   
            // $currentClassLevel
            $SWHObj = new \backend\models\WStudentWithdrawalHistory();
            //  = isset($_POST['prevAcadYearId']) && !empty($_POST['prevAcadYearId']) ? intval($_POST['prevAcadYearId']) : die('Prev Acad. Year Not selected.');
            // $prevSemId = isset($_POST['prevSemId']) && !empty($_POST['prevSemId']) ? intval($_POST['prevSemId']) : die('Prev semester Year Not selected.');

            $gset = NULL;

            $admObj = \backend\models\Admission::findOne($admId);
            $clslvlObj = \backend\models\ClassLevel::findOne($clslvlId);
            $thisSem = Semester::findOne($this->semId);

            if ($thisSem) {
                $pSem = $thisSem->semester_name == 'Summer' ? 'II' : ($thisSem->semester_name == 'II' ? 'I' : ($thisSem->semester_name == 'I' ? ($admObj->admission_name == 'Extension' ? 'Summer' : 'II') : 'Uknown'));

                $prevSemObj = Semester::findOne(['semester_name' => $pSem]);
            } else {
                return "Academic Calendar/Semester is NOT set yet, Please set it first.";
            }

            if ($thisSem->semester_name == 'I' && $clslvlObj->clslvl_num == 1) {
                return "Invalid Selection, Prev record NOT available.";
            }

            $prevClslvl = $thisSem->semester_name == 'I' ? $clslvlObj->clslvl_num - 1 : $clslvlObj->clslvl_num;
            // $prevAcadYearId = $thisSem->semester_name == 'I'?$this->getPrevYear():$this->acadYearId;

            // var_dump($prevClslvl,$prevAcadYearId);die();
            // 'clslvl_admission_relId'=>$admId,
            $pcl = \backend\models\ClassLevel::findOne([
                'clslvl_program_relId' => $progId,
                'clslvl_num' => $prevClslvl,
            ]);

            if ($pcl) {
                $prevClslvl = $pcl;
            } else {
                die('Invalid Selection');
            }

            $prevClslvlObj = $prevClslvl;

            // echo "<pre>";
            // var_dump($prevAcadYearId,$prevSemObj->semester_name,$prevClslvlObj->clslvl_name);
            // die();

            $groups = NULL;
            if (isset($_POST['prevGroup']) && !empty($_POST['prevGroup'])) {
                $groups = WGroupSetup::find()->where(['id' => intval($_POST['prevGroup'])])->all();
            } else {
                // 'gs_acadYear_relId'=>$prevAcadYearId,
                $groups = WGroupSetup::find()->innerJoin('class_level', 'class_level.id=w_group_setup.gs_clslvl_relId')->where([
                    'gs_sem_relId' => $prevSemObj->id,
                    'clslvl_num' => $prevClslvlObj->clslvl_num,
                ]);
                // if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
                //     $groups->andWhere(['gs_dept_relId' => $_POST['deptId']]);
                // }
                if (isset($_POST['specId']) && !empty($_POST['specId'])) {
                    $groups->andWhere(['OR', ['gs_spec_relId' => $_POST['specId']], ['gs_spec_relId' => NULL]]);
                }
                if (isset($_POST['progId']) && !empty($_POST['progId'])) {
                    $groups->andWhere(['gs_prog_relId' => $_POST['progId']]);
                }
                if (isset($_POST['admId']) && !empty($_POST['admId'])) {
                    $groups->andWhere(['gs_adm_relId' => $_POST['admId']]);
                }
                $groups = $groups->all();
            }

            $table = "";
            $record = FALSE;
            if ($groups) {
                foreach ($groups as $key) {
                    // ->andWhere(['std_dept_relId' => intval($_POST['deptId'])])
                    // ->andWhere(['std_dept_relId' => intval($_POST['deptId'])])
                    // ->innerJoin('student','w_group_assignment.gas_std_relId=student.id')
                    $students = WGroupAssignment::find()
                        ->where(['gas_gset_relId' => $key->id])
                        ->all();

                    if ($students) {

                        $table .= "<table class='table table-striped table-condensed table-sm table-bordered'>";
                        $table .= "<caption><p class='bg-success'>Students (<b>{$key->gsGroupRel->group_name} - {$key->gsDeptRel->dept_code}</b>)[{$key->getAcadYearHeader()}].</p></caption>";
                        $table .= "<thead style='background-color:#8D0;'>
                        <th>#</th>
                        <th>Reg. ID.</th>
                        <th>Student Name</th>
                        <th>Prev. Grp.</th>
                        <th>Spec.</th>
                        <th>Select</th>
                        </thead><tbody>";

                        $spec = isset($key->gs_spec_relId) ? $key->gsSpecRel->spec_name : '-';

                        $title2 = $key->gsAdmRel->admission_name;
                        $title2 .= '_' . $key->gsProgRel->program_name;
                        $title2 .= '_' . $key->gsDeptRel->dept_code;
                        $title2 .= '_' . $key->gsClslvlRel->clslvl_num . 'Yr.';
                        $title2 .= '_' . $spec;

                        $prevGroupName = $key->gsGroupRel->group_name;
                        $cc = 0;

                        foreach ($students as $key2) {
                            $std =  $key2->gasStdRel;
                            $user = $std->stdUserRel;

                            $semreg = CCF::checkSemesterPassStatus($key2->gasGsetRel, $std);

                            if (is_bool($semreg)) {
                                if ($semreg == true) {
                                    $option = "<input type='checkbox' onchange='addToGroup({$std->id})' />";
                                } else {
                                    $option = "<span style='color:red' title='{$semreg}'> ?? </span>";
                                }
                            } else if (is_int($semreg)) {
                                $option = "<span style='color:yellow'>Further Check required</span>";
                            } else if ($std->std_semester_status == "WITHDRAWN") {
                                $option = "<span style='color:yellow'>WITHDRAWN</span>";
                            } else if (is_string($semreg)) {
                                $option = "<span title='{$semreg}' style='color:red; font-style:italic; cursor:pointer; font-size:8pt'> ?? </span> ";
                            }

                            // check if assigned to this class level and this semester on somewhen
                            $gggg = CCF::getStudentGroupHistoryPublic($clslvlId, $thisSem->id, $std->id);
                            // check if completed prev. semester enroll.
                            if ($gggg) {
                                continue;
                                // $option = "<span title='Assigned to {$gggg->gsGroupRel->group_name}_[{$gggg->gsClslvlRel->clslvl_num},{$gggg->gsSemRel->semester_name}_{$gggg->gsSemRel->semester_name}]' style='color:red; font-style:italic; cursor:pointer; font-size:8pt'> !! </span> ";
                            } else {
                                $gggg = CCF::currentStudentGroup($std,1);
                                if ($gggg) {
                                    // $option = "<span title='Assigned to {$gggg->gsGroupRel->group_name}' style='color:red; font-style:italic; cursor:pointer; font-size:8pt'> !! </span> ";
                                    continue;
                                }
                            }

                            // check if WITHDRAWN
                            $wdr = $SWHObj->hasValidWithdrawal($std);
                            if ($wdr) {
                                $option = "<span title='WITHDRAWN' style='color:red; font-style:italic; cursor:pointer; font-size:8pt'> !! </span> ";
                            }

                            $gggg2 = CCF::currentStudentGroup($std, 1);
                            $table .= "<tr>
                                <td>" . ++$cc . "</td>
                                <td>" . $std->std_regId . "</td>
                                <td>{$std->getFullName()}</td>
                                <td title = '{$title2}' style='text-align:center;'>{$prevGroupName}</td>
                                <td>" . $spec . "</td>
                            ";
                            if ($gggg2) {
                                $gset = $gggg2;
                                $this->setStudentGroup($gset, $std);
                                $gname2 = $gset->gsGroupRel->group_name;

                                $title = $gset->gsAdmRel->admission_name;
                                $title .= '_' . $gset->gsProgRel->program_name;
                                $title .= '_' . $gset->gsDeptRel->dept_code;
                                $title .= '_' . $gset->gsClslvlRel->clslvl_num . 'Yr.';
                                $title .= '_' . $gset->gsSemRel->semester_name;
                                // $title .= '_'.isset($groupId->gs_spec_relId)?\backend\models\Specialization::findOne(['id'=>$groupId->gs_spec_relId])->spec_name:'';


                                $table .= "
                                <td title='Assigned to {$title} in this semester.' style='text-align:center;'>{$gname2}</td>";
                            } else {
                                $record = TRUE;
                                $table .= "
                                <td title='Not assigned to any group in this semester.' style='text-align:center;'>{$option}</td>";
                            }
                            $table .= "</tr>";
                        }
                        $table .= "</tbody></table>";

                        // if ($record) {
                        //     echo "{$table}";
                        // }else{
                        //     echo "No recod";
                        // }

                    } else {
                        return "No record!";
                    }
                }
                return $table;
            } else {

                // check if is GC group setup::
                //  [5,I or II]
                // check GC case, for those who haven't completed course
                if ($this->isGCStudent($thisSem, $clslvlObj) == true) {

                    // get students attended [5,[I,II]] but DID NOT completed their course::
                    // get Students::

                    $stds = \backend\models\WGroupAssignment::find()
                        ->innerJoin('w_group_setup', 'w_group_setup.id = w_group_assignment.gas_gset_relId')
                        ->where([
                            'gs_clslvl_relId' => $clslvlObj->id,
                            'gs_sem_relId' => $thisSem->id,
                        ])
                        ->all();
                    $rtable = "";
                    if ($stds) {
                        $rtable .= "<table class='table table-striped table-condensed table-sm table-bordered'>";
                        $rtable .= "<caption><p class='bg-success'>Graduated Stidents</caption>";
                        $rtable .= "<thead style='background-color:#FD8;'>
                        <th>Select</th>
                        <th>Reg. ID.</th>
                        <th>Student Name</th>
                        <th>Prev. Grp.</th>
                        <th>Spec.</th>
                        </thead><tbody>";
                        foreach ($stds as $GCStudkey) {
                            // check if remaining course exists
                            $std = $GCStudkey->gasStdRel;
                            $semregDetailObj = new SemesterRegistration2Detail();
                            $eligible = $semregDetailObj->isEligible($std, $GCStudkey->gasGsetRel);

                            $spec = $GCStudkey->gasGsetRel->gsSpecRel;
                            $spec = is_null($spec) ? '-' : $spec->spec_name;
                            if (is_array($eligible)) {
                                if (isset($eligible[1]) && $eligible[1] == 'YES') {
                                    continue;
                                }
                                $option = "<input type='checkbox' title='Graduated on [{$GCStudkey->gasGsetRel->gsAcadYearRel->sch_year_desc}], But NOT completed course' onchange='addToGroup({$std->id})' />";
                                $gggg = CCF::currentStudentGroup($std,1);
                                if ($gggg) {
                                    // $option = "<span title='Assigned to {$gggg->gsGroupRel->group_name}' style='color:red; font-style:italic; cursor:pointer; font-size:8pt'> !! </span> ";
                                    continue;
                                }
                                // "<pre>{$std->stdUserRel->first_name} {$std->stdUserRel->middle_name} - Graduated on [{$GCStudkey->gasGsetRel->gsAcadYearRel->sch_year_desc}-{$GCStudkey->gasGsetRel->gsSemRel->semester_name}] - Eligible = {$eligible[1]}</pre>";
                                $rtable .= "<tr>
                                            <td>{$option}</td>
                                            <td>{$std->std_regId}</td>
                                            <td>{$std->getFullName()}</td>
                                            <td>-</td>
                                            <td>{$spec}</td>
                                        </tr>";
                                $record = TRUE;
                                // 
                            } else {
                                continue;
                            }
                        }
                        if ($record) {
                            echo "{$rtable}";
                        } else {
                            echo "No record";
                        }
                    }
                } else {
                    echo "<br><p class='bg-danger'>No record found.</p>";
                }
            }
        } else { //prev_NORMAL
            // $prevSemId;  
            // $prevAcadYearId   
            // $currentClassLevel
            $SWHObj = new \backend\models\WStudentWithdrawalHistory();
            //  = isset($_POST['prevAcadYearId']) && !empty($_POST['prevAcadYearId']) ? intval($_POST['prevAcadYearId']) : die('Prev Acad. Year Not selected.');
            // $prevSemId = isset($_POST['prevSemId']) && !empty($_POST['prevSemId']) ? intval($_POST['prevSemId']) : die('Prev semester Year Not selected.');

            $gset = NULL;


            $admObj = \backend\models\Admission::findOne($admId);
            $clslvlObj = \backend\models\ClassLevel::findOne($clslvlId);
            $thisSem = Semester::findOne($this->semId);

            if ($thisSem) {
                $pSem = $thisSem->semester_name == 'Summer' ? 'II' : ($thisSem->semester_name == 'II' ? 'I' : ($thisSem->semester_name == 'I' ? ($admObj->admission_name == 'Extension' ? 'Summer' : 'II') : 'Uknown'));

                $prevSemObj = Semester::findOne(['semester_name' => $pSem]);
            } else {
                return "Academic Calendar/Semester is NOT setted yet, Please set it first.";
            }

            if ($thisSem->semester_name == 'I' && $clslvlObj->clslvl_num == 1) {
                return "Invalid Selection, Prev record NOT available.";
            }

            $prevClslvl = $thisSem->semester_name == 'I' ? $clslvlObj->clslvl_num - 1 : $clslvlObj->clslvl_num;
            $prevAcadYearId = $thisSem->semester_name == 'I' ? $this->getPrevYear() : $this->acadYearId;
            // 'clslvl_admission_relId'=>$admId,
            $pcl = \backend\models\ClassLevel::findOne([
                'clslvl_program_relId' => $progId,
                'clslvl_num' => $prevClslvl,
            ]);

            if ($pcl) {
                $prevClslvl = $pcl;
            } else {
                die('Invalid Selection');
            }

            $prevClslvlObj = $prevClslvl;

            // echo "<pre>";
            // var_dump($prevAcadYearId,$prevSemObj->semester_name,$prevClslvlObj->clslvl_num);
            // die();

            $groups = NULL;
            if (isset($_POST['prevGroup']) && !empty($_POST['prevGroup'])) {
                $groups = WGroupSetup::find()->where(['id' => intval($_POST['prevGroup'])])->all();
            } else {
                // 'gs_acadYear_relId'=>$prevAcadYearId,
                $groups = WGroupSetup::find()->where([
                    'gs_sem_relId' => $prevSemObj->id,
                    'gs_clslvl_relId' => $prevClslvlObj->id,
                ]);
                if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
                    $groups->andWhere(['gs_dept_relId' => $_POST['deptId']]);
                }
                if (isset($_POST['specId']) && !empty($_POST['specId'])) {
                    $groups->andWhere(['OR', ['gs_spec_relId' => $_POST['specId']], ['gs_spec_relId' => NULL]]);
                }
                if (isset($_POST['progId']) && !empty($_POST['progId'])) {
                    $groups->andWhere(['gs_prog_relId' => $_POST['progId']]);
                }
                if (isset($_POST['admId']) && !empty($_POST['admId'])) {
                    $groups->andWhere(['gs_adm_relId' => $_POST['admId']]);
                }

                $groups = $groups->all();
            }


            $table = "";
            $record = FALSE;
            if ($groups) {
                foreach ($groups as $key) {
                    $students = \backend\models\WGroupAssignment::find()
                        ->where(['gas_gset_relId' => $key->id])
                        ->all();
                    if ($students) {

                        $table .= "<table class='table table-striped table-condensed table-sm table-bordered'>";
                        $table .= "<caption><p class='bg-success'>Students (<b>{$key->gsGroupRel->group_name} - {$key->gsDeptRel->dept_code}</b>).</p></caption>";
                        $table .= "<thead style='background-color:#8D0;'>
                        <th>#</th>
                        <th>Reg. ID.</th>
                        <th>Student Name</th>
                        <th>Prev. Grp.</th>
                        <th>Spec.</th>
                        <th>Select</th>
                        </thead><tbody>";

                        $spec = isset($key->gs_spec_relId) ? $key->gsSpecRel->spec_name : '-';

                        $title2 = $key->gsAdmRel->admission_name;
                        $title2 .= '_' . $key->gsProgRel->program_name;
                        $title2 .= '_' . $key->gsDeptRel->dept_code;
                        $title2 .= '_' . $key->gsClslvlRel->clslvl_num . 'Yr.';
                        $title2 .= '_' . $spec;

                        $prevGroupName = $key->gsGroupRel->group_name;
                        $cc = 0;

                        foreach ($students as $key2) {
                            $std =  $key2->gasStdRel;
                            $user = $std->stdUserRel;

                            // $immidiatePrevGset = $this->getImmidiatePrevGset($clslvlObj,$std,$prevSemObj);

                            // echo "<pre>";
                            // var_dump([$prevClslvlObj->clslvl_num,$prevSemObj->semester_name,$key->gsAcadYearRel->sch_year_desc]);
                            // die();
                            // continue;


                            $semreg = CCF::checkSemesterPassStatus($key, $std);

                            if (is_bool($semreg)) {
                                if ($semreg == true) {
                                    $option = "<input type='checkbox' onchange='addToGroup({$std->id})' />";
                                } else {
                                    $option = "<span style='color:red' title='{$semreg}'> ?? </span>";
                                }
                            } else if (is_int($semreg)) {
                                $option = "<span style='color:yellow'>Further Check required</span>";
                            } else if ($std->std_semester_status == "WITHDRAWN") {
                                $option = "<span style='color:yellow'>WITHDRAWN</span>";
                            } else if (is_string($semreg)) {
                                $option = "<span title='{$semreg}' style='color:red; font-style:italic; cursor:pointer; font-size:8pt'> ?? </span> ";
                            }

                            // check if assigned to this class level and this semester on somewhen
                            $gggg = CCF::getStudentGroupHistoryPublic($clslvlId, $thisSem->id, $std->id);
                            // check if completed prev. semester enroll.
                            if ($gggg) {
                                continue;
                                // $option = "<span title='Assigned to {$gggg->gsGroupRel->group_name}_[{$gggg->gsClslvlRel->clslvl_num},{$gggg->gsSemRel->semester_name}_{$gggg->gsSemRel->semester_name}]' style='color:red; font-style:italic; cursor:pointer; font-size:8pt'> !! </span> ";
                            } else {
                                $gggg = CCF::currentStudentGroup($std,1);
                                if ($gggg) {
                                    $option = "<span title='Assigned to {$gggg->gsGroupRel->group_name}' style='color:red; font-style:italic; cursor:pointer; font-size:8pt'> !! </span> ";
                                    // continue;
                                }
                            }

                            // check if WITHDRAWN
                            $wdr = $SWHObj->hasValidWithdrawal($std);
                            if ($wdr) {
                                $option = "<span title='WITHDRAWN' style='color:red; font-style:italic; cursor:pointer; font-size:8pt'> !! </span> ";
                            }

                            $gggg2 = CCF::currentStudentGroup($std,1);
                            $table .= "<tr>
                                <td>" . ++$cc . "</td>
                                <td>" . $std->std_regId . "</td>
                                <td>{$std->getFullName()}</td>
                                <td title = '{$title2}' style='text-align:center;'>{$prevGroupName}</td>
                                <td>" . $spec . "</td>
                            ";
                            if ($gggg2) {
                                $gset = $gggg2;
                                $this->setStudentGroup($gset, $std);
                                $gname2 = $gset->gsGroupRel->group_name;

                                $title = $gset->gsAdmRel->admission_name;
                                $title .= '_' . $gset->gsProgRel->program_name;
                                $title .= '_' . $gset->gsDeptRel->dept_code;
                                $title .= '_' . $gset->gsClslvlRel->clslvl_num . 'Yr.';
                                $title .= '_' . $gset->gsSemRel->semester_name;
                                // $title .= '_'.isset($groupId->gs_spec_relId)?\backend\models\Specialization::findOne(['id'=>$groupId->gs_spec_relId])->spec_name:'';


                                $table .= "
                                <td title='Assigned to {$title} in this semester.' style='text-align:center;'>{$gname2}</td>";
                            } else {
                                $record = TRUE;
                                $table .= "
                                <td title='Not assigned to any group in this semester.' style='text-align:center;'>{$option}</td>";
                            }
                            $table .= "</tr>";
                        }
                        $table .= "</tbody></table>";

                        // if ($record) {
                        //     echo "{$table}";
                        // }else{
                        //     echo "No recod";
                        // }

                    }
                }
                return $table;
            } else {

                // check if is GC group setup::
                //  [5,I or II]
                // check GC case, for those who haven't completed course
                if ($this->isGCStudent($thisSem, $clslvlObj) == true) {

                    // get students attended [5,[I,II]] but DID NOT completed their course::
                    // get Students::

                    $stds = \backend\models\WGroupAssignment::find()
                        ->innerJoin('w_group_setup', 'w_group_setup.id = w_group_assignment.gas_gset_relId')
                        ->where([
                            'gs_clslvl_relId' => $clslvlObj->id,
                            'gs_sem_relId' => $thisSem->id,
                        ])
                        ->all();
                    $rtable = "";
                    if ($stds) {
                        $rtable .= "<table class='table table-striped table-condensed table-sm table-bordered'>";
                        $rtable .= "<caption><p class='bg-success'>Graduated Stidents</caption>";
                        $rtable .= "<thead style='background-color:#FD8;'>
                        <th>Select</th>
                        <th>Reg. ID.</th>
                        <th>Student Name</th>
                        <th>Prev. Grp.</th>
                        <th>Spec.</th>
                        </thead><tbody>";
                        foreach ($stds as $GCStudkey) {
                            // check if remaining course exists
                            $std = $GCStudkey->gasStdRel;
                            $semregDetailObj = new SemesterRegistration2Detail();
                            $eligible = $semregDetailObj->isEligible($std, $GCStudkey->gasGsetRel);

                            $spec = $GCStudkey->gasGsetRel->gsSpecRel;
                            $spec = is_null($spec) ? '-' : $spec->spec_name;
                            if (is_array($eligible)) {
                                if (isset($eligible[1]) && $eligible[1] == 'YES') {
                                    continue;
                                }
                                $option = "<input type='checkbox' title='Graduated on [{$GCStudkey->gasGsetRel->gsAcadYearRel->sch_year_desc}], But NOT completed course' onchange='addToGroup({$std->id})' />";
                                $gggg = CCF::currentStudentGroup($std,1);
                                if ($gggg) {
                                    // $option = "<span title='Assigned to {$gggg->gsGroupRel->group_name}' style='color:red; font-style:italic; cursor:pointer; font-size:8pt'> !! </span> ";
                                    continue;
                                }
                                // "<pre>{$std->stdUserRel->first_name} {$std->stdUserRel->middle_name} - Graduated on [{$GCStudkey->gasGsetRel->gsAcadYearRel->sch_year_desc}-{$GCStudkey->gasGsetRel->gsSemRel->semester_name}] - Eligible = {$eligible[1]}</pre>";
                                $rtable .= "<tr>
                                            <td>{$option}</td>
                                            <td>{$std->std_regId}</td>
                                            <td>{$std->getFullName()}</td>
                                            <td>-</td>
                                            <td>{$spec}</td>
                                        </tr>";
                                $record = TRUE;
                                // 
                            } else {
                                continue;
                            }
                        }
                        if ($record) {
                            echo "{$rtable}";
                        } else {
                            echo "No record";
                        }
                    }
                } else {
                    echo "<br><p class='bg-danger'>No record found.</p>";
                }
            }
            // echo "1";
        }
    }

    private function isGCStudent($thisSem, $clslvlObj)
    {
        if ($clslvlObj->clslvl_num == 5) { //UG GC students check
            return true;
        }
    }

    private function getPrevYear()
    {
        $acadYear = SchoolYear::findOne($this->acadYearId);
        if ($acadYear) {
            $em = explode('/', $acadYear->sch_year_desc);

            $prevYear = (string)($em[0] - 1) . '/' . ($em[1] - 1);

            $prevAcadYear = \backend\models\SchoolYear::findOne(['sch_year_desc' => $prevYear]);

            if ($prevAcadYear) {
                return $prevAcadYear->id;
            }
            die("Previous Year Uknown");
        } else {
            die("Academic Year Schedule Uknown.");
        }
    }

    private function setStudentGroup($groupObj, $stdObj)
    {
        $result = Yii::$app->db->createCommand()
            ->update('student', [
                'std_clslvl_relId' => $groupObj->gs_clslvl_relId,
                'std_spec_relId' => $groupObj->gs_spec_relId,
                'std_group_realId' => $groupObj->id,
            ], [
                'id' => $stdObj->id,
            ])
            ->execute();
    }
    private function IfRecommended($stdId, $recType)
    {

        $this->__constructor();
        $recs = NULL;
        if ($recType == 0) { //if ant specialization recommendation is made before for this student.
            $recs = SecSpecRecomend::find()->where([
                'ssr_acadYear_relId' => $this->acadYearId,
                'ssr_sem_relId' => $this->semId,
                'ssr_created_by' => Yii::$app->user->identity->id
            ])
                ->andWhere([
                    'IS NOT',
                    'ssr_rec_spec',
                    NULL
                ])
                ->all();
        } else if ($recType == 1) {
            $recs = SecSpecRecomend::find()->where([
                'ssr_acadYear_relId' => $this->acadYearId,
                'ssr_sem_relId' => $this->semId,
                'ssr_created_by' => Yii::$app->user->identity->id
            ])->andWhere(['IS NOT', 'ssr_rec_section', NULL])
                ->all();
        }

        if ($recs) {
            $recStdsList = [];
            foreach ($recs as $key) {
                foreach (json_decode($key->ssr_stdList) as $keyyy) {
                    $recStdsList[] = $keyyy;
                }
            }
            // echo "<pre>";
            // var_dump($recStdsList);die();
            if (in_array($stdId, $recStdsList)) {
                return TRUE;
            }
            return FALSE;
        } else {
            return FALSE;
        }
    }

    // returns the list of students / groups WRT user selection  get-group-list
    public function actionGetGroupList22()
    {
        // http://rmsfront.local/w-group-setup/get-group-list22
        $this->__constructor();
        // returns the list of students if it is for the first time.
        $deptId = Yii::$app->request->post('deptId');
        $progId = Yii::$app->request->post('progId');
        $clslvl = Yii::$app->request->post('clslvlId');
        $admId = Yii::$app->request->post('admId');
        $specId = Yii::$app->request->post('specId');

        $specId = $specId == 0 ? NULL : $specId;

        $entryType = Yii::$app->request->post('entryId');
        $recType = Yii::$app->request->post('recType');
        $recType == '0' ? 0 : intval($recType);
        // 0=>specialization
        // 1=>section / group
        $prevSemId = Yii::$app->request->post('prevSemId');
        $prevAcadYearId = Yii::$app->request->post('prevAcadYearId');


        if ($recType == 0) { //
            $stds = Student::find()
                ->where([
                    'std_admission_relId' => $admId,
                    'std_prog_relId' => $progId,
                    'std_dept_relId' => $deptId,
                    'std_clslvl_relId' => $clslvl,
                    'std_spec_relId' => NULL,
                ]);
            // if (isset($specId) && !is_null($specId)) {
            //     $stds = $stds->andFilterWhere(['std_spec_relId'=>$specId]);
            // }
            $stds = $stds->all();

            if (!$stds) {
                echo "<p class='bg-danger'>No List </p>";

                // foreach($stds as $std){ .... };
            } else {
                // $setUp = WGroupSetup::findOne([
                //     'gs_acadYear_relId'=>$this->acadYearId,
                //     'gs_sem_relId'=> $this->semId,
                //     'gs_dept_relId'=>$deptId,
                //     'gs_adm_relId'=>$admId,
                //     'gs_prog_relId'=>$progId,
                //     'gs_clslvl_relId'=>$clslvl,
                // ]);

                echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
                echo "<caption><p class='bg-success'>Students Not assigned specialization yet</p></caption>";
                echo "<thead>
            <th>Select</th>
            <th>Reg. ID.</th>
            <th>Student Name</th>
            <th>Year Of Adm.</th>
            <th>Spec.</th>
            </thead><tbody>";
                foreach ($stds as $std) {
                    if ($this->IfRecommended($std->id, $recType)) {
                        continue;
                    }
                    $usr = User::findOne(['id' => $std['std_user_relId']]);
                    $spec = $std['std_spec_relId'] ? \backend\models\Specialization::findOne(['id' => $std['std_spec_relId']])->spec_name : 'N/A';
                    $grp = 'N/A';
                    if (!is_null($std['std_group_realId'])) {
                        $gset = \backend\models\WGroupSetup::findOne(['id' => $std['std_group_realId']]);

                        if ($gset) {
                            $grp = \backend\models\WGroup::findOne(['id' => $gset->gs_group_relId])->group_name;
                        }
                    }
                    echo "<tr>
                <td><input type='checkbox' onchange='addToGroup(" . $std['id'] . ")' /></td>
                <td>" . $std['std_regId'] . "</td>
                <td>{$std->getFullName()}</td>
                <td>" . \backend\models\SchoolYear::findOne(['id' => $std['std_acadYear_relId']])->sch_year_desc . "</td>
                <td>" . $spec . "</td>
                </tr>";
                }
                echo "</tbody></table>";
            }
        } else {
            // $prevSemId;  
            // $prevAcadYearId     
            // 
            $prevAcadYearId = isset($_POST['prevAcadYearId']) && !empty($_POST['prevAcadYearId']) ? intval($_POST['prevAcadYearId']) : die('Prev Acad. Year Not selected.');
            $prevSemId = isset($_POST['prevSemId']) && !empty($_POST['prevSemId']) ? intval($_POST['prevSemId']) : die('Prev semester Year Not selected.');

            $gset = NULL;


            if (isset($_POST['prevGroup']) && !empty($_POST['prevGroup'])) {
                $gset = \backend\models\WGroupSetup::find()
                    ->where([
                        'id' => intval($_POST['prevGroup']),
                        'gs_acadYear_relId' => intval($prevAcadYearId),
                        'gs_sem_relId' => intval($prevSemId)
                    ])
                    ->all();
            } else {
                $gset = \backend\models\WGroupSetup::find()
                    ->where([
                        'gs_acadYear_relId' => intval($prevAcadYearId),
                        'gs_sem_relId' => intval($prevSemId),
                        'gs_adm_relId' => $admId,
                        'gs_prog_relId' => $progId,
                        'gs_dept_relId' => $deptId,
                        'gs_clslvl_relId' => $clslvl,
                    ])
                    ->all();
            }

            if ($gset) {
                foreach ($gset as $key) {
                    $students = \backend\models\WGroupAssignment::find()
                        ->where(['gas_gset_relId' => $key->id])->all();
                    if ($students) {

                        $setUp = WGroupSetup::find()->where([
                            'gs_acadYear_relId' => $this->acadYearId,
                            'gs_sem_relId' => $this->semId,
                            'gs_dept_relId' => $deptId,
                            'gs_adm_relId' => $admId,
                            'gs_prog_relId' => $progId,
                            'gs_clslvl_relId' => $clslvl,
                        ])->all();

                        $setUpArray = [];
                        if ($setUp) {
                            foreach ($setUp as $key3) {
                                $setUpArray[] = $key3->id;
                            }
                        }
                        $prevGroupName = \backend\models\WGroup::findOne(['id' => $key->gs_group_relId])->group_name;
                        echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
                        echo "<caption><p class='bg-info'>Students in {$prevGroupName}</p></caption>";
                        echo "<thead style='background-color:#8D0;'>
                        <th>Select</th>
                        <th>Reg. ID.</th>
                        <th>Student Name</th>
                        <th>Prev. Grp.</th>
                        <th>Spec.</th>
                        </thead><tbody>";

                        $spec = isset($key->gs_spec_relId) ? \backend\models\Specialization::findOne(['id' => $key->gs_spec_relId])->spec_name : '-';

                        $title2 = \backend\models\Admission::findOne(['id' => $key->gs_adm_relId])->admission_name;
                        $title2 .= '_' . \backend\models\Program::findOne(['id' => $key->gs_prog_relId])->program_name;
                        $title2 .= '_' . \backend\models\Department::findOne(['id' => $key->gs_dept_relId])->dept_name;
                        $title2 .= '_' . \backend\models\ClassLevel::findOne(['id' => $key->gs_clslvl_relId])->clslvl_num . 'Yr.';
                        $title2 .= '_' . $spec;

                        foreach ($students as $key2) {
                            if ($this->IfRecommended($key2->gas_std_relId, $recType)) {
                                continue;
                            }
                            $std = \backend\models\Student::findOne(['id' => $key2->gas_std_relId]);
                            $user = \backend\models\User::findOne(['id' => $std->std_user_relId]);

                            $exists = \backend\models\WGroupAssignment::find()->where(['gas_gset_relId' => $setUpArray, 'gas_std_relId' => $std->id])->one();

                            echo "<tr>";
                            if ($exists) {
                                $groupId = \backend\models\WGroupSetup::findOne(['id' => $exists->gas_gset_relId]);
                                $gname2 = \backend\models\WGroup::findOne(['id' => $groupId->gs_group_relId])->group_name;

                                $title = \backend\models\Admission::findOne(['id' => $groupId->gs_adm_relId])->admission_name;
                                $title .= '_' . \backend\models\Program::findOne(['id' => $groupId->gs_prog_relId])->program_name;
                                $title .= '_' . \backend\models\Department::findOne(['id' => $groupId->gs_dept_relId])->dept_name;
                                $title .= '_' . \backend\models\ClassLevel::findOne(['id' => $groupId->gs_clslvl_relId])->clslvl_num . 'Yr.';
                                // $title .= '_'.isset($groupId->gs_spec_relId)?\backend\models\Specialization::findOne(['id'=>$groupId->gs_spec_relId])->spec_name:'';


                                echo "
                                <td title='This Student is already assigned to {$title} in this semester.' style='text-align:center;'>{$gname2}</td>";
                            } else {
                                echo "
                                <td title='Not assigned to any group in this semester.' style='text-align:center;'><input type='checkbox' onchange='addToGroup(" . $std['id'] . ")' /></td>";
                            }
                            echo "
                                <td>" . $std->std_regId . "</td>
                                <td>{$std->getFullName()}</td>
                                <td title = '{$title2}' style='text-align:center;'>{$prevGroupName}</td>
                                <td>" . $spec . "</td>
                                </tr>";
                        }
                    } else {
                        echo "<p class='bg-danger'>No student assignment to this group.</p>";
                    }
                }
            } else {
                echo "<p class='bg-danger'>Such group setup not made yet. Please re-try.</p>";
            }
            // echo "1";
        }
        die();
        //echo "Entry Type selected: ".$entryType;die();
    }

    public function actionPrintStudentList()
    {
        $params = isset($_GET['params']) && !empty($_GET['params']) ? (string)$_GET['params'] : die('Uknown Seelction Parameter, Please try again...');

        $params = json_decode($params);

        if (!is_object($params)) {
            $message = 'Invalid Parameters!';
            // die();
        }

        $clslvlId = isset($params->clslvlId) && !empty($params->clslvlId) ? intval($params->clslvlId) : die('Uknown Class Level. Please try again...');
        $deptId = isset($params->deptId) && !empty($params->deptId) ? intval($params->deptId) : die('Uknown Department. Please try again...');
        $groupId = isset($params->groupId) && !empty($params->groupId) ? intval($params->groupId) : die('Uknown Department. Please try again...');
        $gsetId = isset($params->gsetId) && !empty($params->gsetId) ? intval($params->gsetId) : die('Uknown Group Setup entry. Please try again...');
        $requiredList = isset($params->requiredList) && !empty($params->requiredList) ? intval($params->requiredList) : NULL;


        $this->__constructor();
        $gset = \backend\models\WGroupSetup::findOne($gsetId);
        $gass = WGroupAssignment::find()
            ->innerJoin('w_group_setup', 'w_group_setup.id = w_group_assignment.gas_gset_relId')
            ->where([
                'gs_acadYear_relId' => $this->acadYearId,
                'gs_sem_relId' => $this->semId,
                'gs_dept_relId' => $deptId,
                'gs_clslvl_relId' => $clslvlId,
                'gs_group_relId' => $groupId,
                'gas_gset_relId' => $gsetId
            ])
            ->all();
        $stds2 = [];

        if ($gass) {
            foreach ($gass as $GASkey) {
                $ss = $this->enrolledCourse($GASkey->gas_gset_relId, $GASkey->gas_std_relId);

                if (!is_null($requiredList)) {
                    if ($requiredList == 1 && !$ss) {
                        continue;
                    }
                    if ($requiredList == 404 && $ss) {
                        continue;
                    }
                }

                $stds2[] = $GASkey->gas_std_relId;
            }
        } else {
            $message = "Empty Student List";
        }
        if (is_array($stds2) && count($stds2) > 0) {
            $students = Student::find()
                ->innerJoin('user', 'user.id = student.std_user_relId')
                ->where(['student.id' => $stds2])
                ->orderBy(['user.first_name' => SORT_ASC, 'user.middle_name' => SORT_ASC])
                ->all();

            return $this->renderPartial('student-group-list', [
                'gsModel' => $gset,
                'stdModel' => $students,
            ]);
        } else {
            $message = "<p class='bg-danger'>No record found</p>";
            return $this->renderPartial('student-group-list', [
                'message' => $message,
            ]);
        }
    }

    private function enrolledCourse($gsetId, $std)
    {
        $gset = $this->findModel($gsetId);
        return $gset->getStudentSemesterRegistrations2($std)->count() > 0;

        // return \frontend\models\SemesterRegistration2Detail::find()
        //     ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
        //     ->where([
        //         'srd2_group_relId' => $gsetId,
        //     ])
        //     ->andWhere(['semreg_std_relId' => $std])
        //     ->count() > 0;
    }


    public function actionGetStudents()
    {
        $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : die('Uknown Group setup enrty. Please try again...');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Uknown Class Level. Please try again...');
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId']) ? intval($_POST['deptId']) : die('Uknown Department. Please try again...');
        $groupId = isset($_POST['groupId']) && !empty($_POST['groupId']) ? intval($_POST['groupId']) : die('Uknown Department. Please try again...');

        $requiredList = isset($_POST['requiredList']) && !empty($_POST['requiredList']) ? intval($_POST['requiredList']) : NULL;

        $this->__constructor();
        $gset = \backend\models\WGroupSetup::findOne($gsetId);
        $gass = WGroupAssignment::find()
            ->innerJoin('w_group_setup', 'w_group_setup.id = w_group_assignment.gas_gset_relId')
            ->innerJoin('student', 'student.id = w_group_assignment.gas_std_relId')
            ->innerJoin('user', 'user.id = student.std_user_relId')
            ->where([
                'gs_acadYear_relId' => $this->acadYearId,
                'gs_sem_relId' => $this->semId,
                'gs_dept_relId' => $deptId,
                'gs_clslvl_relId' => $clslvlId,
                'gs_group_relId' => $groupId,
                'gas_gset_relId' => $gsetId
            ])
            ->orderBy([
                'user.first_name' => SORT_ASC, 
                'user.middle_name' => SORT_ASC,
                'user.last_name' => SORT_ASC,
            ])
            ->all();

        $wt_response = "";

        if ($gass) {
            $dept = \backend\models\Department::findOne($deptId);
            $clslvl = \backend\models\ClassLevel::findOne($clslvlId);
            // $dept = \backend\models\ClassLevel::findOne($key->gs_clslvl_relId);
            $n = $clslvl->clslvl_num;
            $adm = \backend\models\Admission::findOne($gset->gs_adm_relId)->admission_name;
            $prog = \backend\models\Program::findOne($gset->gs_prog_relId)->program_name;
            $fff = $n == 1 ? '1<sup>st</sup>' : ($n == 2 ? '2<sup>nd</sup>' : ($n == 3 ? '3<sup>rd</sup>' : $n . '<sup>th</sup>'));

            $wt_response .= "<div class='panel panel-default'><div class='panel-heading'>";
            $group = \backend\models\WGroup::findOne($groupId);

            $wt_response .= "Students in <i>{$dept->dept_name} >> {$adm} >> {$prog} >> {$fff}Yr. >> {$group->group_name}</i>";
            $wt_response .= "<i id='printIcon' style='cursor: pointer; float: right; color: blue;' title='Print' onclick='printCont({$deptId},{$clslvlId},{$gsetId},{$groupId})'>Print<span class='glyphicon glyphicon-print'></span></i>";
            $wt_response .= "</div><div class='panel-body'>";
            $wt_response .= "<table class='table-bordered table-condensed table'>";
            $wt_response .= "<thead style='background-color:#850;'>
                    <th>#</th>
                    <th>REG. ID</th>
                    <th>Full Name</th>
                    <th>Gender</th>
                    <th>Type</th>
                    <th>Specialization</th>
                    <th>Admission</th>
                    <th>Program</th>
                    <th>Sem. Regd</th>
            </thead>";
            $spec = is_null($gset->gs_spec_relId) ? '-' : \backend\models\Specialization::findOne($gset->gs_spec_relId)->spec_name;

            $count = 0;
            foreach ($gass as $key) {
                $std = Student::findOne($key->gas_std_relId);
                $user = User::findOne($std->std_user_relId);
                $ss = $this->enrolledCourse($key->gas_gset_relId, $std->id);
                $t = "";
                if ($ss == 0) {
                    // student NOT enrolled course, check if another group assigned for him in this seemster
                    // in which s/he enrolled course exists
                    $cgroup = CCF::getStudentGroupHistoryPublic($clslvlId, $this->semId, $key->gas_std_relId);
                    if ($cgroup) {
                        // this is the assignment on which the student enolled the course:
                        $t = "Enrolled course in Gset Id : " . $cgroup->id;
                        if ($cgroup->id != $key->gas_gset_relId) {
                            $key->delete();
                            continue;
                        }
                    }
                }
                $sr = $ss ? "<span class='bg-success'> Enrolled</span>" : "<span class='bg-warning'> NOT Enrolled</span>";
                $c = count($key->gasGsetRel->getStudentSemesterRegistrations2($std->id)->all());
                if (!is_null($requiredList)) {
                    if ($requiredList == 1 && !$ss) {
                        continue;
                    }
                    if ($requiredList == 404 && $ss) {
                        continue;
                    }
                }
                $wt_response .= "<tr>
                        <td>" . ++$count . "</td>
                        <td>{$std->std_regId}</td>
                        <td>{$user->getFullName()}</td>
                        <td>{$user->gender}</td>
                        <td title='{$std->std_type}'>{$user->user_type}</td>
                        <td>{$spec}</td>
                        <td>{$adm}</td>
                        <td>{$prog}</td>
                        <td>{$sr}({$c})</td>
                </tr>";
            }
            $wt_response .= "</tbody></table></div></div>";
            return $wt_response;
        } else {
            echo "<p class='bg-danger'>No record found</p>";
            die();
        }
    }

    public function actionGetGroup()
    {
        $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : die('Uknown Group setup enrty. Please try again...');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Uknown Class Level. Please try again...');
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId']) ? intval($_POST['deptId']) : die('Uknown Department. Please try again...');

        $this->__constructor();
        // ->groupBy('gs_group_relId')
        $groups = \backend\models\WGroupSetup::find()
            ->where([
                'gs_acadYear_relId' => $this->acadYearId,
                'gs_sem_relId' => $this->semId,
                'gs_dept_relId' => $deptId,
                'gs_clslvl_relId' => $clslvlId,
            ])
            ->all();

        if ($groups) {
            echo "<div class='panel panel-default'><div class='panel-heading'><div class='row' ><div class='col-md-9'> ";
            foreach ($groups as $key) {
                $group = \backend\models\WGroup::findOne($key->gs_group_relId);
                $spec = isset($key->gs_spec_relId) ? " - [{$key->gsSpecRel->spec_name}]" : '';
                // $n = $dept->clslvl_num;
                // $fff = $n == 1?'1<sup>st</sup>':($n==2?'2<sup>nd</sup>':($n == 3?'3<sup>rd</sup>':$n.'<sup>th</sup>'));
                echo "<button class='btn btn-link' onclick='loadStudents({$key->id},{$deptId},{$clslvlId},{$group->id})'>{$group->group_name}{$spec}</button>";
                //put a right aligned seelction for filtering All,Enrolled,NOT Enrolled students.
            }
            echo "</div><div class='col-md-3'>
                <select class='form-control'onchange='loadStudentsS($(this).val());' id='deptidddd'>
                    <option value=''> All </option>
                    <option value='1'> Enrolled </option>
                    <option value='404'> Not Enrolled </option>
                </select>
            </div></div>";
            echo "</div><div class='panel-body' id='StudentContainer'></div></div>";
        } else {
            echo "<p class='bg-danger'>No record found</p>";
            die();
        }
    }


    public function actionGetBatch()
    {
        $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : die('Uknown Group setup enrty. Please try again...');
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId']) ? intval($_POST['deptId']) : die('Uknown Group. Please try again...');

        $this->__constructor();

        $batchs = WGroupSetup::find()
            ->where([
                'gs_acadYear_relId' => $this->acadYearId,
                'gs_sem_relId' => $this->semId,
                'gs_dept_relId' => $deptId
            ])->groupBy('gs_clslvl_relId')
            ->orderBy(['gs_clslvl_relId' => SORT_ASC])
            ->all();

        if ($batchs) {
            echo "<div class='panel panel-default'><div class='panel-heading'>";
            foreach ($batchs as $key) {
                $program = \backend\models\Program::findOne($key->gs_prog_relId)->program_name;
                $admission = \backend\models\Admission::findOne($key->gs_adm_relId)->admission_name;
                $title = "{$program}_{$admission}";
                $dept = \backend\models\ClassLevel::findOne($key->gs_clslvl_relId);
                $n = $dept->clslvl_num;
                $fff = $n == 1 ? '1<sup>st</sup>' : ($n == 2 ? '2<sup>nd</sup>' : ($n == 3 ? '3<sup>rd</sup>' : $n . '<sup>th</sup>'));
                echo "<button class='btn btn-link' title='{$title}' onclick='loadGroups({$key->id},{$dept->id},{$deptId})'>{$fff} Year. [{$program}]</button>";
            }
            echo "</div><div class='panel-body' id='groupContainer'></div></div>";
        } else {
            echo "<p class='bg-danger'>No record found</p>";
            die();
        }
    }

    // returns list of setups made for this semester.
    public function actionGetlistReport()
    {
        $this->__constructor();

        $grpsets = WGroupSetup::find()
            ->innerJoin("department", 'department.id = w_group_setup.gs_dept_relId')
            ->where([
                'gs_acadYear_relId' => $this->acadYearId,
                'gs_sem_relId' => $this->semId,
            ])
            ->orderBy(['dept_name' => SORT_ASC])
            ->groupBy('gs_dept_relId')
            ->all();

        if (\Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);

            if ($inst) {
                $grpsets = WGroupSetup::find()
                    ->where([
                        'gs_acadYear_relId' => $this->acadYearId,
                        'gs_sem_relId' => $this->semId,
                        'gs_dept_relId' => $inst->inst_dep_relId,
                    ])
                    ->groupBy('gs_dept_relId')
                    ->all();
            } else {
                return "<p class='bg-danger'>Problem with your account.</p>";
            }
        }

        if ($grpsets) {
            echo "<div class='panel panel-default'>
                <div class='panel-heading'>";
            foreach ($grpsets as $key) {
                $dept = $key->gsDeptRel;
                // $dept = \backend\models\Department::findOne($key->gs_dept_relId);
                echo "<button class='btn btn-link' onclick='loadBatch({$key->id},{$dept->id})'>{$dept->dept_name}</button>";
            }
            echo "</div><div class='panel-body' id='batchContainer'></div></div>";
        } else {
            echo "<p class='bg-danger'>No record found</p>";
            die();
        }
    }


    // returns list of setups made for this semester.
    public function actionGetlistReport2()
    {
        $this->__constructor();
        $grpsets = WGroupSetup::find()
            ->where([
                'gs_acadYear_relId' => $this->acadYearId,
                'gs_sem_relId' => $this->semId,
            ])
            ->all();
        // 

        $divs = "";
        if (empty($grpsets)) {
            echo "<p class='bg-danger'>No Setup made for this semester.</p>";
        } else {
            $divs .= "<table class='table table-condensed table-sm table-bordered'>";
            $divs .= "<thead style='background-color:#ace;'>
            <th>#</th>
            <th>Department</th>
            <th>Program</th>
            <th>Year</th>
            <th>Group</th>
            <th>Spec.</th>
            <th>List</th>
            </thead><tbody>";
            $count = 0;

            $dgroup = WGroupSetup::find()
                ->where([
                    'gs_acadYear_relId' => $this->acadYearId,
                    'gs_sem_relId' => $this->semId
                ])
                ->all();
            if ($dgroup) {
                foreach ($dgroup as $keyd) {
                    $pgroup = WGroupSetup::find()
                        ->where([
                            'gs_acadYear_relId' => $this->acadYearId,
                            'gs_sem_relId' => $this->semId,
                            'gs_prog_relId' => $keyd->gs_prog_relId
                        ])
                        ->all();
                    if ($pgroup) {
                        $dname = \backend\models\Department::findOne($keyd->gs_dept_relId);
                        $divs .= "<tr><td style='vertical-align:middle;'  rowspan = " . count($pgroup) . ">" . ++$count . "</td>";
                        $divs .= "<td style='vertical-align:middle;' rowspan = " . count($pgroup) . ">{$dname->dept_name}</td></tr>";
                        // continue;
                        foreach ($pgroup as $keyp) { //program
                            $cgroup = WGroupSetup::find()
                                ->where([
                                    'gs_acadYear_relId' => $this->acadYearId,
                                    'gs_sem_relId' => $this->semId,
                                    'gs_dept_relId' => $keyd->gs_dept_relId,
                                    'gs_prog_relId' => $keyp->gs_prog_relId,
                                ])
                                ->all();
                            if ($cgroup) {
                                $pgname = \backend\models\Program::findOne($keyp->gs_prog_relId);
                                $divs .= "<tr><td style='vertical-align:middle;' rowspan = " . count($cgroup) . ">{$pgname->program_name}</td></tr>";

                                foreach ($cgroup as $keyc) {
                                    $ggroup = WGroupSetup::find()
                                        ->where([
                                            'gs_acadYear_relId' => $this->acadYearId,
                                            'gs_sem_relId' => $this->semId,
                                            'gs_dept_relId' => $keyd->gs_dept_relId,
                                            'gs_prog_relId' => $keyp->gs_prog_relId,
                                            'gs_clslvl_relId' => $keyc->gs_clslvl_relId,
                                        ])
                                        ->groupBy('gs_group_relId')
                                        ->all();
                                    if ($ggroup) {
                                        $cls = \backend\models\ClassLevel::findOne($keyc->gs_clslvl_relId);
                                        $divs .= "<tr><td style='vertical-align:middle;' rowspan = " . count($ggroup) . ">{$cls->clslvl_num}</td></tr>";
                                        foreach ($ggroup as $keyg) {
                                            $group = \backend\models\WGroup::findOne(['id' => $keyg->gs_group_relId])->group_name;

                                            $spec = is_null($keyg['gs_spec_relId']) ? '-' : \backend\models\Specialization::findOne(['id' => $keyg['gs_spec_relId']])->spec_name;

                                            $divs .= "<tr>
                                                    <td>{$group}</td>
                                                    <td>{$spec}</td>
                                                    <td><button class='btn btn-primary btn-xs' title='show students assigned in this group.'>Students</button></td>
                                                    </tr>
                                                    ";
                                        }
                                    } else {
                                        $divs .= "<tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>";
                                    }
                                }
                            } else {
                                $divs .= "<tr>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                             </tr>";
                            }
                        }
                    } else {
                        $divs .= "<tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>";
                    }
                }
                // $divs .= "</tr>";
            }
            echo "</tbody></table>";
            echo "{$divs}";
            die();
        }
    }

    // returns the list of students / groups WRT user selection  get-group-list
    public function actionGetGroupList2()
    {
        $this->__constructor();
        // returns the list of students if it is for the first time.
        $deptId = Yii::$app->request->post('deptId');
        $progId = Yii::$app->request->post('progId');
        $clslvl = Yii::$app->request->post('clslvlId');
        $admId = Yii::$app->request->post('admId');
        $specId = Yii::$app->request->post('specId');

        $specId = $specId == 0 ? NULL : $specId;

        $entryType = Yii::$app->request->post('entryId');
        $prevSemId = Yii::$app->request->post('prevSemId');
        $prevAcadYearId = Yii::$app->request->post('prevAcadYearId');


        if ($entryType == 0) {
            $stds = Student::find()
                ->where([
                    'std_admission_relId' => $admId,
                    'std_prog_relId' => $progId,
                    'std_dept_relId' => $deptId,
                    'std_clslvl_relId' => $clslvl,
                    'std_acadYear_relId' => $this->acadYearId,
                    'std_group_realId' => NULL,
                ]);
            if (isset($specId) && !is_null($specId)) {
                $stds = $stds->andFilterWhere(['std_spec_relId' => $specId]);
            }
            $stds = $stds->all();

            if (empty($stds) && count($stds) < 1) {
                echo "<p class='bg-danger'>No Un-grouped new entry student found / all the new entry students are grouped. </p>";

                // foreach($stds as $std){ .... };
            } else {
                $setUp = WGroupSetup::findOne([
                    'gs_acadYear_relId' => $this->acadYearId,
                    'gs_sem_relId' => $this->semId,
                    'gs_dept_relId' => $deptId,
                    'gs_adm_relId' => $admId,
                    'gs_prog_relId' => $progId,
                    'gs_clslvl_relId' => $clslvl,
                ]);

                echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
                echo "<caption><p class='bg-success'>Ungrouped students.</p></caption>";
                echo "<thead>
            <th>Select</th>
            <th>Reg. ID.</th>
            <th>Student Name</th>
            <th>Year Of Adm.</th>
            <th>Spec.</th>
            </thead><tbody>";
                foreach ($stds as $std) {
                    // $id = empty($setUp)?$$setUp->id:NULL;
                    if ($setUp) {
                        $ass = WGroupAssignment::findOne(['gas_gset_relId' => $setUp->id, 'gas_std_relId' => $std['id']]);
                        if (!empty($ass)) {
                            continue;
                        }
                    }
                    $usr = User::findOne(['id' => $std['std_user_relId']]);
                    $spec = $std['std_spec_relId'] ? \backend\models\Specialization::findOne(['id' => $std['std_spec_relId']])->spec_name : 'N/A';
                    $grp = 'N/A';
                    if (!is_null($std['std_group_realId'])) {
                        $gset = \backend\models\WGroupSetup::findOne(['id' => $std['std_group_realId']]);

                        if ($gset) {
                            $grp = \backend\models\WGroup::findOne(['id' => $gset->gs_group_relId])->group_name;
                        }
                    }
                    echo "<tr>
                <td><input type='checkbox' onchange='addToGroup(" . $std['id'] . ")' /></td>
                <td>" . $std['std_regId'] . "</td>
                <td>{$std->getFullName()}</td>
                <td>" . \backend\models\SchoolYear::findOne(['id' => $std['std_acadYear_relId']])->sch_year_desc . "</td>
                <td>" . $spec . "</td>
                </tr>";
                }
                echo "</tbody></table>";
            }
        } else {
            // $prevSemId;  
            // $prevAcadYearId     
            $prevGroup = isset($_POST['prevGroup']) && !empty($_POST['prevGroup']) ? intval($_POST['prevGroup']) : die('Prev Group Not selected.');
            $prevAcadYearId = isset($_POST['prevAcadYearId']) && !empty($_POST['prevAcadYearId']) ? intval($_POST['prevAcadYearId']) : die('Prev Acad. Year Not selected.');
            $prevSemId = isset($_POST['prevSemId']) && !empty($_POST['prevSemId']) ? intval($_POST['prevSemId']) : die('Prev semester Year Not selected.');

            $gset = \backend\models\WGroupSetup::find()
                ->where(['id' => $prevGroup, 'gs_acadYear_relId' => intval($prevAcadYearId), 'gs_sem_relId' => intval($prevSemId)])
                ->one();

            if ($gset) {
                $students = \backend\models\WGroupAssignment::find()
                    ->where(['gas_gset_relId' => $gset->id])->all();
                if ($students) {

                    $setUp = WGroupSetup::find()->where([
                        'gs_acadYear_relId' => $this->acadYearId,
                        'gs_sem_relId' => $this->semId,
                        'gs_dept_relId' => $deptId,
                        'gs_adm_relId' => $admId,
                        'gs_prog_relId' => $progId,
                        'gs_clslvl_relId' => $clslvl,
                    ])->all();

                    $setUpArray = [];
                    if ($setUp) {
                        foreach ($setUp as $key) {
                            $setUpArray[] = $key->id;
                        }
                    }
                    echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
                    echo "<caption><p class='bg-success'>Students in this group.</p></caption>";
                    echo "<thead>
                    <th>Select</th>
                    <th>Reg. ID.</th>
                    <th>Student Name</th>
                    <th>Prev. Grp.</th>
                    <th>Spec.</th>
                    </thead><tbody>";

                    $spec = isset($gset->gs_spec_relId) ? \backend\models\ClassLevel::findOne(['id' => $gset->gs_clslvl_relId])->clslvl_num . 'Yr.' : '-';

                    $title2 = \backend\models\Admission::findOne(['id' => $gset->gs_adm_relId])->admission_name;
                    $title2 .= '_' . \backend\models\Program::findOne(['id' => $gset->gs_prog_relId])->program_name;
                    $title2 .= '_' . \backend\models\Department::findOne(['id' => $gset->gs_dept_relId])->dept_name;
                    $title2 .= '_' . \backend\models\ClassLevel::findOne(['id' => $gset->gs_clslvl_relId])->clslvl_num . 'Yr.';
                    $title2 .= '_' . $spec;

                    $prevGroupName = \backend\models\WGroup::findOne(['id' => $gset->gs_group_relId])->group_name;
                    foreach ($students as $key) {
                        $std = \backend\models\Student::findOne(['id' => $key->gas_std_relId]);
                        $user = \backend\models\User::findOne(['id' => $std->std_user_relId]);

                        $exists = \backend\models\WGroupAssignment::find()->where(['gas_gset_relId' => $setUpArray, 'gas_std_relId' => $std->id])->one();

                        echo "<tr>";
                        if ($exists) {
                            $groupId = \backend\models\WGroupSetup::findOne(['id' => $exists->gas_gset_relId]);
                            $gname2 = \backend\models\WGroup::findOne(['id' => $groupId->gs_group_relId])->group_name;

                            $title = \backend\models\Admission::findOne(['id' => $groupId->gs_adm_relId])->admission_name;
                            $title .= '_' . \backend\models\Program::findOne(['id' => $groupId->gs_prog_relId])->program_name;
                            $title .= '_' . \backend\models\Department::findOne(['id' => $groupId->gs_dept_relId])->dept_name;
                            $title .= '_' . \backend\models\ClassLevel::findOne(['id' => $groupId->gs_clslvl_relId])->clslvl_num . 'Yr.';
                            // $title .= '_'.isset($groupId->gs_spec_relId)?\backend\models\Specialization::findOne(['id'=>$groupId->gs_spec_relId])->spec_name:'';


                            echo "
                            <td title='Assigned to {$title} in this semester.' style='text-align:center;'>{$gname2}</td>";
                        } else {
                            echo "
                            <td title='Not assigned to any group in this semester.' style='text-align:center;'><input type='checkbox' onchange='addToGroup(" . $std['id'] . ")' /></td>";
                        }
                        echo "
                            <td>" . $std->std_regId . "</td>
                            <td>{$std->getFullName()}</td>
                            <td title = '{$title2}' style='text-align:center;'>{$prevGroupName}</td>
                            <td>" . $spec . "</td>
                            </tr>";
                    }
                } else {
                    echo "<p class='bg-danger'>No student assignment to this group.</p>";
                }
            } else {
                echo "<p class='bg-danger'>Such group setup not made yet. Please re-try.</p>";
            }
            // echo "1";
        }
        die();
        //echo "Entry Type selected: ".$entryType;die();
    }

    // returns list of setups made for this semester.
    public function actionGetlist()
    {
        $this->__constructor();
        $grpsets = WGroupSetup::find()
            ->innerJoin('program', 'program.id = w_group_setup.gs_prog_relId')
            ->innerJoin('department', 'department.id = w_group_setup.gs_dept_relId')
            ->innerJoin('class_level', 'class_level.id = w_group_setup.gs_clslvl_relId')
            ->where([
                'gs_acadYear_relId' => $this->acadYearId,
                'gs_sem_relId' => $this->semId,
            ])->orderBy([
                'program.program_name' => SORT_ASC,
                'department.dept_name' => SORT_ASC,
                'class_level.clslvl_num' => SORT_ASC,
            ])->all();
        // 

        $wreport = "";
        if (empty($grpsets)) {
            echo "<p class='bg-danger'>No Setup made for this semester.</p>";
        } else {
            $wreport .= "<table class='table table-striped table-condensed table-sm table-bordered'>";
            $wreport .= "<thead style='background-color:#8D0;'>
            <th>Group</th>
            <th>Admission</th>
            <th>Program</th>
            <th>Department</th>
            <th>Cls. yr.</th>
            <th>Spec.</th>
            <th>Grp. set at</th>
            </thead><tbody>";
            $count = 0;
            foreach ($grpsets as $grpst) {
                $spec = is_null($grpst['gs_spec_relId']) ? 'N/A' : \backend\models\Specialization::findOne(['id' => $grpst['gs_spec_relId']])->spec_name;
                $wreport .= "<tr>

                    <td>" . \backend\models\WGroup::findOne(['id' => $grpst['gs_group_relId']])->group_name . "</td>
                    <td>" . \backend\models\Admission::findOne(['id' => $grpst['gs_adm_relId']])->admission_name . "</td>
                    <td>" . \backend\models\Program::findOne(['id' => $grpst['gs_prog_relId']])->program_name . "</td>
                    <td>" . \backend\models\Department::findOne(['id' => $grpst['gs_dept_relId']])->dept_name . "</td>
                    <td>" . \backend\models\ClassLevel::findOne(['id' => $grpst['gs_clslvl_relId']])->clslvl_name . "</td>
                    <td>" . $spec . "</td>
                    <td>" . date_format(date_create($grpst['gs_created_at']), 'D M, Y') . "</td>
                </tr>";
            }
            $wreport .= "</tbody></table>";
            return $wreport;
        }
    }

    // returns un-asigned free groups for group-setting..
    public function actionGetFreeGroupList()
    {
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? $_POST['admId'] : die('Admission Uknown');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? $_POST['progId'] : die('Program Uknown');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? $_POST['clslvlId'] : die('Class year Uknown');
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? $_POST['acadYearId'] : die('Academic Year Uknown');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? $_POST['semId'] : die('Semester Uknown');
        // $this->__constructor();
        var_dump($_POST);
        $groups = WGroup::find()->all();
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $exists = WGroupSetup::findOne([
                    'gs_acadYear_relId' => $acadYearId,
                    'gs_sem_relId' => $semId,
                    'gs_group_relId' => $group['id'],
                    'gs_clslvl_relId' => $clslvlId,
                    'gs_adm_relId' => $admId,
                    'gs_prog_relId' => $progId,
                ]);
                if ($exists) {
                    continue;
                } else {
                    echo "<option value='" . $group['id'] . "'>" . $group['group_name'] . "</option>";
                }
            }
        } else {
            echo "<option value=''>All groups assigned, please add one if you need.</option>";
        }
        // var_dump($groups);die();
    }

    // get-group-lists-peryear
    public function actionGetGroupListsPeryear()
    {
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? $_POST['admId'] : die('Admission Uknown');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? $_POST['progId'] : die('Program Uknown');
        // $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? $_POST['clslvlId'] : die('Class year Uknown');
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? $_POST['acadYearId'] : die('Academic Year Uknown');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? $_POST['semId'] : die('Semester Uknown');
        //$this->__constructor();

        // var_dump($_POST['specId']);die();

        $groups = WGroupSetup::find()->where([
            'gs_acadYear_relId' => $acadYearId,
            'gs_sem_relId' => $semId,
            'gs_adm_relId' => $admId,
            'gs_prog_relId' => $progId,
        ]);
        if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
            $groups = $groups->andWhere(['gs_dept_relId' => intval($_POST['deptId'])]);
        }
        if (isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])) {
            $groups = $groups->andWhere(['gs_clslvl_relId' => intval($_POST['clslvlId'])]);
        }
        // if (isset($_POST['specId']) && !empty($_POST['specId']) && $_POST['specId'] != '0') {
        //      $groups = $groups->andWhere(['OR',['gs_spec_relId' => intval($_POST['specId'])],['gs_spec_relId' => NULL]]);
        // }
        if (Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
            $groups = $groups->andWhere(['gs_dept_relId' => $inst->inst_dep_relId]);
        } else if (Yii::$app->user->can('Instructor')) {
            $semesterAdvisorModel = new \backend\models\SemesterAdvisor();
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
            $groupsSetIds = $semesterAdvisorModel->getAdvisingGsets($acadYearId, $semId, $admId, $progId, $inst->id);

            $groups = $groups->andWhere(['id' => $groupsSetIds]);
        }
        $groups = $groups->all();
        if (!empty($groups)) {
            echo "<option value='' title='Not needed'>None</option>";
            foreach ($groups as $group) {
                $title = \backend\models\Department::findOne(['id' => $group->gs_dept_relId])->dept_name;

                if (isset($group['gs_spec_relId']) && !empty($group['gs_spec_relId'])) {
                    $title .= ' : ' . \backend\models\Specialization::findOne(['id' => $group->gs_spec_relId])->spec_name;
                }

                $gId = $group['gs_group_relId'];
                $groupI = \backend\models\WGroup::findOne(['id' => $gId]);

                echo "<option value='" . $group['id'] . "' title='" . $title . "'>" . $groupI['group_name'] . "</option>";
            }
        } else {
            echo "<option value='' title='No group assigned, please set one....'> - </option>";
        }
        // var_dump($groups);die();
    }

    // get-group-lists-peryear
    public function actionGetGroupListsPeryear3()
    {
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? $_POST['admId'] : die('Admission Uknown');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? $_POST['progId'] : die('Program Uknown');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? $_POST['clslvlId'] : die('Class year Uknown');
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? $_POST['acadYearId'] : die('Academic Year Uknown');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? $_POST['semId'] : die('Semester Uknown');
        //$this->__constructor();

        // var_dump($_POST['specId']);die();

        $groups = WGroupSetup::find()->where([
            'gs_acadYear_relId' => $acadYearId,
            'gs_sem_relId' => $semId,
            'gs_clslvl_relId' => $clslvlId,
            'gs_adm_relId' => $admId,
            'gs_prog_relId' => $progId,
        ]);
        if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
            $groups = $groups->andWhere(['gs_dept_relId' => intval($_POST['deptId'])]);
        }
        if (isset($_POST['specId']) && !empty($_POST['specId'])) {
            $groups = $groups->andWhere(['OR', ['gs_spec_relId' => intval($_POST['specId'])], ['gs_spec_relId' => NULL]]);
        }
        $groups = $groups->all();
        if (!empty($groups)) {
            echo "<option value='' title='Not needed'>None</option>";
            foreach ($groups as $group) {
                $title = \backend\models\Department::findOne(['id' => $group->gs_dept_relId])->dept_name;

                if (isset($group['gs_spec_relId']) && !empty($group['gs_spec_relId'])) {
                    $title .= ' : ' . \backend\models\Specialization::findOne(['id' => $group->gs_spec_relId])->spec_name;
                }

                $gId = $group['gs_group_relId'];
                $groupI = \backend\models\WGroup::findOne(['id' => $gId]);

                echo "<option value='" . $group['id'] . "' title='" . $title . "'>" . $groupI['group_name'] . "</option>";
            }
        } else {
            echo "<option value='' title='No group assigned, please set one....'> - </option>";
        }
        // var_dump($groups);die();
    }
    // get-group-lists-peryear
    public function actionGetGroupListsPeryear2()
    {
        $prevAcadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? $_POST['acadYearId'] : die('Academic Year Uknown');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? $_POST['semId'] : die('Semester Uknown');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? $_POST['clslvlId'] : die('Class Year Uknown');
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? $_POST['admId'] : die('Admission Year Uknown');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? $_POST['progId'] : die('Program Year Uknown');
        //$this->__constructor();

        $prevSemObj = \backend\models\Semester::findOne($semId);
        $clslvlObj = \backend\models\ClassLevel::findOne($clslvlId);

        $prevClslvl = $clslvlObj->clslvl_num - 1;
        if ($prevSemObj->semester_name == 'I' && $clslvlObj->clslvl_num == 1) {
            return "Invalid Selection, Prev record NOT available.";
        }

        // 'clslvl_admission_relId'=>$admId,
        $pcl = \backend\models\ClassLevel::findOne([
            'clslvl_program_relId' => $progId,
            'clslvl_num' => $prevClslvl,
        ]);

        if ($pcl) {
            $prevClslvl = $pcl;
        } else {
            die('Invalid Selection');
        }

        $prevClslvlObj = $prevClslvl;
        $groups = WGroupSetup::find()->where([
            'gs_acadYear_relId' => intval($prevAcadYearId),
            'gs_sem_relId' => $prevSemObj->id,
            'gs_clslvl_relId' => $prevClslvlObj->id,
        ]);
        if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
            $groups->andWhere(['gs_dept_relId' => $_POST['deptId']]);
        }
        if (isset($_POST['specId']) && !empty($_POST['specId'])) {
            $groups->andWhere(['OR', ['gs_spec_relId' => $_POST['specId']], ['gs_spec_relId' => NULL]]);
        }
        if (isset($_POST['progId']) && !empty($_POST['progId'])) {
            $groups->andWhere(['gs_prog_relId' => $_POST['progId']]);
        }
        if (isset($_POST['admId']) && !empty($_POST['admId'])) {
            $groups->andWhere(['gs_adm_relId' => $_POST['admId']]);
        }

        // $groups = $groups->all();
        $groups = $groups->all();
        if (!empty($groups)) {
            echo "<option value='' title='None'>All</option>";
            foreach ($groups as $group) {
                $title = \backend\models\Admission::findOne(['id' => $group->gs_adm_relId])->admission_name;

                $title .= '_' . \backend\models\Program::findOne(['id' => $group->gs_prog_relId])->program_name;
                $title .= '_' . \backend\models\Department::findOne(['id' => $group->gs_dept_relId])->dept_name;
                $title .= '_' . \backend\models\ClassLevel::findOne(['id' => $group->gs_clslvl_relId])->clslvl_num;
                $title .= '_' . \backend\models\Semester::findOne(['id' => $group->gs_sem_relId])->semester_name;

                if (isset($group['gs_spec_relId']) && !empty($group['gs_spec_relId'])) {
                    $title .= ' _' . \backend\models\Specialization::findOne(['id' => $group->gs_spec_relId])->spec_name;
                }

                $gId = $group['gs_group_relId'];
                $groupI = \backend\models\WGroup::findOne(['id' => $gId]);

                echo "<option value='" . $group->id . "' title='" . $title . "'>" . $groupI['group_name'] . "</option>";
            }
        } else {
            echo "<option value='' title='No group assigned, please set one....'> - </option>";
        }
        // var_dump($groups);die();
    }

    // returns those groups assigned for an filter correspondingly.
    public function actionGetGroupLists()
    {
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? $_POST['admId'] : die('Admission Uknown');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? $_POST['progId'] : die('Program Uknown');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? $_POST['clslvlId'] : die('Class year Uknown');
        //$deptId = isset($_POST['deptId']) && !empty($_POST['deptId'])?$_POST['deptId']:die('Department Uknown');
        $this->__constructor();

        $groups = WGroupSetup::find()->where([
            'gs_acadYear_relId' => $this->acadYearId,
            'gs_sem_relId' => $this->semId,
            'gs_clslvl_relId' => $clslvlId,
            'gs_adm_relId' => $admId,
            'gs_prog_relId' => $progId,
        ]);
        if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
            $groups->andFilterWhere(['gs_dept_relId' => $_POST['deptId']]);
        }
        if (isset($_POST['specId']) && !empty($_POST['specId'])) {
            $groups->andFilterWhere(['gs_spec_relId' => $_POST['specId']]);
        }

        $cofTemp = new \backend\models\CourseOfferingTemp();
        $courseOffered = $cofTemp->checkIfOffered($this->acadYearId, $this->semId, $admId, $progId, $_POST['deptId'], $clslvlId, $_POST['specId'], 1); //1 indicates the offering shoul have to be approved by Registrar, before distributing to groups

        if ($courseOffered <= 0) {
            return "<option value=''> Course Not Offered for this section / Offering not yet approved. </option>";
        }

        $groups = $groups->all();
        if (!empty($groups)) {
            $title2 = ($courseOffered > 0) ? "Course Offered." : "Course NOT Offered Yet.";
            echo "<option value='' title='({$title2})'> - </option>";
            foreach ($groups as $group) {
                $title = \backend\models\Department::findOne(['id' => $group->gs_dept_relId])->dept_name;

                if (isset($group['gs_spec_relId']) && !empty($group['gs_spec_relId'])) {
                    $title .= ' : ' . \backend\models\Specialization::findOne(['id' => $group->gs_spec_relId])->spec_name;
                }

                $gId = $group['gs_group_relId'];
                $groupI = \backend\models\WGroup::findOne(['id' => $gId]);

                echo "<option value='" . $group['id'] . "' title='{$title} ({$title2})'>" . $groupI['group_name'] . "</option>";
            }
        } else {
            echo "<option value='' title='No group assigned, please set one....'> - </option>";
        }
        // var_dump($groups);die();
    }

    /**
     * Displays a single WGroupSetup model.
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
     * Creates a new WGroupSetup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WGroupSetup();

        if (!Yii::$app->user->can('Admin')) {
            throw new \yii\web\ForbiddenHttpException('The requested page does not exist.');
        }
        if ($model->load(Yii::$app->request->post())) {

            // var_dump($model->gs_active_prog_relId);die();

            // check if the group selected is already assigned for this semsetr or not. DONE
            // It will not be visible for selection if it is assigned before.

            $model->gs_created_at = $model->gs_updated_at = date('Y-m-d h:i:s');
            $model->gs_updated_by = $model->gs_created_by = Yii::$app->user->identity->id;
            $model->gs_spec_relId = $model->gs_spec_relId == 0 ? NULL : $model->gs_spec_relId;
            if ($model->save()) {
                // link the group setup to active academic program.
                $model->checkAcLink();
                return $this->redirect(['create', 'model' => new WGroupSetup()]);
            } else {
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', json_encode($model->getErrors()));
                return $this->render('create', ['model' => $model,]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WGroupSetup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
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
     * Deletes an existing WGroupSetup model.
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
     * Finds the WGroupSetup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WGroupSetup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WGroupSetup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
