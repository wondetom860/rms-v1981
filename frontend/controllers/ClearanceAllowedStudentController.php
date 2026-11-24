<?php

namespace frontend\controllers;

use Yii;
use backend\models\ClearanceAllowedStudent;
use backend\models\ClearanceAllowedStudentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\SemesterRegistration2;

/**
 * ClearanceAllowedStudentController implements the CRUD actions for ClearanceAllowedStudent model.
 */
class ClearanceAllowedStudentController extends Controller
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
                       'actions' => ['index','create', 'view', 'update', 'delete','get-student-list-for-adding','get-clearance-form'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin','clerk'],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all ClearanceAllowedStudent models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClearanceAllowedStudentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * This is the model class for table "clearance_allowed_student".
     *
     * @property int $id
     * @property string $cas_full_name
     * @property string $cas_regId
     * @property string $cas_acadYear
     * @property string $cas_semester
     * @property string $cas_admission
     * @property string $cas_program
     * @property string $cas_department
     * @property string $cas_clslvl
     * @property string $cas_reason//
     * @property string|null $cas_spec
     * @property string $created_at
     * @property string $created_by
     * @property string|null $updated_by
     * @property string|null $updated_at
    */

    public function actionGetClearanceForm(){//get-clearance-form
        $semregId = isset($_POST['semregId']) && !empty($_POST['semregId']) ? intval($_POST['semregId']) : die('INVALID_REQUEST');

        $modelSemreg = SemesterRegistration2::findOne($semregId);
        if ($modelSemreg) {
            $model = new ClearanceAllowedStudent();

            if ($model->requestedForThis($modelSemreg)) {
                return "ALREADY REQUESTED CLEARANCE!";
            }

            $user = $modelSemreg->semregStdRel->stdUserRel;
            $model->cas_full_name = $user->first_name . ' '. $user->middle_name.' '.$user->last_name;

            $model->cas_acadYear = $modelSemreg->semregAcadYearRel->sch_year_desc;
            $model->cas_regId = $modelSemreg->semregStdRel->std_regId;
            $model->cas_semester = $modelSemreg->semregSemesterRel->semester_name;
            $model->cas_admission = $modelSemreg->semregStdRel->stdAdmissionRel->admission_name;
            $model->cas_program = $modelSemreg->semregStdRel->stdProgRel->program_name;
            $model->cas_clslvl = $modelSemreg->semregClslvlRel->clslvl_num;


            $model->cas_gender = $user->gender;
            $model->cas_nationality = $user->nationality?$user->nationalityRel->name : ' - ';
            $model->cas_dob = date_format(date_create($user->dob),"Y-m-d");


            $model->cas_department = $modelSemreg->semregStdRel->stdDeptRel->dept_name;
            $model->cas_spec =  isset($modelSemreg->semregStdRel->std_spec_relId) ? $modelSemreg->semregStdRel->stdSpecRel->spec_name : '';



            return $this->renderPartial('_form2',['model' => $model]);


        }
        return "NO SUCH ENTRY!";
    }

    public function actionGetStudentListForAdding(){//get-student-list-for-adding
        $requested = NULL;
        // ->where(['OR',
        //             ['gstd_approved_by' => NULL],['gstd_approved_at' => NULL]
        //         ])
        if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {
            $model = SemesterRegistration2::find()
                ->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')
                ->innerJoin('user','user.id = student.std_user_relId')
                ->innerJoin('school_year','school_year.id = semester_registration_2.semreg_acadYear_relId')
                ->innerJoin('semester','semester.id = semester_registration_2.semreg_semester_relId')
                ->innerJoin('class_level','class_level.id = semester_registration_2.semreg_clslvl_relId')
                ->innerJoin('admission','admission.id = student.std_admission_relId')
                ->innerJoin('program','program.id = student.std_prog_relId')
                ->innerJoin('department','department.id = student.std_dept_relId')
                ->orderBy('user.first_name');

            if (isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])) {
                $model = $model->andWhere(['school_year.sch_year_desc' => $_POST['acadYearId']]);
            }

            if (isset($_POST['semId']) && !empty($_POST['semId'])) {
                $model = $model->andWhere(['semester.semester_name' => $_POST['semId']]);
            }

            if (isset($_POST['clslvl']) && !empty($_POST['clslvl'])) {
                $model = $model->andWhere(['class_level.clslvl_num' => $_POST['clslvl']]);
            }

            if (isset($_POST['admId']) && !empty($_POST['admId'])) {
                $model = $model->andWhere(['admission.admission_name' => $_POST['admId']]);
            }

            if (isset($_POST['progId']) && !empty($_POST['progId'])) {
                $model = $model->andWhere(['program.program_name' => $_POST['progId']]);
            }

            if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
                $model = $model->andWhere(['department.dept_name' => $_POST['deptId']]);
            }

            if (isset($_POST['specId']) && !empty($_POST['specId'])) {
                $model = $model
                    ->innerJoin('specialization','specialization.id = student.std_spec_relId')
                    ->andWhere(['specialization.spec_name' => $_POST['specId']]);
            }

            $model = $model->all();
            return $this->renderPartial('includes/__std_list',['model' => $model]);
        }else{
            return "UN_PRIEVELLEGED_ACCESS!";
        }
    }

    /**
     * Displays a single ClearanceAllowedStudent model.
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

    /**
     * Creates a new ClearanceAllowedStudent model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ClearanceAllowedStudent();

        if ($model->load(Yii::$app->request->post())) {

            $model->created_by = Yii::$app->user->identity->username;
            $model->created_at = date("Y-m-d H:i:s");

            if ($model->save()) {
                return $this->redirect(['index']);
            }

            var_dump($model->getErrors());die();
            // $model->save()
            
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ClearanceAllowedStudent model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->updated_by = Yii::$app->user->identity->username;
            $model->updated_at = date("Y-m-d H:i:s");
            if ($model->save()) {
                return $this->redirect(['index']);
            }

            var_dump($model->getErrors());die();
            
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ClearanceAllowedStudent model.
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
     * Finds the ClearanceAllowedStudent model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ClearanceAllowedStudent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClearanceAllowedStudent::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
