<?php

namespace frontend\modules\AcademicCalendar\controllers;

use Yii;
use frontend\modules\AcademicCalendar\models\AcActivePrograms;
use frontend\modules\AcademicCalendar\models\AcActiveProgramsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AcActiveProgramsController implements the CRUD actions for AcActivePrograms model.
 */
class AcActiveProgramsController extends Controller
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
                        'roles' => ['Admin'],
                    ],
                    [
                        'actions' => ['index', 'change-status', 'get-report-print-backlog', 'get-active-programs', 'change-gc-status', 'change-intern-status', 'change-entry-type'],
                        'allow' => true,
                        'roles' => ['Registrar'],
                    ],
                    [
                        'actions' => ['index', 'view', 'offer-courses', 'get-catalog-courses', 'get-course-offered', 'get-offering-detail', 'get-enrollments'],
                        'allow' => true,
                        'roles' => ['DepartmentHead'],
                    ],
                ]
            ]
        ];
    }

    public function actionGraduateStudentList2()
    {
        $acpId = Yii::$app->request->get('acpId');

        $model = $this->findModel($acpId);

        $result = $model->getGraduateStudentList2();
        $this->layout = '@frontend/views/layouts/printLayout';
        if ($result) {
            return $this->render('__report/graduate-student-list2', ['list' => $result]);
        }

        return;
    }

    public function actionChangeEntryType()
    {
        $acpId = Yii::$app->request->post('acpId');
        $model = $this->findModel($acpId);
        $model->entry_type = $model->entry_type == 1 ? 2 : 1; //toggle 1 to 2 &vv
        $model->updated_at = time();
        $model->updated_by = Yii::$app->user->id;
        $model->cascadeToGsets();
        $model->getData();

        if ($model->update()) {
            return 1;
        } else {
            return json_encode($model->errors);
        }
    }

    public function actionChangeInternStatus()
    {
        $acpId = Yii::$app->request->post('acpId');
        $model = $this->findModel($acpId);
        $model->on_internship = $model->on_internship == 1 ? 0 : 1; //toggle 1 to 0 &vv
        $model->updated_at = time();
        $model->updated_by = Yii::$app->user->id;
        $model->getData();

        if ($model->update()) {
            return 1;
        } else {
            return json_encode($model->errors);
        }
    }

    public function actionChangeGcStatus()
    {
        $acpId = Yii::$app->request->post('acpId');
        $model = $this->findModel($acpId);
        $model->graduate_class = $model->graduate_class == 1 ? 0 : 1; //toggle 1 to 0 &vv
        $model->updated_at = time();
        $model->updated_by = Yii::$app->user->id;
        $model->getData();

        if ($model->update()) {
            return 1;
        } else {
            return json_encode($model->errors);
        }
    }
    public function actionOfferCourses()
    {
        $activeProgId = isset($_POST['acpId']) && !empty($_POST['acpId']) ? intval($_POST['acpId']) : die('INVALID_REQUEST');
        $catIds = isset($_POST['catIds']) && !empty($_POST['catIds']) ? $_POST['catIds'] : die('INVALID_REQUEST');

        $modelActiveProgram = $this->findModel($activeProgId);

        // Hod shoud offer for only his departments:
        $instructor = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
        if ($instructor && $instructor->inst_dep_relId == $modelActiveProgram->dept_relId) {
            if (($modelActiveProgram->status != $modelActiveProgram::STATUS_CLOSED) || ($modelActiveProgram->entry_type == $modelActiveProgram::ENTRY_TYPE_TRANSFER) || ($modelActiveProgram->admissionRel->admission_name != 'REGULAR')) {
                return $modelActiveProgram->offerCourse($catIds);
            } else {
                return "Course offering to in-active academic programs is not possible";
            }
        } else {
            return "Offer courses for your department selections only.";
        }
        // return "Offering...";
    }

    public function actionGetCatalogCourses()
    {
        $activeProgId = isset($_POST['activeProgId']) && !empty($_POST['activeProgId']) ? intval($_POST['activeProgId']) : die('INVALID_REQUEST');
        $modelActiveProgram = $this->findModel($activeProgId);

        // Hod shoud offer for only his departments:
        $instructor = \backend\models\Instructor::findOne(['inst_user_relId' => \Yii::$app->user->id]);
        if ($instructor && $instructor->inst_dep_relId == $modelActiveProgram->dept_relId) {
            if (($modelActiveProgram->status != $modelActiveProgram::STATUS_CLOSED) || ($modelActiveProgram->entry_type == $modelActiveProgram::ENTRY_TYPE_TRANSFER) || ($modelActiveProgram->admissionRel->admission_name != 'REGULAR')) {
                return $this->renderPartial('__report/offer-offer-new', ['cocatModels' => $modelActiveProgram->getCatalogCourses(), 'modelActiveProgram' => $modelActiveProgram]);
            } else {
                return "Course offering to in-active academic programs is not possible";
            }
        } else {
            return "Offer courses for your department selections only.";
        }
    }

    public function actionGetEnrollments()
    {
        $coff2DetId = isset($_POST['coff2DetId']) && !empty($_POST['coff2DetId']) ? intval($_POST['coff2DetId']) : die('INVALID_REQUEST');

        $coff2DetModel = \backend\models\CourseOffering2Detail::findOne($coff2DetId);

        $enrollments = $coff2DetModel->coff2detCoff2Rel
            ->getSemesterRegistration2Details($coff2DetModel->coff2det_course_relId)
            ->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
            ->innerJoin('student', 'student.id=semester_registration_2.semreg_std_relId')
            ->innerJoin('user', 'user.id=student.std_user_relId')
            ->orderBy(['trim(first_name)' => SORT_ASC, 'middle_name' => SORT_ASC])
            ->all();

        $_SESSION['coff2detId'] = $coff2DetId;

        return $this->renderPartial('@frontend/views/course-offering2-detail/__report/enrollments', ['enrollments' => $enrollments, 'model' => $coff2DetModel]);
    }

    public function actionGetOfferingDetail()
    {
        $coff2Id = isset($_POST['coff2Id']) && !empty($_POST['coff2Id']) ? intval($_POST['coff2Id']) : die('INVALID_REQUEST');

        $coff2Model = \backend\models\CourseOffering2::findOne($coff2Id);

        if ($coff2Model && count([$coff2Model]) > 0) {
            return $this->renderPartial('__report/course-offering-detail', ['coff2Model' => $coff2Model]);
        }
    }

    public function actionGetCourseOffered()
    {
        $activeProgId = isset($_POST['activeProgId']) && !empty($_POST['activeProgId']) ? intval($_POST['activeProgId']) : die('INVALID_REQUEST');

        $model = $this->findModel($activeProgId);

        return $this->renderPartial('__report/course-offerings', ['model' => $model]);
    }

    public function actionGetStudents()
    {
        $gsetId = isset($_GET['gsetId']) && !empty($_GET['gsetId']) ? intval($_GET['gsetId']) : die('Invalid request');
        $filterMode = isset($_GET['filterMode']) && !empty($_GET['filterMode']) ? intval($_GET['filterMode']) : 0;
        $renderPart = isset($_GET['renderPart']) && !empty($_GET['renderPart']) ? intval($_GET['renderPart']) : 0;
        $loadOnlyUngrouped = isset($_GET['loadOnlyUngrouped']) && !empty($_GET['loadOnlyUngrouped']) ? intval($_GET['loadOnlyUngrouped']) : 0;

        $gsetModel = \backend\models\WGroupSetup::findOne($gsetId);
        $activeAcademicYear = null;
        // get Academic status from $gset.
        $ac = Yii::$app->AcademicCalendar->getActiveProgramModels(null);
        if ($ac && count($ac) > 0) {
            $activeAcademicYear = $ac[0];
        }

        // No need to check whether the academic year is active/not.
        // $academicProgram = $gsetModel->gsActiveProgRel;

        // if ($academicProgram->status == $academicProgram::STATUS_CLOSED) {
        //     // code...
        //     return "<p class='bg-warning'>You can not assign studnts to inactive/closed academic program section.</p>";
        // }

        if ($filterMode == 0) { //load all students case
            //'std_dept_relId' => $gsetModel->gs_dept_relId,
            //'std_prog_relId' => $gsetModel->gs_prog_relId,
            $students = \backend\models\Student::find()->where([
                'std_admission_relId' => $gsetModel->gs_adm_relId,
                'std_semester_status' => ['APPROVED', 'ACTIVE', 'PROBATION 1', 'PROBATION 2'],
            ]);
        } else if ($filterMode == 2) { //new entry students:

            if ($gsetModel->gsClslvlRel->clslvl_num == 1 && $gsetModel->gsSemRel->semester_name == 'I') {
            } else {
                return "<p class='bg-warning'>Invalid selection, Please select fresh year student section</p>";
            }

            if (is_null($activeAcademicYear)) {
                return "<p class='bg-warning'>No active Academic Calendar set.</p>";
            }

            $students = \backend\models\Student::find()
                ->where([
                    'std_dept_relId' => $gsetModel->gs_dept_relId,
                    'std_admission_relId' => $gsetModel->gs_adm_relId,
                    'std_prog_relId' => $gsetModel->gs_prog_relId,
                    'std_acadYear_relId' => $activeAcademicYear->schoolYearRel->id,
                    'std_sem_relId' => $activeAcademicYear->semesterRel->id,
                    'std_semester_status' => ['APPROVED', 'ACTIVE'],
                ]);
            if ($loadOnlyUngrouped) {
                $students = $students->andWhere(['std_group_realId' => NULL]);
            }
        } else { //previously grouped students
            if ($gsetModel->gsClslvlRel->clslvl_num == 1 && $gsetModel->gsSemRel->semester_name == 'I') {
                return "<p class='bg-warning'>Invalid selection, there is NO previous history for fresh year student</p>";
            }

            $modelActiveProgram = $gsetModel->gsActiveProgRel;
            $students = $modelActiveProgram->getStudents($modelActiveProgram, $gsetModel, $filterMode, $loadOnlyUngrouped, $activeAcademicYear);

            if (is_string($students)) {
                return "<p class='bg-danger'>Error: {$students}</p>";
            }
        }
        if (is_null($students)) {
            return "<p class='bg-danger'>Error: Student List Empty</p>";
        }
        $students = $students
            ->innerJoin('user', 'user.id=student.std_user_relId')
            ->orderBy([
                'std_acadYear_relId' => SORT_DESC,
                'std_sem_relId' => SORT_DESC,
                'std_prog_relId' => SORT_DESC,
                'std_clslvl_relId' => SORT_DESC,
                'std_dept_relId' => SORT_ASC,
                'first_name' => SORT_ASC,
                'middle_name' => SORT_ASC,
            ])
            ->all();
        return $this->renderPartial($renderPart ? '__report/__table' : '__report/group-student', ['gsetModel' => $gsetModel, 'filterMode' => $filterMode, 'students' => $students, 'loadOnlyUngrouped' => $loadOnlyUngrouped]);
    }

    public function actionAddSection()
    {
        $activeProgId = isset($_POST['activeProgId']) && !empty($_POST['activeProgId']) ? intval($_POST['activeProgId']) : die('INVALID_REQUEST');

        $modelActiveProgram = $this->findModel($activeProgId);
        $modelGset = new \backend\models\WGroupSetup();

        $modelGset->gs_active_prog_relId = $modelActiveProgram->id;
        $modelGset->gs_acadYear_relId = $modelActiveProgram->academicYearRel->schoolYearRel->id;
        $modelGset->gs_sem_relId = $modelActiveProgram->academicYearRel->semesterRel->id;
        $modelGset->gs_clslvl_relId = $modelActiveProgram->class_level_relId;
        $modelGset->gs_adm_relId = $modelActiveProgram->admission_relId;
        $modelGset->gs_prog_relId = $modelActiveProgram->program_relId;
        $modelGset->gs_dept_relId = $modelActiveProgram->dept_relId;
        $modelGset->gs_spec_relId = $modelActiveProgram->spec_relId;


        return $this->renderAjax('../../../../views/w-group-setup/create', ['model' => $modelGset]);
    }

    public function actionGetActivePrograms()
    {
        $ayId = isset($_POST['ayId']) && !empty($_POST['ayId']) ? intval($_POST['ayId']) : die('INVALID_REQUEST');

        $model = AcActivePrograms::find()->where(['academic_year_relId' => $ayId])->orderBy(['program_relId' => SORT_ASC, 'class_level_relId' => SORT_ASC, 'dept_relId' => SORT_ASC])->all();

        return $this->renderPartial('__report/index', ['records' => $model]);
    }

    /**
     * Lists all AcActivePrograms models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AcActiveProgramsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AcActivePrograms model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        // auto register all academic actions for this entry:

        $model = $this->findModel($id);

        if (Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
            if ($inst && count([$inst]) > 0) {
                if ($model->dept_relId == $inst->inst_dep_relId) {
                    return $this->render('view-hod', [
                        'model' => $model,
                    ]);
                }
            }

            unset($model);

            Yii::$app->session->setFlash('type', 'danger');
            Yii::$app->session->setFlash('message', 'Un-authorized access');

            return $this->redirect('index');
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new AcActivePrograms model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($syid)
    {
        $model = new AcActivePrograms();
        $model->academic_year_relId = intval($syid);
        $model->status = 1;
        $records = $model->find()->where(['academic_year_relId' => $syid])->orderBy(['dept_relId' => SORT_ASC, 'class_level_relId' => SORT_ASC, 'program_relId' => SORT_ASC,])->all();

        if ($model->load(Yii::$app->request->post())) {

            // check if duplicate entry exists:

            if ($model->checkIfExists()) {
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'Such entry already exists.');

                return $this->redirect([
                    'create',
                    'model' => $model,
                    'syid' => $model->academic_year_relId,
                    'ayId' => $syid,
                ]);
            }

            $model->created_by = Yii::$app->user->identity->id;
            $model->created_at = time();

            $model->getData();
            if ($model->save()) {
                $model = new AcActivePrograms();
                $model->academic_year_relId = intval($syid);
                $model->status = 1;
                $records = $model->find()->where(['academic_year_relId' => $syid])->orderBy(['dept_relId' => SORT_ASC, 'class_level_relId' => SORT_ASC, 'program_relId' => SORT_ASC,])->all();
                return $this->render('create', [
                    'model' => $model,
                    'records' => $records,
                ]);
            } else {
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'Error while storing record.' . json_encode($model->getErrors()));

                return $this->render(
                    'create',
                    [
                        'model' => $model,
                        'syid' => $model->academic_year_relId,
                        'ayId' => $model->academic_year_relId
                    ]
                );
            }
            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'records' => $records,
            'ayId' => $model->academic_year_relId,
        ]);
    }

    /**
     * Updates an existing AcActivePrograms model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!$model->program_schedule_used && $model->status == $model::STATUS_ACTIVE) {
            // 
        } else {
            Yii::$app->session->setFlash('type', 'error');
            Yii::$app->session->setFlash('message', 'You can NOT update closed/completed/referenced program record.');
            return $this->redirect('index');
        }
        $records = $model->find()->where(['academic_year_relId' => $model->academic_year_relId])->orderBy(['dept_relId' => SORT_ASC, 'class_level_relId' => SORT_ASC, 'program_relId' => SORT_ASC,])->all();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->checkIfExists($model->id)) {
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'Such entry already exists.');

                return $this->redirect([
                    'update',
                    'model' => $model,
                    'syid' => $model->academic_year_relId,
                    'id' => $model->id
                ]);
            }

            $model->updated_by = Yii::$app->user->identity->id;
            $model->updated_at = time();

            $model->getData();
            if ($model->save()) {
                unset($model);
                return $this->redirect('index');
            } else {
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'Error while updating record.' . json_encode($model->getErrors()));

                return $this->render(
                    'update',
                    [
                        'model' => $model,
                        'syid' => $model->academic_year_relId,
                        'id' => $model->id
                    ]
                );
            }
        }

        return $this->render('update', [
            'model' => $model,
            'records' => $records,
        ]);
    }

    /**
     * Deletes an existing AcActivePrograms model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$model->program_schedule_used && $model->status == $model::STATUS_ACTIVE) {
            $model->delete();
        } else {
            Yii::$app->session->setFlash('type', 'error');
            Yii::$app->session->setFlash('message', 'You can NOT delete this entry, Program has been recorded with reference.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the AcActivePrograms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AcActivePrograms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AcActivePrograms::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
