<?php

namespace frontend\controllers;

use backend\models\CourseOffering2;
use backend\models\CourseOffering2Detail;
use yii;
use yii\widgets\ActiveForm;
use \common\components\CommonControllerFunctions as CCF;

class SemesterEvaluationController extends \yii\web\Controller
{

    public $acadYearId;
    public $semId;
    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    // Store evaluation record to file
    public function actionSaveEvaluation()
    {

        $evaluator = Yii::$app->user->identity->id;
        $semevRecId = isset($_POST['semevRecId']) && !empty($_POST['semevRecId']) ? intval($_POST['semevRecId']) : die('Uknown Progress record, please try again.');
        $givenPoint = isset($_POST['averageptGiven']) && !empty($_POST['averageptGiven']) ? $_POST['averageptGiven'] : die('Empty average point given');

        if (!is_numeric($givenPoint)) {
            die('Error!');
        }

        $evalType = isset($_POST['evalTypeSelected']) && !empty($_POST['evalTypeSelected']) ? intval($_POST['evalTypeSelected']) : die('Uknown Evaluation Type selected.');
        $courseId = intval($_POST['courseId']);

        if ($evalType == 8 && is_null($_POST['courseId'])) {
            die('Course should be selected');
        }

        $cccc = \backend\models\SemesterEvaluationTypes::findOne(['semev_type_level' => $evalType]);

        if ($cccc->semev_type_maxPoint < $givenPoint) {
            return "Average point Can NOT be more than {$cccc->semev_type_maxPoint} ";
        }

        $id = $cccc->id;
        $this->__constructor();

        //echo !is_null($semevId)?'Recording...':'Not did anything brfore.';

        // check if such SemRecId exists == $semevRecId

        $exst = \backend\models\SemesterEvaluation2::findOne([
            'id' => $semevRecId,
            'semev_acadYear_relId' => $this->acadYearId,
            'semev_sem_relId' => $this->semId,
            'semev_evalType_relId' => $id
        ]);

        //var_dump($exst);die();


        if ($exst) {
            // check if detail also exists, else create one:
            $exists2 = \backend\models\SemesterEvaluation2Detail::find()
                ->where([
                    'semev2det_semev_relId' => $semevRecId,
                    'semev2det_evaluator_relId' => Yii::$app->user->identity->id,
                    'semev2det_completed' => 0,
                ])
                ->one();
            if ($exists2) {
                # code...
            } else {
                $model = new \backend\models\SemesterEvaluation2Detail();
                $model->semev2det_semev_relId = $semevRecId;
                $model->semev2det_evaluator_relId = Yii::$app->user->identity->id;
                $model->semev2det_completed = 0;
                $model->save();
            }
        } else {
            die('Operation failed, no such record exists.');
        }
        $result = Yii::$app->db->createCommand()
            ->update(
                'semester_evaluation2_detail',
                [
                    'semev2det_completed' => 1,
                    'semev2det_evaluated_at' => date('Y-m-d h:i:s'),
                ],
                [
                    'semev2det_semev_relId' => $semevRecId,
                    'semev2det_evaluator_relId' => Yii::$app->user->identity->id,
                ]
            )
            ->execute();

        if ($result) {
            $ayi = $this->acadYearId;
            $aci = $this->semId;

            $pp = Yii::$app->db->createCommand("
                 UPDATE semester_evaluation2 
                 SET semev_averagePoint = semev_averagePoint + $givenPoint 
                 WHERE id = $semevRecId
                 ")
                ->execute();

            return $pp;
            // 
        } else {
            print_r($result);
            die();
        }
    }

    protected function getSERId($evalueeId, $semevId)
    {
        $idd = NULL;
        $evalueeRecord = \backend\models\SemesterEvaluationResult::findOne([
            'ser_evaluee_relId' => $evalueeId,
            'ser_semev_relId' => $semevId,
        ]);

        if ($evalueeRecord) {
            $idd = $evalueeRecord->id;
        } else {
            // $evalueeRecord
            $mm = new \backend\models\SemesterEvaluationResult();
            $mm->ser_semev_relId = $semevId;
            $mm->ser_evaluee_relId = $evalueeId;
            $mm->ser_point_given = 0.00;

            if ($mm->save()) {
                $idd = $mm->id;
            } else {
                die('Creating report pot failed');
            }
        }

        return $idd;
    }

    // Get list of evaluation points for evaluating Inst and / TA.
    public function actionGetRatingScale2()
    {
        $ok = 0;
        $rs = '';
        $message = '';
        $progError = [];
        $rsN = '';


        if (isset($_POST['semevRecId']) && !empty($_POST['semevRecId']) && isset($_POST['evalType']) && !empty($_POST['evalType'])) {

            $semevRecId = intval($_POST['semevRecId']);
            $evalType = intval($_POST['evalType']);
            $course = intval($_POST['course']);

            $suc = \backend\models\SemesterEvaluation2Detail::findOne([
                'semev2det_evaluator_relId' => Yii::$app->user->identity->id,
                'semev2det_semev_relId' => $semevRecId,
            ]);

            if ($suc) {
                if ($suc->semev2det_completed == 1) {
                    $message = 'You completed evaluation for this evaluee.';
                    $ok = 0;
                } else if ($suc->semev2det_completed == 0) {
                    $ok = 1;
                }
            } else {
                $model = new \backend\models\SemesterEvaluation2Detail();
                $model->semev2det_semev_relId = $semevRecId;
                $model->semev2det_evaluator_relId = Yii::$app->user->identity->id;
                $model->semev2det_completed = 0;

                if ($model->save()) {
                    $ok = 1;
                } else {
                    $ok = 0;
                    $message = 'Failed to track progress, please retry later.';
                    $progError = $model->errors;
                }
            }
        } else {
            $ok = 0;
            $message = 'Please select the evaluee before proceeding evaluation.';
        }

        if ($ok == 1) {

            $evalTypeObj = \backend\models\SemesterEvaluationTypes::findOne($evalType);

            if ($evalTypeObj && $evalTypeObj->semev_type_level > 100) {
                $rs = "<input type='number' scale='0.01' max= '{$evalTypeObj->semev_type_maxPoint}' min=0/>";
            } else {
                $rs =  "<select><option value='' title='Choose your rating scale for this point'>-</option>
                        <option value='1' title='Very Low'>1</option>
                        <option value='2' title='Low'>2</option>
                        <option value='3' title='Average'>3</option>
                        <option value='4' title='High'>4</option>
                        <option value='5' title='Very High'>5</option>
                        <option value='0' title='Not Applicable'>0</option></select>";
            }
        }

        echo json_encode([
            'ok' => $ok,
            'message' => $message,
            'rs' => $rs,
            'rsN' => $rsN,
            'progError' => $progError,
        ]);
        die();
    }


    // Get evaluation ponts for evaluating and evaluee
    public function actionGetPoints()
    {
        // get-points
        $evalType = isset($_POST['evalType']) && !empty($_POST['evalType']) ? $_POST['evalType'] : die('Evaluation type should have to be known. Please select one from availables');

        $evalTypId = \backend\models\SemesterEvaluationTypes::findOne(['semev_type_level' => $evalType])->id;

        $evalPoints = \backend\models\SemesterEvaluationPoints::find()
            ->where([
                'semevPoint_semev_relId' => $evalTypId,
            ])->all();

        $form = ActiveForm::begin();
        $model = new \backend\models\SemesterEvaluationtypes();

        if ($evalPoints) {
            echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
            echo "<caption><p class='bg-success'> <i class='bg-danger' id='totalCount'>" . count($evalPoints) . "</i> Evaluation points.</p></caption>";
            echo "<thead style='background-color: #69E;'>
            <th>#</th>
            <th>Point</th>
            <th>Rate</th>
            </thead><tbody>";
            $count = 0;

            foreach ($evalPoints as $evalPoint) {
                echo "<tr>
                <td>" . ++$count . "</td>
                <td>" . $evalPoint['sem_eval_point_desc'] . "</td>
                <td>";
?>
                <?= $form->field($model, 'semev_type_level')->dropDownList(
                    [],
                    [
                        'onChange' => '
                                        addPoint(' . $evalPoint['id'] . ')
                                    ',
                        'class' => 'ser_semev_point',
                        'id' => 'ser_semev_point' . $evalPoint['id'],
                        'required' => 'true',


                    ]
                )->label('') ?>
                <?php
                echo "<input type='hidden' value='0' id='ser_semev_" . $evalPoint['id'] . "'></td>
                </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='bg-danger'>No Evaluation point found.</p>";
        }
    }

    // checks if the student is assigned to a group, if grouped, it returns the gsetup id, and else it prints the approprite message and dies.

    protected function checkGroupAssigned($stdObject)
    {
        $this->__constructor();
        $gset = \backend\models\WGroupSetup::find()
            ->where([
                'gs_acadYear_relId' => $this->acadYearId,
                'gs_sem_relId' => $this->semId,
                'gs_adm_relId' => $stdObject->std_admission_relId,
                'gs_prog_relId' => $stdObject->std_prog_relId,
            ])
            ->innerJoin('w_group_assignment', 'w_group_setup.id = w_group_assignment.gas_gset_relId')
            ->andWhere(['w_group_assignment.gas_std_relId' => $stdObject->id])
            ->with('wGroupAssignments')
            ->one();
        return $gset;
    }

    // get-evaluee-courses
    public function actionGetEvalueeCourses()
    {
        $message = '';
        $ok = 0;
        $courses = '';
        $courses .= '<select class="form-control" id="ddddd" onchange="loadRatingScale()">';

        // Check if selected the evaluation Type
        $evalType = isset($_POST['evalType']) && !empty($_POST['evalType']) ? $_POST['evalType'] : die('Uknown Evaluation Type');
        $semevRecId = isset($_POST['semevRecId']) && !empty($_POST['semevRecId']) ? $_POST['semevRecId'] : die('Uknown Progress. please re-satrt it.');

        $evalType = intval($evalType);
        $semevRecId = intval($semevRecId);
        $evalTypeO = \backend\models\SemesterEvaluationTypes::findOne(['semev_type_level' => $evalType]);
        $evalTypeId = $evalTypeO->id;

        // Initialize current Seme and academic year
        $this->__constructor();

        $semevRec = \backend\models\SemesterEvaluation2::findOne([
            'semev_acadYear_relId' => $this->acadYearId,
            'semev_sem_relId' => $this->semId,
            'semev_evalType_relId' => $evalTypeId,
            'id' => $semevRecId,
        ]);

        if (Yii::$app->user->can('Student')) {

            //var_dump($semevRecId);die();

            $std = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);

            $gsetId = $this->checkGroupAssigned($std);
            // $semevRec = $semevRec->andWhere(['semev_group_relId'=>$gsetId]);
            // Student are allowed to evaluate instructors and TAs.

            // if ($semevRec->semev_group_relId != $gsetId) {
            //     die('Invalid Evaluation progress, Please restart');
            // }

            if ($evalType == 4 || $evalType == 5 || $evalType == 12) { //Students can only evaluate INSTs and TAs.

                //var_dump($semevRec);die();
                if ($semevRec) {
                    $courses .= "<option value=''> - </option>";
                    $ok = 1;

                    $course = \backend\models\Course::findOne(['id' => $semevRec->semev_course_relId]);

                    $courses .= "<option value='" . $course->id . "'>" . $course->course_title . "</option>";


                    $courses .= "</select>";
                }
            }
        } else if (Yii::$app->user->can('Instructor')) {
            if ($evalType == 6 || $evalType == 12) { //Evaluate PEER

                $courses .= "<option value=''> - </option>";
                $ok = 2;

                // $course = \backend\models\Course::findOne(['id'=>$semevRec->semev_course_relId]);

                // $courses .= "<option value='". $course->id ."'>". $course->course_title ."</option>";


                $courses = "<input id='eval_type_{$evalTypeO->semev_type_level}' type='number' class='form-control' max='{$evalTypeO->semev_type_maxPoint}' step='0.01' min='0.00' value='0.00' required='' /> [$evalTypeO->semev_type_maxPoint]";
            } else if ($evalType == 7) { // Evaluate TAs for your course.
                $courses .= "<option value=''> - </option>";
                $ok = 1;

                $course = \backend\models\Course::findOne(['id' => $semevRec->semev_course_relId]);

                $courses .= "<option value='" . $course->id . "'>" . $course->course_title . "</option>";


                $courses .= "</select>";
            }
        } else if (Yii::$app->user->can('DepartmentHead')) {
            if ($evalType == 8 || $evalType == 6) { //If it is instructor supervission by HoD

                //$courses .= "<option value=''> - </option>";
                $ok = 2;

                // $course = \backend\models\Course::findOne(['id'=>$semevRec->semev_course_relId]);

                // $courses .= "<option value='". $course->id ."'>". $course->course_title ."</option>";


                $courses = "<input id='eval_type_{$evalTypeO->semev_type_level}' type='number' value='0.00' class='form-control' max='{$evalTypeO->semev_type_maxPoint}' step='0.01' min='0.00' required='' /> [$evalTypeO->semev_type_maxPoint]";
            }
        } else if (Yii::$app->user->can('PGCoordinator')) {
            if ($evalType == 13) {
                //$courses .= "<option value=''> - </option>";
                $ok = 2;

                // $course = \backend\models\Course::findOne(['id'=>$semevRec->semev_course_relId]);

                // $courses .= "<option value='". $course->id ."'>". $course->course_title ."</option>";


                $courses = "<input id='eval_type_{$evalTypeO->semev_type_level}' type='number' value='0.00' class='form-control' max='{$evalTypeO->semev_type_maxPoint}' step='0.01' min='0.00' required='' /> [$evalTypeO->semev_type_maxPoint]";
            }
        } else if (Yii::$app->user->can('AcademicDean')) {
            if ($evalType == 11) {
                //$courses .= "<option value='' title='HoDs'> - </option>";
                $ok = 2;

                // $course = \backend\models\Course::findOne(['id'=>$semevRec->semev_course_relId]);

                // $courses .= "<option value='". $course->id ."'>". $course->course_title ."</option>";


                $courses = "<input id='eval_type_{$evalTypeO->semev_type_level}' type='number' value='0.00' class='form-control' max='{$evalTypeO->semev_type_maxPoint}' step='0.01' min='0.00' required='' /> [$evalTypeO->semev_type_maxPoint]";
            }
        }
        echo json_encode([
            'ok' => $ok,
            'message' => $message,
            'evaluee' => $courses,
        ]);
        die();
    }

    // 
    protected function getGroupCourseInstructor($instId, $gsetId)
    {
        $this->__constructor();
        $inst = NULL;
        $coffId = CourseOffering2::findOne([
            'coff2_acadYear_relId' => $this->acadYearId,
            'coff2_sem_relId' => $this->semId,
            'coff2_group_relId' => $gsetId,
        ]);

        if ($coffId) {
            $coff2Det = CourseOffering2Detail::find()->where([
                'coff2det_coff2_relId' => $coffId->id,
            ])
                ->andFilterWhere(['OR', 'coff2det_inst_relId' => $instId, 'coff2det_TA_relId' => $instId])
                ->One();

            if ($coff2Det && count($coff2Det) > 0) {
                if (is_null($coff2Det->coff2det_TA_relId)) {
                    $inst = \backend\models\Instructor::findOne(['id' => $coff2Det->coff2det_inst_relId]);
                } else {
                    $inst = \backend\models\Instructor::findOne(['id' => $coff2Det->coff2det_TA_relId]);
                }
            }
        }
        return $inst;
    }

    protected function getSemevRecord($gsetId, $courseId, $evalueeId, $evalType, $fs = false)
    {
        $recId = null;
        $this->__constructor();
        // var_dump($evalueeId);die();
        //$instId = \backend\models\Instructor::findOne(['id'=>$evalueeId])->inst_user_relId;
        $usr = \backend\models\User::findOne(['id' => $evalueeId]);
        $semevRec = \backend\models\SemesterEvaluation2::findOne([
            'semev_acadYear_relId' => $this->acadYearId,
            'semev_sem_relId' => $this->semId,
            'semev_group_relId' => $gsetId,
            'semev_course_relId' => $courseId,
            'semev_evalType_relId' => $evalType,
            'semev_evaluee_relId' => $evalueeId,
        ]);

        if ($semevRec) {
            if ($fs) {
                return $semevRec;
            }
            $recId = $semevRec->id;
        } else {
            if ($fs) {
                echo json_encode([
                    'ok' => 0,
                    'message' => 'Error while saving your evaluation record. please try again, Your evalaution is not saved.',
                ]);
                die();
            }
            $model = new \backend\models\SemesterEvaluation2();
            $model->semev_acadYear_relId = $this->acadYearId;
            $model->semev_sem_relId = $this->semId;
            $model->semev_group_relId = $gsetId;
            $model->semev_course_relId = $courseId;
            $model->semev_evalType_relId = intval($evalType);
            $model->semev_evaluee_relId = $evalueeId;
            $model->semev_averagePoint = 0.00;

            if ($model->save()) {
                $recId = $model->id;
            } else {
                print_r($model->errors);
                die();
            }
        }

        return $recId;
    }

    // Get evaluee 2
    public function actionGetEvalueeToEvaluate2()
    {

        $message = '';
        $ok = 0;
        $evaluee = '';
        $evaluee .= '<select class="form-control" id="22222222222222" onchange="loadEvalueeCourses()">';

        // Check if selected the evaluation Type
        $evalType = isset($_POST['evalType']) && !empty($_POST['evalType']) ? $_POST['evalType'] : die('Uknown Evaluation Type_1');
        $evalType = intval($evalType);


        if (Yii::$app->user->can('Student')) {

            $std = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);

            $gsetId = $this->checkGroupAssigned($std);

            if (!($gsetId && count($gsetId) > 0)) {
                die('Not assigned to any group');
            }

            // Student are allowed to evaluate instructors and TAs.

            if ($evalType == 4 || $evalType == 5 || $evalType == 12) { //Students can only evaluate INSTs and TAs.
                // Initialize current Seme and academic year
                $this->__constructor();

                // Getting list of added courses
                $addedWithGroupsArray = [];
                $gsetArray = [];

                $addedWithGroups = \backend\models\AddHistory::find()->where([
                    'ah_acadYear_relId' => $this->acadYearId,
                    'ah_sem_relId' => $this->semId,
                    'ah_std_relId' => $std->id
                ])->all();

                if ($addedWithGroups && count($addedWithGroups) > 0) {
                    foreach ($addedWithGroups as $key) {
                        $gsetArray[] = $key['ah_addWithGroup_relId'];
                    }
                }
                $gsetArray[] = $gsetId->id;
                $coffArray = [];
                $id = \backend\models\SemesterEvaluationTypes::findOne(['semev_type_level' => $evalType])->id;
                $coff =  \backend\models\CourseOffering2::find()->where([
                    'coff2_acadYear_relId' => $this->acadYearId,
                    'coff2_sem_relId' => $this->semId,
                    'coff2_group_relId' => $gsetArray,
                ])->all();
                if ($coff && count($coff) > 0) {
                    foreach ($coff as $key) {
                        $coffArray[] = $key['id'];
                    }
                }

                if ($coff && count($coff) > 0) {

                    //var_dump($evalType);die();
                    $offeredCourses = \backend\models\CourseOffering2Detail::find()
                        ->where(['coff2det_coff2_relId' => $coffArray])
                        ->andWhere(['IS NOT', 'coff2det_inst_relId', NULL]);

                    if ($evalType == 5) { // inst evaluation is assigned level 4 and TA is 5.
                        $offeredCourses = $offeredCourses->andWhere(['IS NOT', 'coff2det_TA_relId', NULL]);
                    }

                    $enCoursesArray = [];
                    // Students can evaluate those instructors who thought them, 
                    $enrolledCourses = \frontend\models\SemesterRegistration2Detail::find()
                        ->innerJoinWith(
                            'srd2SemregRel',
                            'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId'
                        )
                        ->andWhere([
                            'semreg_acadYear_relId' => $this->acadYearId,
                        ])->andWhere([
                            'semreg_semester_relId' => $this->semId,
                        ])->andWhere(['semreg_std_relId' => $std->id,])
                        ->groupBy('srd2_course_relId')
                        ->all();


                    if ($enrolledCourses && count($enrolledCourses) > 0) {
                        foreach ($enrolledCourses as $enrolledCourse) {
                            array_push($enCoursesArray, $enrolledCourse['srd2_course_relId']);
                        }
                    }

                    //Eliminate those courses which inst / TAs not assigned.
                    if ($evalType == 5) { // inst evaluation is assigned level 4 and TA is 5.
                        $offeredCourses = $offeredCourses->andWhere(['IS NOT', 'coff2det_TA_relId', NULL]);
                    }

                    // filter those courses only enrolled by student.
                    $offeredCourses = $offeredCourses->andWhere(['IN', 'coff2det_course_relId', $enCoursesArray]);

                    $offeredCoursesCount = $offeredCourses->groupBy('coff2det_course_relId')->all();

                    if ($offeredCoursesCount && count($offeredCoursesCount) > 0) {
                        $ok = 1;
                        $evaluee .= "<option value=''> - </option>";
                        foreach ($offeredCoursesCount  as $offeredCourse) {
                            // Check if the student evaluated the inst for this course

                            //$evaluatedPersons = [];

                            $gset2 = '';

                            $gset2 = \backend\models\CourseOffering2::findOne(['id' => $offeredCourse['coff2det_coff2_relId']])->coff2_group_relId;

                            // Don't forget evalType is evaluation level, you have to get the record's id fro eval types file.

                            $evalTypeId = \backend\models\SemesterEvaluationTypes::findOne(['semev_type_level' => $evalType])->id;

                            // You have to evaluate him/her as per the job he/she done. so evaluation for Lab session and evaluation for the lecture session is separate, even if it is held by one persob. 
                            // Get semevId for this course,inst,group,
                            $tt = $evalType == 5 ? $offeredCourse['coff2det_TA_relId'] : ($evalType == 4 || $evalType == 12 ? $offeredCourse['coff2det_inst_relId'] : die('Uknown Evaluation Type.'));

                            $tit = $evalType == 5 ? '[ LAB ]' : '{ Lecture }';

                            $INSTusrID = \backend\models\Instructor::findOne(['id' => $tt])->inst_user_relId;


                            // check if evaluee is evaluated before,
                            $evaluatedPerson = \backend\models\SemesterEvaluation2::find()
                                ->innerJoinWith('semesterEvaluation2Details', 'semester_evaluation2_detail.semev2det_semev_relId = semester_evaluation2.id')
                                ->where([
                                    'semev_acadYear_relId' => $this->acadYearId,
                                    'semev_sem_relId' => $this->semId,
                                    'semev_group_relId' => $gset2,
                                    'semev_course_relId' => $offeredCourse['coff2det_course_relId'],
                                    'semev_evalType_relId' => $evalTypeId,
                                    'semev_evaluee_relId' => $INSTusrID,
                                ])->andWhere([
                                    'semev2det_evaluator_relId' => Yii::$app->user->identity->id,
                                    'semev2det_completed' => 1
                                ])
                                ->all();

                            if ($evaluatedPerson && count($evaluatedPerson) > 0) {
                                continue;
                                $message .= "Already evalutaed SomeOne." . $evaluatedPerson;
                            } else {

                                $user = \backend\models\User::findOne(['id' => $INSTusrID]);
                                $course = \backend\models\Course::findOne(['id' => $offeredCourse['coff2det_course_relId']]);
                                $recId = $this->getSemevRecord($gset2, $offeredCourse['coff2det_course_relId'], $user->id, $evalTypeId);

                                $evaluee .= "<option value='" . $recId . "' title='" . $course->course_title . ' - ' . $tit . "'>" . $user->first_name . ' ' . $user->middle_name . "</option>";
                            }
                        }

                        $evaluee .= "</select>";
                    } else {
                        $ok = 0;
                        $message = "<p class='bg-warning'>You not enrolled any course in this semester.</p>";
                    }
                } else {
                    $ok = 0;
                    $grp = \backend\models\WGroup::findOne(['id' => $gsetId->gs_group_relId])->group_name;
                    $message = "<p class='bg-warning'>No course offered for your group in this semester {$grp}. No one to evaluate.</p>";
                }
            }
        } else if (Yii::$app->user->can('Instructor')) {
            if ($evalType == 6 || $evalType == 7) { //7 for TAs assisting inst courses ans 6 for PEER intsts.
                $evalTypeId = \backend\models\SemesterEvaluationTypes::findOne(['semev_type_level' => $evalType]);

                $instructor = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
                $this->__constructor();
                if ($evalTypeId && $instructor) {

                    $ok = 0;
                    $message = 'Loading evaluee';
                    if ($evalType == 6) { // Load PEER evaluee
                        // 1. Inst have to be assgned at least 1 course for this semester to evaluate his/her peers.

                        $coff = CourseOffering2::find()
                            ->innerJoinWith(
                                'coff2CoffDet',
                                'course_offering2.id = course_offering2_detail.coff2det_coff2_relId'
                            )
                            ->where([
                                'course_offering2.coff2_acadYear_relId' => $this->acadYearId,
                            ])
                            ->andWhere(['course_offering2.coff2_sem_relId' => $this->semId,])
                            ->andWhere([
                                'course_offering2_detail.coff2det_inst_relId' => $instructor->id,
                            ])
                            ->all();

                        if (!($coff && count($coff) > 0)) {
                            $ok = 0;
                            $message = 'You didn\'t assigned any course in this semester, No one to evaluate.';
                        } else {
                            // Those instructors in his/ her dept.
                            $peers = \backend\models\Instructor::find()
                                ->where([
                                    'inst_dep_relId' => $instructor->inst_dep_relId
                                ])
                                ->all();

                            if ($peers && count($peers) > 0) {
                                $ok = 1;
                                $evaluee .= "<option value=''> - </option>";

                                foreach ($peers as $peer) {

                                    if ($peer['id'] == $instructor->id) {
                                        continue;
                                    }

                                    $coff3 = $this->ckeckCourseAssigned($peer['id']);
                                    if (is_null($coff3)) {
                                        continue;
                                    }

                                    //var_dump($coff3);die();


                                    // $TA = '';
                                    // $recId = '';
                                    // if ($coff3['coff2CoffDet']['coff2det_TA_relId'] == $peer['id']) {
                                    // }

                                    foreach ($coff3 as $keys) {
                                        if ($keys['coff2CoffDet']['coff2det_inst_relId'] == $instructor->id && $keys['coff2CoffDet']['coff2det_TA_relId'] == $instructor->id) {
                                            continue;
                                        }

                                        if (is_null($keys['coff2CoffDet']['coff2det_TA_relId']) && $keys['coff2CoffDet']['coff2det_inst_relId'] == $instructor->id) {
                                            continue;
                                        }
                                        //     echo "<pre>";
                                        // var_dump($peer);die();
                                        $recId = $this->getSemevRecord(NULL, NULL, $peer['inst_user_relId'], $evalTypeId->id);

                                        $TA = \backend\models\Instructor::findOne(['id' => $peer['id']]);
                                        $user = \backend\models\User::findOne(['id' => $TA->inst_user_relId]);

                                        // Load thos evaluees who are not evaluated yet.
                                        $evaluatedPerson = \backend\models\SemesterEvaluation2::find()
                                            ->innerJoinWith('semesterEvaluation2Details', 'semester_evaluation2_detail.semev2det_semev_relId = semester_evaluation2.id')
                                            ->where([
                                                'semev_acadYear_relId' => $this->acadYearId,
                                                'semev_sem_relId' => $this->semId,
                                                'semev_evaluee_relId' => $user->id,
                                                'semev_evalType_relId' => $evalTypeId->id,
                                            ])->andWhere([
                                                'semev2det_evaluator_relId' => Yii::$app->user->identity->id,
                                                'semev2det_completed' => 1
                                            ])
                                            ->all();

                                        $course = \backend\models\Course::findOne(['id' => $keys['coff2CoffDet']['coff2det_course_relId']]);

                                        if ($evaluatedPerson && count($evaluatedPerson) > 0) {
                                            continue;
                                        }
                                        $evaluee .= "<option value='" . $recId . "' title='" . $course->course_title . "'>" . $user->first_name . ' ' . $user->middle_name . "</option>";
                                    }
                                }
                                $evaluee .= "</select>";
                            } else {
                                $ok = 0;
                                $message = 'No peer instructor assigned course for this semseter found.';
                            }
                        }

                        // 2. All his dept. instructors who held course for this semester except the current user are to be laoded as an evaluee fo the evaluator

                        // 3. Each Insts with the assigned course is listed as ana evalee for evaluation. 
                    } else if ($evalType == 7) { //Load TAs who held the insts course in this semsetr.
                        // $message .= ' [TAs]';
                        // 1. Get Inst courses assigned for him/her in this semester.

                        $coff = \backend\models\CourseOffering2::find()
                            ->innerJoinWith(
                                'coff2CoffDet',
                                'course_offering2.id = course_offering2_detail.coff2det_coff2_relId'
                            )
                            ->where([
                                'course_offering2.coff2_acadYear_relId' => $this->acadYearId,
                            ])
                            ->andWhere(['course_offering2.coff2_sem_relId' => $this->semId,])
                            ->andWhere([
                                'course_offering2_detail.coff2det_inst_relId' => $instructor->id,
                            ])->groupBy('course_offering2_detail.coff2det_TA_relId')
                            ->all();

                        if ($coff && count($coff) > 0) {
                            $ok = 1;
                            $evaluee .= "<option value=''> - </option>";
                            foreach ($coff as $co) {
                                if (is_null($co['coff2CoffDet']['coff2det_TA_relId']) || $co['coff2CoffDet']['coff2det_TA_relId'] == $instructor->id) {
                                    continue;
                                } else {

                                    $recId = $this->getSemevRecord(NULL, $co['coff2CoffDet']['coff2det_course_relId'], $co['coff2CoffDet']['coff2det_TA_relId'], $evalTypeId->id);

                                    $TA = \backend\models\Instructor::findOne(['id' => $co['coff2CoffDet']['coff2det_TA_relId']]);
                                    $course = \backend\models\Course::findOne(['id' => $co['coff2CoffDet']['coff2det_course_relId']]);
                                    $user = \backend\models\User::findOne(['id' => $TA->inst_user_relId]);

                                    // Load thos evaluees who are not evaluated yet.
                                    $evaluatedPerson = \backend\models\SemesterEvaluation2::find()
                                        ->innerJoinWith('semesterEvaluation2Details', 'semester_evaluation2_detail.semev2det_semev_relId = semester_evaluation2.id')
                                        ->where([
                                            'semev_acadYear_relId' => $this->acadYearId,
                                            'semev_sem_relId' => $this->semId,
                                            'semev_evaluee_relId' => $user->id,
                                            'semev_course_relId' => $co['coff2CoffDet']['coff2det_course_relId'],
                                            'semev_evalType_relId' => $evalTypeId->id,
                                        ])->andWhere([
                                            'semev2det_evaluator_relId' => Yii::$app->user->identity->id,
                                            'semev2det_completed' => 1
                                        ])
                                        ->all();

                                    if ($evaluatedPerson && count($evaluatedPerson) > 0) {
                                        continue;
                                    }


                                    $evaluee .= "<option title='" . $course->course_title . "' value='" . $recId . "'>" . $user->first_name . ' ' . $user->middle_name . "</option>";
                                }
                            }

                            $evaluee .= "</select>";

                            $message = 'You have ' . count($coff) . ' Course(s) in this semseter.';
                        } else {
                            $message = 'You have No course assigned fpr this semseter';
                        }
                    } else {
                        $message =  "<p class='bg-danger'>Invalid Evaluation type for you.</p>";
                    }
                } else {
                    $message =  "<p class='bg-danger'>Invalid Evaluation type provided, any Change made? (::).</p>";
                }
            }
        } elseif (Yii::$app->user->can('DepartmentHead')) {

            $evalTypeId = \backend\models\SemesterEvaluationTypes::findOne(['semev_type_level' => $evalType]);

            $instructor = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
            $this->__constructor();
            if ($evalTypeId && $instructor) {

                $ok = 0;
                $message = 'Loading evaluee';
                if ($evalType == 8 || $evalType == 6) {
                    // Load Dept Instructors / and or PEERS. HoD & Peers
                    // Those instructors in his/ her dept.
                    $peers = \backend\models\Instructor::find()
                        ->where([
                            'inst_dep_relId' => $instructor->inst_dep_relId
                        ])
                        ->all();

                    if ($peers && count($peers) > 0) {
                        $ok = 2;
                        $evaluee .= "<option value=''> - </option>";

                        foreach ($peers as $peer) {

                            if ($peer['id'] == $instructor->id) {
                                $message .= 'Loading evaluee []->' . 'SELF_EVALUATION_SKIPPED. ';
                                continue;
                            }

                            $coff3 = $this->ckeckCourseAssigned($peer['id'], true);
                            if (is_null($coff3)) {
                                $message .= 'Loading evaluee []->' . 'COURSE_NOT_ASSIGNED ';
                                continue;
                            }

                            //var_dump($coff3);die();


                            // $TA = '';
                            // $recId = '';
                            // if ($coff3['coff2CoffDet']['coff2det_TA_relId'] == $peer['id']) {
                            // }

                            foreach ($coff3 as $keys) {

                                if ($keys['coff2CoffDet']['coff2det_inst_relId'] == $instructor->id && $keys['coff2CoffDet']['coff2det_TA_relId'] == $instructor->id) {
                                    continue;
                                }

                                if (is_null($keys['coff2CoffDet']['coff2det_TA_relId']) && $keys['coff2CoffDet']['coff2det_inst_relId'] == $instructor->id) {
                                    continue;
                                }

                                $recId = $this->getSemevRecord(NULL, NULL, $peer['inst_user_relId'], $evalTypeId->id);

                                $TA = \backend\models\Instructor::findOne(['id' => $peer['id']]);
                                $user = \backend\models\User::findOne(['id' => $TA->inst_user_relId]);

                                // 'semev_course_relId'=>$keys['coff2CoffDet']['coff2det_course_relId'],
                                // Load thos evaluees who are not evaluated yet.
                                $evaluatedPerson = \backend\models\SemesterEvaluation2::find()
                                    ->innerJoinWith('semesterEvaluation2Details', 'semester_evaluation2_detail.semev2det_semev_relId = semester_evaluation2.id')
                                    ->where([
                                        'semev_acadYear_relId' => $this->acadYearId,
                                        'semev_sem_relId' => $this->semId,
                                        'semev_evaluee_relId' => $user->id,
                                        'semev_evalType_relId' => $evalTypeId->id,
                                    ])->andWhere([
                                        'semev2det_evaluator_relId' => Yii::$app->user->identity->id,
                                        'semev2det_completed' => 1
                                    ])
                                    ->all();

                                //$course = \backend\models\Course::findOne(['id'=>$keys['coff2CoffDet']['coff2det_course_relId']]);

                                if ($evaluatedPerson && count($evaluatedPerson) > 0) {
                                    $message .= 'Loading evaluee []->' . 'EVALUATED. ';
                                    continue;
                                }
                                $evaluee .= "<option value='" . $recId . "' >" . $user->first_name . ' ' . $user->middle_name . "</option>";
                            }
                        }
                        $evaluee .= "</select>";
                    } else {
                        $ok = 0;
                        $message = 'No peer instructor assigned course for this semseter found.';
                    }
                } else if ($evalType == 9) { //Load Tech Assistance in the dept.
                    $ok = 0;
                    $message = "Evaluee not available for this type of evaluation..";
                } else {
                    $ok = 0;
                    $message = 'Uknown Evaluation Types for you.';
                }
            }
        } else if (Yii::$app->user->can('PGCoordinator')) {
            if ($evalType == 13) {

                $evalTypeId = \backend\models\SemesterEvaluationTypes::findOne(['semev_type_level' => $evalType]);
                if (!($evalTypeId)) {
                    die('Invalid Evaluation Type for you!');
                }
                // Load all Instructors who held PG courses in this semester.
                $pgProgram = \backend\models\Program::findOne(['program_name' => 'MASTERS']);
                $pgOffrdCourses = $this->getPGInstructors($pgProgram->id);

                if (is_null($pgOffrdCourses)) {
                    die('No Course Offered for PG-Students In this Semester.');
                } else {
                    if (is_array($pgOffrdCourses)) {
                        if ($pgOffrdCourses && count($pgOffrdCourses) > 0) {
                            $ok = 1;
                            $evaluee .= "<option value=''> - </option>";
                            foreach ($pgOffrdCourses as $keys) {
                                // if ($keys['coff2CoffDet']['coff2det_inst_relId'] == $instructor->id && $keys['coff2CoffDet']['coff2det_TA_relId'] == $instructor->id) {
                                //         continue;
                                //     }

                                // if (is_null($keys['coff2CoffDet']['coff2det_TA_relId']) && $keys['coff2CoffDet']['coff2det_inst_relId'] == $instructor->id) {
                                //    continue;
                                //     }

                                $TA = \backend\models\Instructor::findOne(['id' => $keys['coff2CoffDet']['coff2det_inst_relId']]);
                                $user = \backend\models\User::findOne(['id' => $TA->inst_user_relId]);


                                // Load thos evaluees who are not evaluated yet.
                                $evaluatedPerson = \backend\models\SemesterEvaluation2::find()
                                    ->innerJoinWith('semesterEvaluation2Details', 'semester_evaluation2_detail.semev2det_semev_relId = semester_evaluation2.id')
                                    ->where([
                                        'semev_acadYear_relId' => $this->acadYearId,
                                        'semev_sem_relId' => $this->semId,
                                        'semev_evaluee_relId' => $user->id,
                                        'semev_evalType_relId' => $evalTypeId->id,
                                    ])->andWhere([
                                        'semev2det_evaluator_relId' => Yii::$app->user->identity->id,
                                        'semev2det_completed' => 1
                                    ])
                                    ->all();

                                if ($evaluatedPerson && count($evaluatedPerson) > 0) {
                                    $message .= 'Loading evaluee []->' . 'EVALUATED. ';
                                    continue;
                                }
                                $recId = $this->getSemevRecord(NULL, NULL, $user->id, $evalTypeId->id);

                                //$course = \backend\models\Course::findOne(['id'=>$keys['coff2CoffDet']['coff2det_course_relId']]);
                                $evaluee .= "<option value='" . $recId . "'>" . $user->first_name . ' ' . $user->middle_name . "</option>";
                            }
                            $evaluee .= "</select>";
                        } else {
                            die('Course Offering Not made yet.');
                        }
                    } else {
                        die($pgOffrdCourses);
                    }
                }
                // return those instructors.
            } else {
                $message = "No Evaluee available for this type.";
            }
        } else if (Yii::$app->user->can('AcademicDean')) {
            if ($evalType == 11) { // supervise HoDs
                $this->__constructor();
                // get all active dept heads with their dept.
                $dhs = \backend\models\DepartmentHead::find()->where([
                    'dh_status' => 1,
                ])->all();

                $evalTypeId = \backend\models\SemesterEvaluationTypes::findOne(['semev_type_level' => $evalType]);

                $dhArray = [];
                if ($dhs && count($dhs) > 0) {
                    $ok = 2;
                    $evaluee .= "<option value=''> - </option>";
                    foreach ($dhs as $key) {
                        $inst = \backend\models\Instructor::findOne(['id' => $key['dh_inst_relId']]);
                        $user = \backend\models\User::findOne(['id' => $inst->inst_user_relId]);
                        $fullName = $user->first_name . ' ' . $user->middle_name;
                        $evaluatedPerson = \backend\models\SemesterEvaluation2::find()
                            ->innerJoinWith('semesterEvaluation2Details', 'semester_evaluation2_detail.semev2det_semev_relId = semester_evaluation2.id')
                            ->where([
                                'semev_acadYear_relId' => $this->acadYearId,
                                'semev_sem_relId' => $this->semId,
                                'semev_evaluee_relId' => $user->id,
                                'semev_evalType_relId' => $evalTypeId->id,
                            ])->andWhere([
                                'semev2det_evaluator_relId' => Yii::$app->user->identity->id,
                                'semev2det_completed' => 1
                            ])
                            ->all();

                        if ($evaluatedPerson && count($evaluatedPerson) > 0) {
                            $message .= 'Loading evaluee []->' . 'EVALUATED. ';
                            continue;
                        }
                        $dept = \backend\models\Department::findOne(['id' => $key['dh_dept_relId']])->dept_name;

                        $recId = $this->getSemevRecord(NULL, NULL, $user->id, $evalTypeId->id);


                        $evaluee .= "<option value='" . $recId . "' title='{$dept}'>" .  $fullName . "</option>";

                        //$key['fullName'] = $fullName;
                    }
                    $evaluee .= "</select>";
                }
            }
            //var_dump($dhs);die();
        } else {
            $ok = 0;
            $message = 'Not made available yet. please wait a moment.';
        }


        echo json_encode([
            'ok' => $ok,
            'message' => $message,
            'evaluee' => $evaluee,
        ]);
        die();
    }

