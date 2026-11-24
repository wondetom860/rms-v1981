<?php

namespace frontend\controllers;

use Yii;
use backend\models\Student;
use backend\models\StudentGroupHistory;
use backend\models\StudentSearch;
use backend\models\Semester;
use backend\models\SchoolYear;
use backend\models\Department;
use backend\models\StudentProfileChange;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StudentController implements the CRUD actions for Student model.
 */
class StudentController extends Controller
{
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
                        'roles' => ['Admin', 'Registrar'],
                    ],
                    // [
                    //     'actions' => ['update'],
                    //     'allow' => false,
                    //     // Allow  admins to create, update, and delete, and view (index)
                    //     'roles' => ['Admin',],
                    // ],
                    [
                        'actions' => ['approve-admission', 'view', 'get-course-completion-report'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['UniversityRegistrar'],
                    ],
                    [
                        'actions' => ['index', 'view', 'get-course-completion-report'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['clerk'],
                    ],
                    [
                        'actions' => ['update-eth-group'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Student'],
                    ],
                    [
                        'actions' => ['get-dept-student-list', 'get-registration-history', 'get-course-completion-report'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['DepartmentHead',],
                    ],
                ]
            ]
        ];
    }

    public function actionSetAmName()
    {
        $id = Yii::$app->request->post('id');
        $name_am = Yii::$app->request->post('name_am');

        $model =  $this->findModel($id);

        $user = $model->stdUserRel;
        $fname_array = explode(' ', $name_am);

        if (count($fname_array) == 3) {
            $user->first_name_am = ucfirst(strtolower($fname_array[0]));
            $user->middle_name_am = ucfirst(strtolower($fname_array[1]));
            $user->last_name_am = ucfirst(strtolower($fname_array[2]));
        } else if (count($fname_array) == 2) {
            $user->first_name_am = ucfirst(strtolower($fname_array[0]));
            $user->last_name_am = ucfirst(strtolower($fname_array[1]));
        } else {
            return "Invalid input, Please enter full name  separated with space. 
                [A B C] => first name, father name, grandfather name:,
                [A B] => first name, grandfather name
            ";
        }

        if ($user->save(false)) {
            Yii::$app->session->setFlash('success', 'Student Name in Amharic set successfully');
            return $this->redirect(['/student/view', 'id' => $model->id]);
        } else {
            return "Setting Amharic Name failed, Error: " . json_encode($user->errors);
        }
    }

    public function actionRequestProfileChange($stdId, $attribute, $relationName = null, $relatedAttributeName = null)
    {
        $newModel = new StudentProfileChange();
        $newModel->std_relId = $stdId;
        $newModel->std_attribute = $attribute;
        $attributeValues = [];
        $newModel->relation_name = $relationName;
        $newModel->related_attribute_name = $relatedAttributeName;
        $newModel->attribute_label = $newModel->stdRel->getAttributeLabel($attribute);
        if ($relationName) {
            // ->select('id', $relatedAttributeName)
            $attributeValues = $newModel->stdRel->$relationName::find()->all();
            $newModel->old_value = $newModel->stdRel->$relationName->$relatedAttributeName;
        } else {
            $newModel->old_value = $newModel->stdRel->$attribute;
        }

        // var_dump($relatedAttributeName);exit;


        $requestedbefore = StudentProfileChange::find()
            ->where([
                'std_relId' => $stdId,
                'std_attribute' => $attribute,
                'approval_status' => NULL
            ])
            ->one();
        return $requestedbefore ?
            $this->renderAjax('/student-profile-change/view', ['model' => $requestedbefore, 'attributeValues' => $attributeValues, 'attributeName' => $relatedAttributeName]) :
            $this->renderAjax('/student-profile-change/create', ['model' => $newModel, 'attributeValues' => $attributeValues, 'attributeName' => $relatedAttributeName]);
    }

    public function actionPrintTemporaryCertificate()
    {
        $studentId = isset($_GET['studentId']) && !empty($_GET['studentId']) ? intval($_GET['studentId']) : die('InvalId Request');
        $model = $this->findModel($studentId);
        $gsetId = isset($_GET['gsetId']) && !empty($_GET['gsetId']) ? intval($_GET['gsetId']) : die('InvalId Request');
        $gset = \backend\models\WGroupSetup::findOne($gsetId);

        $this->layout = "certPrintLayout";
        return $this->render('__report/temporary-certificate', ['modelStudent' => $model, 'gsetModel' => $gset, 'printing' => 1]);
    }

    public function actionUpdateEthGroup()
    {
        $stdId = isset($_POST['stdId']) && !empty($_POST['stdId']) ? intval($_POST['stdId']) : die('Invalid request');
        $ethgroup = isset($_POST['ethgroup']) && !empty($_POST['ethgroup']) ? intval($_POST['ethgroup']) : die('Invalid request');

        $studentModel = $this->findModel($stdId);
        $user = $studentModel->stdUserRel;
        $user->eth_group_relId = $ethgroup;

        return $user->update(false);
    }

    public function actionManageAdmission()
    {
        return $this->render('manage-admission', ['models' => new Student()]);
    }

    public function actionGetCourseCompletionReport()
    {
        $stdId = isset($_GET['stdId']) && !empty($_GET['stdId']) ? intval($_GET['stdId']) : die('InvalId Request');
        $model = $this->findModel($stdId);

        $this->layout = "printLayout";
        return $this->render('_tab_stu_cocomp', ['modelStudent' => $model, 'printing' => 1]);
    }

    public function actionAdmissionForm($formId, $validFor)
    {
        die('Application Form!');
    }

    public function actionApproveAdmission()
    {
        // get list of students' admissions that are not approved yet.
        $model = new Student();
        return $this->render('approve-admission', ['models' => $model->getNewAdmittedStudents()]);
    }

    public function actionGetRegistrationHistory()
    {
        $stdId = isset($_POST['stdId']) && !empty($_POST['stdId']) ? intval($_POST['stdId']) : die('InvalId Request');

        $model = $this->findModel($stdId);

        return $this->renderPartial('__report/semregs', ['model' => $model, 'semregs' => $model->semesterRegistrations]);
    }

    /**
     * Lists all Student models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StudentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->user->can('Admin') || Yii::$app->user->can('Registrar') || Yii::$app->user->can('clerk')) {
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    public function actionPrintLoginCreden()
    {
        $searchModel = new StudentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 1);
        $_SESSION['report'] = Yii::t('rms', 'Students Login Credentials');
        $this->layout = 'printLayout';

        return $this->render('print_login_cred', ['models' => $dataProvider]);
    }

    public function actionGetDeptStudentList()
    {
        if (Yii::$app->user->can('DepartmentHead')) { //check studentAffairsHead
            $instDept = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);

            $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : NULL;
            $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : NULL;
            $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : NULL;
            $specId = isset($_POST['specId']) && !empty($_POST['specId']) ? intval($_POST['specId']) : NULL;
            $deptId = $instDept->inst_dep_relId;

            if ($deptId != $_POST['deptId']) {
                die("Please select students from your department.");
            }
            $searchModel = new StudentSearch();
            $dataProvider = $searchModel->searchW($admId, $progId, $deptId, $clslvlId, $specId);

            if ($dataProvider && count($dataProvider) > 0) {
                $table = "<table class='table table-striped table-condensed table-hover table-sm' style='font-size:9pt;'><tbody>";
                $count = 0;
                foreach ($dataProvider as $Skey) {
                    // $user = User1::findOne($key->std_user_relId);
                    $cGset = \common\components\CommonControllerFunctions::currentStudentGroup($Skey);
                    if ($cGset) {
                        if ($cGset->gs_clslvl_relId != $clslvlId) {
                            continue;
                        }
                    }
                    $btnnn = NULL;
                    if (isset($_POST['t'])) {
                        $btnnn = "<button class='btn btn-xs btn-primary pull-right' onclick='loadrequestForm({$Skey->stdUserRel->id})'>Load Form</button>";
                    }

                    $table .= "<tr>
                            <td>" . ++$count . "</td>
                            <td>{$Skey->std_regId}</td>
                            <td>{$Skey->stdUserRel->first_name} {$Skey->stdUserRel->middle_name}</td>
                            <td>{$btnnn}</td>
                        </tr>";
                }

                $table .= "</tbody></table>";
                // echo "<span style='font-style:italic' class='bg-success'>Found <b>{$count}</b> record(s).</span>";
                echo "{$table}";
                die();
            } else {
                die("<p class='bg-danger'>No Result found.</p>");
            }
        } else {
            die("<p class='bg-danger'>Un-privilleged user</p>");
        }
    }
    // $.post('/student/',
    //     {
    //         admId:admId,
    //         progId:progId,
    //         clslvlId:clslvlId,
    //         specId:spec,

    //         }).done(function(data){
    //           $('#ressult_container').html(data).trigger('change');
    //           // $('#printIcon').show();
    //     });
    /**
     * Displays a single Student model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->calculateGPAs($id);
        if (Yii::$app->request->isAjax) {
            return $this->renderPartial('view', [
                'model' => $model,
            ]);
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    protected function listMater()
    {
        if (isset($_POST['specId']) && $_POST['specId'] == 0) {
            $_POST['specId'] = NULL;
        }
        $stList = Student::find();
        if (isset($_POST['admId'])) {
            if (isset($_POST['progId'])) {
                if (isset($_POST['deptId'])) {
                    if (isset($_POST['clslvlId'])) {
                        if (isset($_POST['specId'])) {
                            $stList->andFilterWhere([
                                'std_admission_relId' => $_POST['admId'],
                                'std_prog_relId' => $_POST['progId'],
                                'std_dept_relId' => $_POST['deptId'],
                                'std_clslvl_relId' => $_POST['clslvlId'],
                                'std_spec_relId' => $_POST['specId'],
                            ]);
                        } else {
                            $stList->andFilterWhere([
                                'std_admission_relId' => $_POST['admId'],
                                'std_prog_relId' => $_POST['progId'],
                                'std_dept_relId' => $_POST['deptId'],
                                'std_clslvl_relId' => $_POST['clslvlId'],
                            ]);
                        }
                    } else {
                        $stList->andFilterWhere([
                            'std_admission_relId' => $_POST['admId'],
                            'std_prog_relId' => $_POST['progId'],
                            'std_dept_relId' => $_POST['deptId'],
                        ]);
                    }
                } else {
                    $stList->andFilterWhere([
                        'std_admission_relId' => $_POST['admId'],
                        'std_prog_relId' => $_POST['progId'],
                    ]);
                }
            } else {
                $stList->andFilterWhere([
                    'std_admission_relId' => $_POST['admId'],
                ]);
            }
        } else {
            $stList = null;
        }

        //exclude those alredy grouped in the session:: #acadYear, $semester.
        return $stList;
    }

    // returns the list of students for grading, calculating 
    public function actionGetlist()
    {
        if ($stdList = $this->listMater()) {
            $semId = Semester::findOne(['semester_name' => Yii::$app->params['C']['S']])->id;
            $acadYearId = SchoolYear::findOne(['sch_year_desc' => Yii::$app->params['C']['Y']])->id;
            $stdList = $stdList->all();

            if ($stdList) {
                $stdCount = 0;
                echo "<table class='table table-hover table-sm table-bordered table-condensed'>";
                echo "<caption style='background-color:#ace;'>Total Students found.</caption>";
                echo "<thead>
                        <th>Reg. ID</th>
                        <th>Full Name</th>
                        <th>Department</th>
                        <th>Option</th>
                        </thead><tbody>";
                foreach ($stdList as $std) {
                    // $user = User::findOne(['id' => $std['std_user_relId']]);

                    echo "<tr>
                        <td>{$std->std_regId}</td>
                        <td>{$std->getFullName()}</td>
                        <td>{$std->stdDeptRel->dept_name}</td>
                        <td>
                        <button class='btn btn-success btn-sm' onclick='generate(" . $std['id'] . ")'>Generate</button>
                        </td>
                    </tr>";
                    $stdCount++;
                }
                echo "</tbody></table>";
            } else {
                echo "No student found for your selection.";
                die();
            }
        } else {
            echo "No Student found.";
        }
    }

    // Get list of studets for grouping...
    public function actionStudentlist()
    {
        if ($stdList = $this->listMater()) {
            $semId = Semester::findOne(['semester_name' => Yii::$app->params['C']['S']])->id;
            $acadYearId = SchoolYear::findOne(['sch_year_desc' => Yii::$app->params['C']['Y']])->id;

            if ($stdList->count() > 0) {
                $students = $stdList->all();

                echo "<table class='table table-hover table-sm table-bordered'>";
                echo "<thead>
                    <th>Select</th>
                    <th>Reg. ID</th>
                    <th>Full Nmae</th>
                    <th>Class Year</th>
                    <th>Department</th>
                    </thead><tbody>";
                $cccc = 0;
                foreach ($students as $student) {
                    $rgd = StudentGroupHistory::find()->where([
                        'sgh_stud_relId' => $student->id,
                        'sgh_semester_relId' => $semId,
                        'sgh_acadYear_relId' => $acadYearId,
                    ])->all();
                    if (!empty($rgd)) {
                        continue;
                    }
                    $cccc++;
                    $us = $student->stdUserRel;
                    echo "<tr>
                            <td><input type='checkbox' onchange='addStdGrp(" . $student->id . ")'></td>
                            <td>" . $student->std_regId . "</td>
                            <td>" . $us->first_name . ' ' . $us->middle_name . "</td>
                            <td>" . \backend\models\ClassLevel::findOne(['id' => $student->std_clslvl_relId])->clslvl_name . "</td>
                            <td>" . Department::findOne(['id' => $student->std_dept_relId])->dept_name . "</td>
                        </tr>";
                }
                if ($cccc == 0) {
                    echo "
                            <tr><td colspan=5>
                            <p class='bg-danger' style='font-style:italic'>No ungrouped student found for your filter in this semester.</p>
                            </td></tr>
                            ";
                }

                echo "</tbody></table>";
            } else {
                echo "<p class='bg-denger'>No student List found, please correct you filter option.</p>";
            }
        } else {
            echo "Waiting for filter..";
        }
    }
    /**
     * Creates a new Student model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Student();
        $model->std_user_relId = $id;
        $userModel = \backend\models\User::findOne(['id' => $id]);
        $success = 0;

        if ($model->load(Yii::$app->request->post())) {
            // Conversions
            $model->std_group_realId = isset($model->std_group_realId) ? $model->std_group_realId : NULL;
            $model->std_semester_status = isset($model->std_semester_status) ? $model->std_semester_status : '-';

            $model->std_spec_relId = $model->std_spec_relId == 0 ? NULL : $model->std_spec_relId;

            $model->std_created_at = date('Y-m-d h:i:s');
            $model->std_updated_at = date('Y-m-d h:i:s');
            // $model->std_updated_by = $model->std_created_by = Yii::$app->user->identity->id;

            if ($model->save()) {
                return $this->redirect(['index']);
            } else {
                print_r($model->errors);
                die();
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'userModel' => $userModel,
            ]);
        }
    }

    // auto generate student registration ID . EG. RDEG/312/06
    public function actionGetregid()
    {
        $regId = '';

        $admission = Yii::$app->request->post('admId');
        $program = Yii::$app->request->post('progId');
        if (!(isset($admission) && !empty($admission) && isset($program) && !empty($program))) {
            # code...
        } else {

            //$adId[0]['admission_desc']
            $adId = \backend\models\Admission::findOne($admission);
            $progId = \backend\models\Program::findOne($program); //
            $scyr = SchoolYear::find()->where(['sch_year_desc' => Yii::$app->params['C']['Y']])->all()[0];

            // echo json_encode(['AdmId'=>$adId['admission_desc'],'ProgId'=>$progId,'schoolYears'=>$scyr]);
            // die();

            $count = Student::find()->where([
                'std_acadYear_relId' => $scyr['id'],
                'std_admission_relId' => $adId['id'],
                'std_prog_relId' => $progId['id'],
            ])->count();

            $count++;
            $regId .= strtoupper($adId['admission_name']) == 'REGULAR' ? 'R' : 'E';
            $regId .= strtoupper($progId['program_name']) == 'DEGREE' ? 'DEG' : (strtoupper($progId['program_name']) == 'MASTERS' ? 'PG' : 'PhD');
            $regId .= '/' . sprintf("%04d", $count) . '/' . substr(date('Y') - 7, -2); //

            echo $regId;
        }
    }

    // private function getAutoIncrement($tableName){
    //     $query = new Query();
    //     $res = $q->select('AUTO_INCREMENT')
    //         ->from('INFORMATION_SCHEMA.TABLES')
    //         ->where('TABLE.SCHEMA = rms AND TABLE_NAME = "{$tableName}" ')
    //         ->one();
    //     if ($res) {
    //         return $res['AUTO_INCREMENT'];
    //     }
    //     return false;
    // }

    /**
     * Updates an existing Student model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        // if (!Yii::$app->user->can('Registrar')) {
        //     throw new ForbiddenHttpException(Yii::t('rms', 'You are NOT allowed to perform this operation'));
        // }
        $model = $this->findModel($id);
        $modelUser = $model->stdUserRel;
        if ($model->load(Yii::$app->request->post())) {
            $model->std_updated_by = Yii::$app->user->identity->id;
            $model->std_updated_at = date("Y-m-d H:i:s");
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Student record updated successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Error updating student record., Error: ' . json_encode($model->errors));
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'userModel' => $modelUser,
            ]);
        }
    }

    /**
     * Deletes an existing Student model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (Yii::$app->user->can('Admin')) {
            $this->findModel($id)->delete();

            return $this->redirect(['index']);
        } else {
            throw new NotFoundHttpException('Invalid operation for such user!.');
        }
    }

    /**
     * Finds the Student model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Student the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Student::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
