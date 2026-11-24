<?php

namespace frontend\controllers;

use yii;
use common\components\CommonControllerFunctions as CCF;
use backend\models\AddHistory;
use frontend\models\SemesterRegistration2;
use frontend\models\SemesterRegistration2Detail;
use backend\models\Student;
use yii\filters\VerbFilter;

class ApproveCourseAddController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
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
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }
    public $acadYearId;
    public $semId;
    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionChangerequeststatus()
    {

        $actionId = isset($_POST['actionId']) && !empty($_POST['actionId']) ? $_POST['actionId'] : die('Uknown Action');
        $reqId = isset($_POST['reqId']) && !empty($_POST['reqId']) ? $_POST['reqId'] : die('Uknown Request');
        $this->__constructor();

        $actionId = $actionId == 1 ? $actionId : 0;

        $request  = AddHistory::findOne([
            'id' => $reqId,
            'ah_approved' => NULL,
        ]);
        //$this->__constructor();

        if ($request) {
            if ($actionId == 1) { //gonna to approve the request.
                $stdId = $request->ah_std_relId;
                $courseId = $request->ah_course_relId;

                $grpId = $request->ah_addWithGroup_relId; //this is setup

                $student = Student::findOne(['id' => $stdId]);
                $gset = $student->stdGroupReal;

                // Get semester registration detail made this semester.
                $semreg2 = SemesterRegistration2::findOne([
                    'semester_registration_2.semreg_std_relId' => $stdId,
                    'semester_registration_2.semreg_acadYear_relId' => $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id,
                    'semester_registration_2.semreg_semester_relId' => $gset->gsActiveProgRel->semester_relId,
                ]);
                if ($semreg2) {
                    $rd = $this->getCoff($grpId, $courseId);

                    if ($rd) {
                        $coffId = $rd;
                    } else {
                        die('Course not offered for your selected group.');
                    }

                    // check if such exixsts- incase student is adding the course for the second time.
                    $exists = SemesterRegistration2Detail::findOne([
                        'srd2_semreg_relId' => $semreg2->id,
                        'srd2_course_relId' => $courseId,
                        'srd2_coff_relId' => $coffId,
                    ]);

                    // check if the course is enrolled and completed with valid grade

                    // check 2.

                    $validGradeListArray = [];
                    $validGrades = \backend\models\GradeScale::find()
                        ->where(['gs_ltr_grade' => ['F', 'D', 'C', 'C+']])
                        ->all();

                    if ($validGrades) {
                        foreach ($validGrades as $key) {
                            $validGradeListArray[] = $key->id;
                        }
                    }

                    $exists2 = SemesterRegistration2Detail::find()
                        ->where([
                            'srd2_course_relId' => $courseId,
                            'srd2_grade_relId' => $validGradeListArray,
                        ])
                        ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                        ->andWhere(['semester_registration_2.semreg_std_relId' => $stdId])
                        ->one();

                    // if (strtoupper($gsetObj->gsProgRel->program_name) == 'MASTERS') {$exists = NULL;}

                    // check if the course was successfully enrolled and graded.
                    if ($exists && is_null($exists->srd2_grade_relId)) {
                        die('The course is enrolled for this semester. You can not add this course.');
                    }

                    $model = new SemesterRegistration2Detail();
                    if ($exists2) {
                        // This course is enrolled before.
                        // Update it

                        // In case if masters thesis add is allowed to bypass reptd flag, add condition here.
                        // if (strtoupper($student->stdProgRel->program_name) == 'MASTERS') {
                        //     // Bypass repeated flag
                        // }else{

                        // }


                        $model->srd2_corptd_flag = 1;
                    } else {
                        $model->srd2_corptd_flag = 0;
                    }

                    $model->srd2_semreg_relId = $semreg2->id;
                    $model->srd2_coff_relId = $coffId;
                    $model->srd2_course_relId = $courseId;
                    $model->srd2_created_at = $model->srd2_updated_at = date('Y-m-d h:i:s');
                    $model->srd2_created_by = $model->srd2_updated_by = Yii::$app->user->identity->id;
                    $model->srd2_group_relId = $grpId;
                    $model->srd2_coadd_flag = $reqId;

                    if ($model->save()) {
                        $result = Yii::$app->db->createCommand()
                            ->update(
                                'add_history',
                                [
                                    'ah_approved' => $actionId,
                                    'ah_approved_by' => Yii::$app->user->identity->id,
                                    'ah_approved_at' => date('Y-m-d h:i:s'),
                                    'ah_srd2_relId' => $model->id,
                                ],
                                'id=' . $reqId
                            )
                            ->execute();
                        if ($result) {
                            echo 1;
                        } else {
                            echo "Failed to Approve the request.";
                        }
                    } else {
                        //$result->rollBack();
                        echo "Course enrolling failed.";
                        print_r($model->errors);
                    }
                } else {
                    // Create semester registration record for this student.
                    $Smodel = new SemesterRegistration2();
                    $Smodel->semreg_std_relId = $student->id;
                    $Smodel->semreg_semester_relId = $gset->gsActiveProgRel->semester_relId;
                    $Smodel->semreg_acadYear_relId = $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id;
                    $Smodel->semreg_clslvl_relId = $student->std_clslvl_relId;
                    $Smodel->semreg_created_by = $Smodel->semreg_updated_by = Yii::$app->user->identity->id;
                    $Smodel->semreg_created_at = $Smodel->semreg_updated_at = date('Y-m-d h:i:s');

                    if ($Smodel->save()) {
                        echo "Error for a while, please try again";
                        die();
                    } else {
                        print_r($Smodel->errors);
                    }
                }
                die();
            } else {
                $result = Yii::$app->db->createCommand()
                    ->update(
                        'add_history',
                        [
                            'ah_approved' => $actionId,
                            'ah_approved_by' => Yii::$app->user->identity->id,
                            'ah_approved_at' => date('Y-m-d h:i:s'),
                        ],
                        'id=' . $reqId
                    )
                    ->execute();
                if ($result) {
                    echo 1;
                } else {
                    echo "Failed to Approve the request.";
                }
            }
        } else {
            echo "No such add request found!";
            die();
        }
    }

    // Must return the courses offering histry id but not the course offering detail ID
    // Takes GroupSetupId and CoursaeId.
    protected function getCoff($gsId, $courseId)
    {
        if (empty($gsId) || empty($courseId)) {
            return false;
        } else {
            $this->__constructor();
            $gset = \backend\models\WGroupSetup::findOne(['id' => $gsId]);

            $catIds = [];
            $cocat = \backend\models\CourseCatalog::find()->where([
                'cocat_course_relId' => $courseId,
                'cocat_dept_relId' => $gset->gs_dept_relId,
                'cocat_admission_relId' => $gset->gs_adm_relId,
                'cocat_program_relId' => $gset->gs_prog_relId,
            ])->andWhere(['OR', ['cocat_sem_relId' => $gset->gsActiveProgRel->semester_relId,], ['cocat_sem_relId' => NULL]])->all();
            if ($cocat) {

                foreach ($cocat as $c) {
                    $catIds[] = $c->id;
                }
                $coff2 = \backend\models\CourseOffering2::find()
                    ->innerJoinWith('courseOffering2Details', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
                    ->andWhere([
                        'course_offering2.coff2_acadYear_relId' => $gset->gsActiveProgRel->academicYearRel->schoolYearRel->id,
                        'course_offering2.coff2_sem_relId' => $gset->gsActiveProgRel->semester_relId,
                        'course_offering2.coff2_group_relId' => $gsId
                    ])
                    ->andWhere(['course_offering2_detail.coff2det_cocat_relId' => $catIds])
                    ->with('courseOffering2Details')
                    ->One();


                //$coff = \backend\models\CourseOffering2::findOne(['coff2_cocat_relId'=>$cocat->id]);
                //var_dump($coff2['coff2detCoff2Rel']->id);die();
                return $coff2 ? $coff2->id : false;
            } else {
                echo "Course Catalog NOT available!";
                die();
                return false;
            }
        }
    }


    public function actionGetapprovedaddrequests()
    {
        $this->__constructor();
        // if(Yii::$app->user->can('DepartmentHead')){}
        $rep = "";
        $reqs = AddHistory::find()
            ->where([
                'ah_acadYear_relId' => $this->acadYearId,
                'ah_sem_relId' => $this->semId,
                'ah_approved_by' => Yii::$app->user->identity->id,
            ])
            ->andWhere([
                'OR',
                'ah_approved = 1',
                'ah_approved = 0'
            ])
            ->all();
        if ($reqs) {
            $rep .= "<table class='table table-striped table-condensed table-xs table-bordered' style='font-size:9pt;'>";
            $rep .= "<caption><p class='bg-success'> <i class='bg-danger'>" . count($reqs) . "</i> approved requests.</p></caption>";
            $rep .= "<thead>
                <th>RegId</th>
                <th>Full Name</th>
                <th>Course to add</th>
                 <th>Add with Group</th>
                 <th colspan = 2>Action</th>
                </thead><tbody>";
            foreach ($reqs as $std) {
                $student = \backend\models\Student::findOne(['id' => $std['ah_std_relId']]);
                $usr = \backend\models\User::findOne(['id' => $student->std_user_relId]);
                $grp = \backend\models\WGroupSetup::findOne(['id' => $std['ah_addWithGroup_relId']]);
                $rep .= "<tr>
                <td>" . $student->std_regId . "</td>
                <td>" . $usr->first_name . ' ' . $usr->middle_name . "</td>
                <td>" . \backend\models\Course::findOne(['id' => $std['ah_course_relId']])->course_title . "</td>
                <td>" . \backend\models\WGroup::findOne(['id' => $grp->gs_group_relId])->group_name . "</td>
                <td style='text-align:center;'>
                <button class='btn btn-success btn-link' title='See more.' onclick='seeMore(" . $std['id'] . ")'>?</button>
                </td>
                </tr>";
            }
            $rep .= "</tbody>";

            $rep .= "</table>";
            echo $rep;
        } else {
            echo "<p class='bg-danger'>No approved course.</p>";
        }
    }

    public function actionGetmoreaboutaddrequest()
    {
        $reqId = isset($_POST['reqId']) && !empty($_POST['reqId']) ? $_POST['reqId'] : die('Uknown request');
        $this->__constructor();
        $resp = "";
        // if(Yii::$app->user->can('DepartmentHead')){}
        $req = AddHistory::findOne([
            'id' => $reqId,
        ]);

        if ($req) {
            $resp .= "<table style='text-align:left;' class='table table-striped table-condensed table-sm table-bordered'>";
            $resp .= "<caption><p class='bg-success'> <i class='bg-success'>See more detail about the add course request</i></p></caption>";
            $resp .= "<thead style='background-color: #9DC;'>
            <th>Requested by</th>
            <th>Course</th>
            <th>Add with</th>
            </thead><tbody>";
            $student = $req->ahStdRel;
            $usr = \backend\models\User::findOne(['id' => $student->std_user_relId]);
            $grpId = \backend\models\WGroupSetup::findOne(['id' => $req->ah_addWithGroup_relId]);
            $spec = \backend\models\Specialization::findOne(['id' => $grpId->gs_spec_relId]);
            $addWithSpec = is_null($spec) ? '-' : $spec->spec_name;
            $resp .= "<tr>
            <td>
                <b>Reg. ID: </b>" . $student->std_regId . "<br>
                <b>Name: </b>{$student->getFullName()}<br>
                <b>Department: </b>" . \backend\models\Department::findOne(['id' => $student->std_dept_relId])->dept_name . "<br>
                <b>Class Year: </b>" . \backend\models\ClassLevel::findOne(['id' => $student->std_clslvl_relId])->clslvl_name  . "<br>
            </td>
            <td>{$req->ahCourseRel->course_title}</td>
            <td>
                <b>Group: </b>" . \backend\models\WGroup::findOne(['id' => $grpId->gs_group_relId])->group_name . "
                <br><b>Department: </b>" . \backend\models\Department::findOne(['id' => $grpId->gs_dept_relId])->dept_name . "<br>
                <b>Class Year (batch): </b>" . \backend\models\ClassLevel::findOne(['id' => $grpId->gs_clslvl_relId])->clslvl_name . "<br>
                <b>Specialization: </b>" . $addWithSpec . "
                </td>
            </tr>";
            // changeStatus(". $student->id .",".$req->id.")

            $resp .= "</tbody>";
            $statuss = $req->ah_approved == 1 ? 'Approved' : (is_null($req->ah_approved) ? 'Pending..' : 'Rejected');
            $resp .= "<tr style='text-align:center; background-color: #ace;'>
                    <td colspan=3>
                        Request Status: <u><i>" . $statuss . "</u></i>
                    </td>
                </tr>";
            $resp .= "</table>";
            echo $resp;
        } else {
            echo "<p class='bg-danger'>No such course add request found.</p>";
        }
        return;
    }


    public function actionGetaddrequests()
    {
        $this->__constructor();
        $instCourses = [];
        if (Yii::$app->user->can('Instructor') || Yii::$app->user->can('DepartmentHead')) {
            // Get courses assigned for the instructor in this semester.
            $instructor = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
        }
        $reqs = AddHistory::find()
            ->innerJoin('course_offering2_detail', 'course_offering2_detail.id=add_history.ah_coff_relId')
            ->where([
                'ah_acadYear_relId' => $this->acadYearId,
                'ah_sem_relId' => $this->semId,
                'ah_approved' => NULL,
            ])
            ->andWhere([
                'coff2det_inst_relId' => $instructor->id
            ])
            ->all();
        if ($reqs) {
            $report = "";
            $report .= "<table class='table table-striped table-condensed table-sm table-bordered' style='font-size:9pt;'>";
            $report .= "<caption><p class='bg-success'> <i class='bg-danger'>" . count($reqs) . "</i> Request(s).</p></caption>";
            $report .= "<thead>
                    <th>RegId</th>
                    <th>Full Name</th>
                    <th>Course to add</th>
                     <th>Add with Group</th>
                     <th colspan = 2>Action</th>
                    </thead><tbody>";
            foreach ($reqs as $std) {
                $student = $std->ahStdRel;//\backend\models\Student::findOne(['id' => $std['ah_std_relId']]);
                // $usr = \backend\models\User::findOne(['id' => $student->std_user_relId]);
                // $grp = \backend\models\WGroupSetup::findOne(['id' => $std['ah_addWithGroup_relId']]);
                $report .= "<tr>
                        <td>{$student->std_regId}</td>
                        <td title='" . CCF::$acadYearId . "'>{$student->getFullName()}</td>
                        <td>{$std->ahCourseRel->course_title}</td>
                        <td>{$std->ahAddWithGroupRel->gsGroupRel->group_name}:{$std->ahAddWithGroupRel->getGsDisp()}</td>
                        <td>
                        <button class='btn btn-success btn-link btn-sm' title='See more.' onclick='seeMore(" . $std['id'] . ")'>?</button>
                        </td><td>
                        <select title='select action' onchange='changeStatus({$std->id},$(this).val())'>
                            <option value=' '> </option>
                            <option value='1'>Approve</option>
                            <option value='0'>Reject</option>
                        </select></td>
                        </tr>";
            }
            $report .= "</tbody></table>";
            return $report;
        } else {
            return "<p class='bg-success'>No approval pending request found.</p>";
        }
    }
}
