<?php


/**
 * This is the model class for table "course_offering_temp".
 *
 * @property integer $id
 * @property integer $cot_acadYear_relId
 * @property integer $cot_sem_relId
 * @property integer $cot_adm_relId
 * @property integer $cot_prog_relId
 * @property integer $cot_dept_relId 
 * @property integer $cot_clslvel_relId
 * @property integer $cot_spec_relId
 * @property string $cot_cocat_IDS
 * @property string $cot_created_at
 * @property integer $cot_created_by
 * @property string $cot_updated_at
 * @property integer $cot_updated_by
 *
 * @property SchoolYear $cotAcadYearRel
 * @property Admission $cotAdmRel
 * @property ClassLevel $cotClslvelRel
 * @property User $cotCreatedBy
 * @property User $cotUpdatedBy
 * @property Department $cotDeptRel
 * @property Program $cotProgRel
 * @property Semester $cotSemRel
 * @property Specialization $cotSpecRel
 */

namespace frontend\controllers;

use yii;
use \common\components\CommonControllerFunctions as CCF;
use yii\helpers\ArrayHelper;
use \backend\models\CourseCatalog;
use yii\filters\VerbFilter;

class CourseOffering3Controller extends \yii\web\Controller
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
                        // Allow  access to logged in users only
                        'roles' => ['@',],
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

        $model = new \backend\models\CourseOffering2();
        $instDept = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id])->inst_dep_relId;
        $model->HoDDept = ArrayHelper::map(\backend\models\Department::find()->where(['id' => $instDept])->all(), 'id', 'dept_name');

        return $this->render('index', ['model' => $model]);
        // return $this->render('index',['model'=>$model]);
    }

    // Create Offer_Course pool
    public function actionOfferCourses()
    {
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('Academic Year Not Known.');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('Semester Not Known.');
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Admission Not Known.');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Program Not Known.');
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId']) ? intval($_POST['deptId']) : die('Department Not Known.');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Class Year Not Known.');
        $specId = isset($_POST['specId']) && !empty($_POST['specId']) ? intval($_POST['specId']) : NULL;

        $cocatLists = isset($_POST['catList']) && !empty($_POST['catList']) ? $_POST['catList'] : die('Course List Empty.');

        $model = new \backend\models\CourseOfferingTemp();
        if ($model->checkIfOffered($acadYearId, $semId, $admId, $progId, $deptId, $clslvlId, $specId) > 0) {
            // check if anychange exists on courses List.

            $cocats = $model->getOfferedCourses($acadYearId, $semId, $admId, $progId, $deptId, $clslvlId, $specId, 1);

            if ($cocats[2] == 0) {
                $newList = array_merge($cocats[0], json_decode($cocatLists));

                return Yii::$app->db->createCommand()
                    ->update(
                        'course_offering_temp',
                        [
                            'cot_cocat_IDS' => json_encode($newList),
                        ],
                        [
                            'id' => $cocats[1]
                        ]
                    )
                    ->execute();
            }

            return "Offering Completed, No Further action Allowed. Please Contact registrar if you have any complain.";
        } else {
            $model->cot_acadYear_relId = $acadYearId;
            $model->cot_sem_relId = $semId;
            $model->cot_adm_relId = $admId;
            $model->cot_prog_relId = $progId;
            $model->cot_dept_relId = $deptId;
            $model->cot_clslvel_relId = $clslvlId;
            $model->cot_spec_relId = $specId;
            $model->cot_cocat_IDS = $cocatLists;
            $model->cot_registrarApproved = 1;

            $model->cot_created_by = Yii::$app->user->identity->id;
            $model->cot_created_at = date('Y-m-d h:i:s');
            // $model->cot_cocat_IDS = $cocatLists;
            // $model->cot_cocat_IDS = $cocatLists;

            if ($model->save()) {
                return 1;
            } else {
                var_dump($model->getErrors());
                die();
            }
        }

        // die();	
    }

    // Get Offered Courses
    public function actionGetOfferedCourses()
    {
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('Academic Year Not Known.');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('Semester Not Known.');
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Admission Not Known.');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Program Not Known.');
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId']) ? intval($_POST['deptId']) : die('Department Not Known.');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Class Year Not Known.');
        $specId = isset($_POST['specId']) && !empty($_POST['specId']) ? intval($_POST['specId']) : NULL;


        $model = new \backend\models\CourseOfferingTemp();
        if ($model->checkIfOffered($acadYearId, $semId, $admId, $progId, $deptId, $clslvlId, $specId) > 0) {
            echo "<table class='table table-condensed table-striped table-hover table-bordered' style='font-size:9pt;' >";
            echo "<thead style=' background-color:#8D0;'>
    				<th style=' border:1px solid black;'>#</th>
    				<th style=' border:1px solid black;'>Course Code</th>
    				<th style=' border:1px solid black;'>Course Title</th>
    				<th style=' border:1px solid black;'>Credit Hr.</th>
    			</thead><tbody>
    		";
            $cocats = $model->getOfferedCourses($acadYearId, $semId, $admId, $progId, $deptId, $clslvlId, $specId);
            $count = 0;
            $crSum = 0;
            $btn = "";
            if ($cocats[2] == 0) {
                $btn = "<button class='btn btn-warning btn-xs' title='Cancel This Offering? ({$cocats[1]})' onclick='deleteOffering({$cocats[1]})'>Cancel</button>";
            }

            // var_dump($cocats[0]);die();

            foreach ($cocats[0] as $key) {
                $course = CourseCatalog::findOne($key);
                if ($course) {
                    $course = $course->cocatCourseRel;
                } else {
                    continue;
                }
                echo "<tr>
    				<td style='text-align:center; border:1px solid black;'>" . ++$count . "</td>
    				<td style=' border:1px solid black;'>{$course->course_code}</td>
    				<td style=' border:1px solid black;'>{$course->course_title}</td>
    				<td style=' border:1px solid black;'>{$course->course_crdthr}</td>
    			</tr>";
                $crSum += $course->course_crdthr;
            }
            echo "<tr style='font-weight:bold'><td colspan=3 style='text-align:center; border:1px solid black;'>Total Credit Hour</td><td style='text-align:center; border:1px solid black;'><u>{$crSum}</u></td></tr>";
            echo "<tr><td colspan=4 style='text-align:right; border:1px solid black;'>{$btn}</td></tr>";
            echo "<tbody></table>";
        } else {
            echo "<p class='bg-danger'>Course Not Offered Yet.</p>";
        }
        die();
    }

    // remove
    public function actionDeleteOffering()
    {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('Entry Not Known.');
        echo \backend\models\CourseOfferingTemp::findOne($id)->delete();
    }

    public function actionGetCatalogCourses()
    {

        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('Academic Year Not Known.');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('Semester Not Known.');
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Admission Not Known.');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Program Not Known.');
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId']) ? intval($_POST['deptId']) : die('Department Not Known.');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Class Year Not Known.');
        $specId = isset($_POST['specId']) && !empty($_POST['specId']) ? intval($_POST['specId']) : NULL;

        $extraCourse = isset($_POST['extraCourse']) && !empty($_POST['extraCourse']) ? intval($_POST['extraCourse']) : NULL;

        $offeredCocats = [];
        $model = new \backend\models\CourseOfferingTemp();
        if ($model->checkIfOffered($acadYearId, $semId, $admId, $progId, $deptId, $clslvlId, $specId) > 0) {
            $offeredCocats = $model->getOfferedCourses($acadYearId, $semId, $admId, $progId, $deptId, $clslvlId, $specId);
        }
        $cocats = CourseCatalog::find();

        if (isset($_POST['admId']) && !empty($_POST['admId'])) {
            $cocats = $cocats->Where(['cocat_admission_relId' => $_POST['admId'],]);
        }

        if (isset($_POST['progId']) && !empty($_POST['progId'])) {
            $cocats = $cocats->andWhere(['cocat_program_relId' => $_POST['progId'],]);
        }

        if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
            $cocats = $cocats->andWhere(['cocat_dept_relId' => $_POST['deptId'],]);
        }

        if (isset($_POST['specId']) && !empty($_POST['specId'])) {
            $cocats = $cocats->andWhere([
                'OR',
                ['cocat_spec_relId' => $_POST['specId']],
                ['cocat_spec_relId' => null]
            ]);
        }

        if (isset($_POST['semId']) && !empty($_POST['semId'])) {
            // $cocats = $cocats->andFilterWhere([ 'cocat_sem_relId'=>$_POST['semId'],]);
            $cocats = $cocats->andWhere(['OR', ['cocat_sem_relId' => $_POST['semId']], ['cocat_sem_relId' => NULL]]);
        }

        // if is case of extra course search.....
        if (!is_null($extraCourse)) {
            // get the course from similar seacrh but diffrent class Level,
            $cocats = $cocats->andWhere(['cocat_course_relId' => $extraCourse]);
        } else {

            if (isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])) {
                $cocats = $cocats->andWhere(['OR', ['cocat_clslvl_relId' => $_POST['clslvlId']], ['cocat_clslvl_relId' => NULL]]);
            }
        }

        // chech if the course is already offered,
        if (isset($offeredCocats[0])) {
            $cocats = $cocats->andWhere(['NOT IN', 'id', $offeredCocats[0]]);
        }

        // filter only those courses offerable
        $cocats = $cocats->andWhere('cocat_status = 1');

        // $cocats = $this->getCocat();
        $wreport = "";

        if (!empty($cocats)) {
            $cocats = $cocats->all();
            $crsum2 = 0;
            $wreport .= "<table class='table table-condensed table-bordered table-hover' style='font-size:8pt;'>";
            $wreport .= "<thead style='background-color:#8D0;'>
                <th>#</th>
                <th>Course Code</th>
                <th>Corse Title</th>
                <th>Crdt. Hr.</th>";
            if (isset($_POST['ext'])) {
                $wreport .= "<th>Check</th>";
            }
            if (Yii::$app->user->can('Admin')) {
                $wreport .= "<th>Option</th>";
            }
            $wreport .= "</thead><tbody>";
            $count = 0;
            foreach ($cocats as $cocat) {
                $c = $cocat->cocatCourseRel;
                $crsum2 += $c->course_crdthr;
                $wreport .= "<tr>
                    <td>" . ++$count . "</td>
                    <td>{$c->course_code}</td>
                    <td>{$c->course_title}</td>
                    <td style='font-weight:bold; text-align:center;'>{$c->course_crdthr}</td>
                    ";
                if (isset($_POST['ext'])) {
                    $wreport .= "<td style='text-align:center;'><input type='checkBox' onchange='addToList({$cocat->id},{$c->id},{$c->course_crdthr})'></td>";
                }
                if (Yii::$app->user->can('Admin')) {
                    $wreport .= "
                        <td style='text-align:center;'>
                            <button class='btn btn-warning btn-xs' onclick='deleteCatalog22({$cocat->id})' title='Delete this course catalog 2.'> X </button>
                        </td>";
                }
                $wreport .= "</tr>";
            }
            $maxCredit = $this->getMaxAllowedCreditHrPerSemester($admId, $progId, $semId, $clslvlId, $acadYearId);

            // $this->__collCrdt = $crsum2;
            // $this->__maxCrdt = $maxCredit;

            $wreport .= "<tr style='font-weight:bold; text-align:center;'>
                    <td colspan = 3>
                        Total Credit
                        <input type='hidden' data-maxCredit='{$maxCredit}' id='crdtCollected' value='{$crsum2}'/>
                    </td>
                    <td style='text-decoration:underline;'>{$crsum2}</td>
                </tr>";
            $wreport .= "</tbody></table>";
            return $wreport;
        } else {
            echo "<span style='font-style:italic;' class='bg-warning'>No result.</span>";
        }
    }

    protected function getMaxAllowedCreditHrPerSemesterAC($admId, $progId, $semId, $clslvlId, $acadYearId)
    {

        $ccc = \backend\models\SemesterRegistrationControlac::find()
            ->where([
                'src_acadYear_relId' => $acadYearId,
                'src_sem_relId' => $semId,
                'src_adm_relId' => $admId,
                'src_prog_relId' => $progId,
                'src_clslvl_relId' => $clslvlId,
            ])->one();
        return $ccc;
    }

    // Get allowed semester creditHr for the batch
    protected function getMaxAllowedCreditHrPerSemester($admId, $progId, $semId, $clslvlId, $acadYearId)
    {

        // $this->__constructor();
        // check if any AC decision exists for the batch =>semester_registration_controll
        $ccc = null;
        $ccc = $this->getMaxAllowedCreditHrPerSemesterAC($admId, $progId, $semId, $clslvlId, $acadYearId);
        if ($ccc) {
        } else {
            // If there is no any, follow the default.
            $ccc = \backend\models\SemesterRegistrationControld::find()
                ->where([
                    'src_adm_relId' => $admId,
                    'src_prog_relId' => $progId,
                ])
                ->andWhere([
                    'OR',
                    ['src_sem_relId' => $semId],
                    ['src_sem_relId' => NULL]
                ])
                ->andWhere([
                    'OR',
                    ['src_clslvl_relId' => $clslvlId],
                    ['src_clslvl_relId' => NULL]
                ])
                ->one();
        }
        return is_null($ccc) ? 0 : $ccc->src_max_crdthr;
    }
}