    // Returns the list of instructors who assigned PG courses in this semester and acadYear.
    protected function getPGInstructors($pgProgId)
    {
        // Get PG courses for the catalog

        // Initialize acadYear and Semster
        $this->__constructor();
        // Get PG groups assigned in this semester.
        $pgGroups = \backend\models\WGroupSetup::find()
            ->where([
                'gs_acadYear_relId' => $this->acadYearId,
                'gs_sem_relId' => $this->semId,
                'gs_prog_relId' => $pgProgId,
            ])
            ->all();
        if (!($pgGroups && count($pgGroups) > 0)) {
            die('No PG Course assigned for this semsetr.');
        }

        $pgGroupsArray = [];
        foreach ($pgGroups as $key) {
            array_push($pgGroupsArray, $key['id']);
        }
        // Get Offering detail for thesse course
        $pgOffering = \backend\models\CourseOffering2::find()
            ->where([
                'coff2_acadYear_relId' => $this->acadYearId,
                'coff2_sem_relId' => $this->semId,
                'coff2_group_relId' => $pgGroupsArray,
            ])
            ->innerJoin('course_offering2_detail', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
            ->with(['coff2CoffDet'])
            ->all();

        return $pgOffering;
        // return its Object.
    }

    // Check if course is assigned to an instructor

    protected function ckeckCourseAssigned($instId, $ff = false)
    {
        $this->__constructor();
        $coff = CourseOffering2::find()
            ->innerJoinWith(
                'coff2CoffDet',
                'course_offering2.id = course_offering2_detail.coff2det_coff2_relId'
            )
            ->filterWhere([
                'course_offering2.coff2_acadYear_relId' => $this->acadYearId,
            ])
            ->andFilterWhere(['course_offering2.coff2_sem_relId' => $this->semId])
            ->andFilterWhere([
                'OR',
                ['IN', 'course_offering2_detail.coff2det_inst_relId', $instId],
                ['IN', 'course_offering2_detail.coff2det_TA_relId', $instId]
            ])
            ->groupBy('course_offering2_detail.coff2det_group_relId')
            ->groupBy('course_offering2_detail.coff2det_course_relId');
        if ($ff) {
            $coff = $coff->groupBy('course_offering2_detail.coff2det_inst_relId');
        }

        $coff = $coff->all();
        return $coff;
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetEvaluationType()
    {
        $message = NULL;
        $ok = 0;
        $evalOpt = '<select class="form-control" onchange="GetPerson($(this).val())">';

        $actionName = 'Semester evaluation';
        $action = \backend\models\AcademicCalanderAction::findOne(['aca_name' => $actionName]);
        if ($action) {
            $gset = NULL;
            $schedule = CCF::getScheduleForAction($gset, $action->id);
            if (!($schedule && count($schedule))) {
                die('<p class="bg-danger">Schedule is not open for Semester Evaluation, please come after a while.</p>');
            }
        } else {
            die('<p class="bg-danger">Unregistered action {$actionName}</p>');
        }

        if (Yii::$app->user->can('Student')) {

            // check if the student is of MS student.
            $evalTypes = '';
            $student = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
            $program = \backend\models\Program::findOne(['id' => $student->std_prog_relId]);
            if (strtoupper($program->program_name) == 'MASTERS') {
                $evalTypes = \backend\models\SemesterEvaluationTypes::find()
                    ->where(['IN', 'semev_type_level', [12]])
                    ->all();
            } else {
                $evalTypes = \backend\models\SemesterEvaluationTypes::find()
                    ->where(['IN', 'semev_type_level', [4, 5]])
                    ->all();
            }

            if ($evalTypes) {
                $ok = 1;
                $evalOpt .= "<option value=''> - </option>";
                foreach ($evalTypes as $evalType) {
                    $evalOpt .= "<option value='" . $evalType['semev_type_level'] . "'>" . $evalType['semev_type_desc'] . "</option>";
                }

                $evalOpt .= '</select>';
            } else {
                $message =  "<p class='bg-warning'>No Evaluation made available for you.</p>";
            }
        } else if (Yii::$app->user->can('Instructor')) {
            // Instructors can evaluate their Teaching assistants and their peers.
            $evalTypes = \backend\models\SemesterEvaluationTypes::find()
                ->where(['semev_type_level' => [7, 6]])
                ->all();

            if ($evalTypes) {
                $ok = 1;
                $evalOpt .= "<option value=''> - </option>";
                foreach ($evalTypes as $evalType) {
                    $evalOpt .= "<option value='" . $evalType['semev_type_level'] . "'>" . $evalType['semev_type_desc'] . "</option>";
                }
            } else {
                $message =  "<p class='bg-warning'>No Evaluation made available for you.</p>";
            }
            $evalOpt .= '</select>';
        } else if (Yii::$app->user->can('DepartmentHead')) {
            $evalTypes = \backend\models\SemesterEvaluationTypes::find()
                ->where(['IN', 'semev_type_level', [8, 6]])
                ->all();

            // 8 - suppervise Instructor
            // 6 - Evaluate PEERs.

            if ($evalTypes) {
                $ok = 1;
                $evalOpt .= "<option value=''> - </option>";
                foreach ($evalTypes as $evalType) {
                    $evalOpt .= "<option value='" . $evalType['semev_type_level'] . "'>" . $evalType['semev_type_desc'] . "</option>";
                }

                $evalOpt .= '</select>';
            } else {
                $message =  "<p class='bg-warning'>No Evaluation made available for you.</p>";
            }
        } else if (Yii::$app->user->can('AcademicDean')) {
            $evalTypes = \backend\models\SemesterEvaluationTypes::find()
                ->where(['IN', 'semev_type_level', [11]])
                ->all();
            if ($evalTypes) {
                $ok = 1;
                $evalOpt .= "<option value=''> - </option>";
                foreach ($evalTypes as $evalType) {
                    $evalOpt .= "<option value='" . $evalType['semev_type_level'] . "'>" . $evalType['semev_type_desc'] . "</option>";
                }

                $evalOpt .= '</select>';
            } else {
                $message =  "<p class='bg-warning'>No Evaluation Type made available for you. <i>Please contact developers...</i></p>";
            }
        } else if (Yii::$app->user->can('PGCoordinator')) {
            $evalTypes = \backend\models\SemesterEvaluationTypes::find()
                ->where(['IN', 'semev_type_level', [13]])
                ->all();
            // evaluate PG instructors.
            if ($evalTypes) {
                $ok = 1;
                $evalOpt .= "<option value=''> - </option>";
                foreach ($evalTypes as $evalType) {
                    $evalOpt .= "<option value='" . $evalType['semev_type_level'] . "'>" . $evalType['semev_type_desc'] . "</option>";
                }

                $evalOpt .= '</select>';
            } else {
                $message =  "<p class='bg-warning'>No Evaluation Type made available for you. <i>Please contact developers...</i></p>";
            }
        } else if (Yii::$app->user->can('Commandant')) {
            $evalTypes = \backend\models\SemesterEvaluationTypes::find()
                ->where(['IN', 'semev_type_level', [10]])
                ->all();
            // 10 => evaluation level for suppervise deans.
            if ($evalTypes) {
                $ok = 1;
                $evalOpt .= "<option value=''> - </option>";
                foreach ($evalTypes as $evalType) {
                    $evalOpt .= "<option value='" . $evalType['semev_type_level'] . "'>" . $evalType['semev_type_desc'] . "</option>";
                }

                $evalOpt .= '</select>';
            } else {
                $message =  "<p class='bg-warning'>No Evaluation Type made available for you. <i>Please contact developers...</i></p>";
            }
        }else if(Yii::$app->user->can('Technical Assistant')){
            $evalTypes = \backend\models\SemesterEvaluationTypes::find()
                ->where(['semev_type_level' => 7])
                ->all();

            if ($evalTypes) {
                $ok = 1;
                $evalOpt .= "<option value=''> - </option>";
                foreach ($evalTypes as $evalType) {
                    $evalOpt .= "<option value='" . $evalType['semev_type_level'] . "'>" . $evalType['semev_type_desc'] . "</option>";
                }
            } else {
                $message =  "<p class='bg-warning'>No Evaluation made available for you.</p>";
            }
            $evalOpt .= '</select>';
        }


        echo json_encode([
            'ok' => $ok,
            'message' => $message,
            'evalOpt' => $evalOpt,
        ]);
        die();
    }

    protected function GetSemevId($fs = false)
    {
        $this->__constructor();

        $semevId = null;

        //$std = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);

        $semevExists = \backend\models\SemesterEvaluation::findOne([
            'semev_acadYear_relId' => $this->acadYearId,
            'semev_sem_relId' => $this->semId,
            'semev_evaluator_relId' => Yii::$app->user->identity->id,
        ]);

        if ($semevExists) {
            $semevId = $semevExists->id;
        } else {
            if ($fs) {
                return NULL;
            }
            $model = new \backend\models\SemesterEvaluation();
            $model->semev_acadYear_relId = $this->acadYearId;
            $model->semev_sem_relId = $this->semId;
            $model->semev_evaluator_relId = Yii::$app->user->identity->id;

            if ($model->save()) {
                $semevId = $model->id;
            } else {
                print_r($model->errors);
                die();
            }
        }

        return $semevId;
    }
}
