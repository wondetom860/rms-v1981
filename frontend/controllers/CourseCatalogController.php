<?php

namespace frontend\controllers;

use Yii;
use backend\models\CourseCatalog;
use backend\models\Course;
use backend\models\Department;
use backend\models\CourseOffering;
use backend\models\CourseCatalogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\components\CommonControllerFunctions as CCF;

/**
 * CourseCatalogController implements the CRUD actions for CourseCatalog model.
 */
class CourseCatalogController extends Controller
{

    public $acadYearId;
    public $semId;

    public $__maxCrdt = 0;
    public $__collCrdt = 0;

    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
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
                        'roles' => ['Admin', 'Registrar'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@',],
                    ],
                ]
            ]
        ];
    }

    public function actionSetCategory($id)
    {
        // $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->min_pass_grade_relId == null) {
                $model->min_pass_grade_relId = $model->courseCategoryRel->pass_grade_relId;
            }
            $model->cocat_updated_by = Yii::$app->user->identity->id;
            $model->cocat_updated_at = date('Y-m-d');
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return json_encode($model->errors);
            }
        }
        return $this->renderAjax('__reports/update-category' , ['model' => $model]);
    }
    public function actionSetPassGrade($id)
    {
        // $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $model->cocat_updated_by = Yii::$app->user->identity->id;
            $model->cocat_updated_at = date('Y-m-d');

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return json_encode($model->errors);
            }
        }
        return $this->renderAjax('__reports/update-category' , ['model' => $model]);
    }

    public function actionGetCatalogForm()
    {
        $model = new CourseCatalog();
        $dept_Id = Yii::$app->request->post('deptId');
        $model->cocat_dept_relId = $dept_Id;
        if ($model->load(Yii::$app->request->post())) {

            $model->cocat_created_by = $model->cocat_updated_by = Yii::$app->user->identity->id;
            $model->cocat_created_at = $model->cocat_updated_at = time();
            $model->collectedCredit = 0;


            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Course Catalog added successfully');
            } else {
                Yii::$app->session->setFlash('error', 'Course Catalog not added successfully,Error: ' . json_encode($model->errors));
            }
            return $this->redirect(['/department/view', 'id' => $model->cocat_dept_relId]);
        }
        return $this->renderAjax('_form2', [
            'model' => $model,
        ]);
    }

    public function actionChangeStatus()
    {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('INVALID_REQUEST');
        if (Yii::$app->user->can('Registrar')) {
            $model = $this->findModel($id);
            $model->cocat_status = $model->cocat_status ? 0 : 1;
            return $model->update();
        } else {
            return "Operation is allowed for Registrar only";
        }
    }

    /**
     * Lists all CourseCatalog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CourseCatalogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CourseCatalog model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    protected function getMaxAllowedCreditHrPerSemesterAC($admId, $progId, $sem, $clslvlId)
    {

        $ccc = \backend\models\SemesterRegistrationControlac::find()
            ->where([
                'src_acadYear_relId' => $this->acadYearId,
                'src_sem_relId' => $this->semId,
                'src_adm_relId' => $admId,
                'src_prog_relId' => $progId,
                'src_clslvl_relId' => $clslvlId,
            ])->one();
        return $ccc;
    }

    // Get allowed semester creditHr for the batch
    protected function getMaxAllowedCreditHrPerSemester($admId, $progId, $sem, $clslvlId)
    {

        $this->__constructor();
        // check if any AC decision exists for the batch =>semester_registration_controll
        $ccc = null;
        $ccc = $this->getMaxAllowedCreditHrPerSemesterAC($admId, $progId, $sem, $clslvlId);
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
                    ['src_sem_relId' => $sem],
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

    // common get file for cocat list gfiltering
    protected function getCocat()
    {
        $cocats = CourseCatalog::find();

        if (isset($_POST['admId']) && !empty($_POST['admId'])) {
            $cocats = $cocats->Where(['cocat_admission_relId' => $_POST['admId'],]);
        }

        if (isset($_POST['progId']) && !empty($_POST['progId'])) {
            $cocats = $cocats->andWhere(['cocat_program_relId' => $_POST['progId'],]);
        }

        if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
            $cocats = $cocats->andWhere(['OR', ['cocat_dept_relId' => $_POST['deptId']], ['cocat_dept_relId' => NULL]]);
        }

        if (isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])) {
            $cocats = $cocats->andWhere(['OR', ['cocat_clslvl_relId' => $_POST['clslvlId']], ['cocat_clslvl_relId' => NULL]]);
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

        return $cocats;
    }

    // returns the cocat history matching the parameters if any for offering.
    public function actionCocathistory2()
    {
        $cocats = $this->getCocat();
        $totalCourses = $cocats->count();

        //select('id,course_title, course_code,course_crdthr, course_dept_relId')
        // add filter if the courses are already offered before the selection.
        $coffCourseIds = \backend\models\CourseOffering::find()->select('coff_course_relId')->where([
            'coff_acadYear_relId' => \backend\models\SchoolYear::findOne(['sch_year_desc' => Yii::$app->params['C']['Y']])->id,
            'coff_created_by' => Yii::$app->user->identity->id,
        ])->indexBy('coff_course_relId')->all();
        //$cocats->andFilterWhere(['NOT IN','cocat_course_relId',$coffCourseIds]);

        $remCourses = $cocats->count();
        if (!empty($cocats) && $cocats->count() > 0) {
            $cocats = $cocats->all();
            $count = 0;
            $credSum = 0;
            echo "<table class='table table-hover table-sm table-bordered'>";
            echo "<thead style='background-color:#ace;'>
                <th>#</th>
                <th>Course Detail</th>
                <th>CrdtHr.</th>
                <th>From</th>
                <th>Select</th>
                </thead>
                <tbody>";
            // echo "<table class='table table-condensed table-bordered table-hover'>";
            foreach ($cocats as $cocat) {
                $c = Course::findOne(['id' => $cocat['cocat_course_relId']]);
                $dept = Department::findOne(['id' => $c->course_dept_relId]);
                $ciddd = CourseOffering::findOne(['coff_course_relId' => $cocat['cocat_course_relId']]);
                //$exists = !empty($ciddd);
                echo "<tr>
                    <td>" . ++$count . "</td>
                    <td>" . $c->course_code . ' => ' . $c->course_title . "</td>
                    <td style='text-align:center;'>" . $c->course_crdthr . "</td>
                    <td>" . $dept->dept_name . "</td>
                    <td style='text-align:center;'>";
                if (empty($ciddd)) { //Course not offere
                    echo "<input type='checkbox' onchange='addCocatOffer(" . $c->id . "," . $cocat->id . ")'>";
                } else {
                    echo "Already offered.";
                    $remCourses--;
                }

                echo "</td>
                    </tr>";

                $credSum += $c->course_crdthr;
                // $credtSum = $credtSum + $c->course_crdthr;
            }
            echo "<tr style='text-align:center; font-weight: bold;'>
                    <td colspan=2>Total CrdtHr</td>
                    <td>" . $credSum . "</td>
                    <td colspan=2><i><span id='selcount'>0</span> of " . $count . "</i> Course(s) selected</td>
                </tr>";
            echo "<tr class='info' style='font-style:italic;'><td colspan =3>Total courses to offer in this semester: " . $totalCourses . "</td>
                <td>Offered: " . ($totalCourses - $remCourses) . "</td>
                <td>Remaining: " .  $remCourses . "</td>
                </tr>";
            echo "</table>";
        } else {
            echo "<span style='color:#ace; font-style:italic;'>Total Courses for this Semester: " . $totalCourses . ".<br>Offered Courses: " . ($totalCourses - $remCourses) . ".<br>Remaining Course(s): " . $remCourses . "</span>";
        }
    }

    public function actionCreatePreqBatch(){
        $catalogId = Yii::$app->request->post('catalogId');
        $majorCourses = Yii::$app->request->post('majorCourses');
        $model = $this->findModel($catalogId);
        return $model->savePreqBatch($majorCourses);
    }

    public function actionGetAllMajorCourses(){//
        $catalogId = Yii::$app->request->get('id');
        $model = $this->findModel($catalogId);

        return $this->renderPartial('__reports/_all-major-courses',['majorCourses' => $model->getAllProgramMajorCourses(),'model' => $model]);
    }

    // returns the cocat history matching the parameters if any for catalog setting.
    public function actionCocathistory()
    {

        // echo "<pre>";
        // var_dump($_POST['admId'], $_POST['progId'],$_POST['semId'],$_POST['clslvlId']);
        // die();

        $cocats = $this->getCocat();

        if (!empty($cocats) && $cocats->count() > 0) {
            $cocats = $cocats->all();
            $crsum2 = 0;
            $wonderep = "";
            $wonderep .= "<table class='table table-condensed table-bordered table-hover' style='font-size:8pt;'>";
            $wonderep .= "<thead style='background-color:#8D0;'>
                <th>#</th>
                <th>Course Code</th>
                <th>Corse Title</th>
                <th>Crdt. Hr.</th>";
            if (isset($_POST['ext'])) {
                $wonderep .= "<th>Check</th>";
            }
            if (Yii::$app->user->can('Admin')) {
                $wonderep .= "<th>Option</th>";
            }
            $wonderep .= "
            </thead>
            <tbody>";
            $count = 0;
            foreach ($cocats as $cocat) {
                $c = Course::findOne(['id' => $cocat['cocat_course_relId']]);
                $crsum2 += $c->course_crdthr;
                $__offerable = $cocat->cocat_status ? 'Course elligible for offering' : 'In-active course';
                $wonderep .= "<tr title='{$__offerable}'>
                    <td>" . ++$count . "</td>
                    <td>{$c->course_code}</td>
                    <td>{$c->course_title}</td>
                    <td style='font-weight:bold; text-align:center;'>{$c->course_crdthr}</td>
                    ";
                if (isset($_POST['ext'])) {
                    $wonderep .= "<td style='text-align:center;'><input type='checkBox' onchange='addToList({$cocat->id},{$c->id},{$c->course_crdthr})'></td>";
                }
                if (Yii::$app->user->can('Admin')) {
                    // check id the course is already offered before deletin
                    $__Btn = "<button class='btn btn-warning btn-xs' onclick='deleteCatalog22({$cocat->id})' title='Delete this course catalog 2.'> X </button>";
                    if ($cocat->checkIfOffered()) {
                        $__Btn = "";
                    }
                    $wonderep .= "
                        <td style='text-align:center;'>
                            {$__Btn}
                        </td>";
                }
                $wonderep .= "
                    </tr>";
            }
            $maxCredit = $this->getMaxAllowedCreditHrPerSemester($_POST['admId'], $_POST['progId'], $_POST['semId'], $_POST['clslvlId']);

            $this->__collCrdt = $crsum2;
            $this->__maxCrdt = $maxCredit;

            $wonderep .= "<tr style='font-weight:bold; text-align:center;'>
                    <td colspan = 3>
                        Total Credit
                        <input type='hidden' data-maxCredit='{$maxCredit}' id='crdtCollected' value='{$crsum2}'/>
                    </td>
                    <td style='text-decoration:underline;'>{$crsum2}</td>
                </tr>";
            $wonderep .= "</tbody></table>";
            return $wonderep;
        } else {
            echo "<span style='color:#ace; font-style:italic;'>No History found for your selection.</span>";
        }
    }

    public function actionDeleteCatalog()
    {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('Invalid Entry!');

        $model = $this->findModel($id);

        if ($model->checkIfOffered()) {
            return "<p class='bg-error'>Deleting Failed, Course is offered for the group/section</p>";
        }
        return CourseCatalog::findOne($id)->delete();
    }
    protected function checkExists($model)
    {
        $cocatss = CourseCatalog::find()->where([
            'cocat_admission_relId' => $model->cocat_admission_relId,
            'cocat_program_relId' => $model->cocat_program_relId,
            'cocat_dept_relId' => $model->cocat_dept_relId,
            'cocat_course_relId' => $model->cocat_course_relId,
        ]);
        if (isset($model->cocat_clslvl_relId)) {
            $cocatss = $cocatss->andWhere(['OR', ['cocat_clslvl_relId' => $model->cocat_clslvl_relId], ['cocat_clslvl_relId' => NULL]]);
        }
        if (isset($model->cocat_spec_relId)) {
            $cocatss->andWhere(['OR', ['cocat_spec_relId' => $model->cocat_spec_relId], ['cocat_spec_relId' => null]]);
        }

        return $cocatss;
    }

    /**
     * Creates a new CourseCatalog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CourseCatalog();

        if ($model->load(Yii::$app->request->post())) {

            // conversion for spec-common courses:
            $model->cocat_spec_relId = $model->cocat_spec_relId == 0 ? null : $model->cocat_spec_relId;
            //look if similar record exists:
            $cocatss = $this->checkExists($model);
            if (!empty($cocatss) && $cocatss->count() > 0) {
                echo "<span class='bg-danger'>Such record is already in the catalog.</span>";
                return 0;
            }

            $er = 0;

            $model->collectedCredit = 0;
            $pr = intval($model->cocat_courseProject);
            $mr = intval($model->cocat_courseMajor);
            $el = intval($model->cocat_courseelectives);
            $model->cocat_courseProject = ($pr == 0 || $pr == 1) ? $pr : die('Course Project?');
            $model->cocat_courseMajor = ($mr == 0 || $mr == 1) ? $mr : die('Course Major');
            $model->cocat_courseelectives = ($el == 0 || $el == 1) ? $el : die('Course Elective?');
            $model->cocat_created_by = Yii::$app->user->identity->id;
            $model->cocat_created_at = date('Y-m-d');
            $model->cocat_updated_by = Yii::$app->user->identity->id;
            $model->cocat_updated_at = date('Y-m-d');

            // echo "<pre>";
            // var_dump($model);
            // die();


            if ($model->save()) {
                echo 1;
            } else {
                print_r($model->errors);
                die();
            }
        } else {
            $model->maxCredit = $this->__maxCrdt;
            $model->collectedCredit = $this->__collCrdt;
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CourseCatalog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CourseCatalog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->checkIfOffered()) {
            Yii::$app->session->setFlash('type', 'error');
            Yii::$app->session->setFlash('message', 'Offered course can NOT be deleted, you can change active status.');
        } else {
            if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {
                $model->delete();
                Yii::$app->session->setFlash('type', 'success');
                Yii::$app->session->setFlash('message', 'Catalog entry deleted.');
            } else {
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'This operation is allowed to Registrar office only.');
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the CourseCatalog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CourseCatalog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CourseCatalog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
