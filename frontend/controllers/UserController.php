<?php

namespace frontend\controllers;

use backend\models\Logins;
use Yii;
use backend\models\User;
use common\models\User1;
use backend\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\ErrorException;
use common\models\LoginForm;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
                'rules' => [//
                    [
                        'actions' => ['index', 'create', 'view', 'update-phone', 'update-email', 'update', 'delete', 'create2', 'get-user-profile-info', 'reset-password-wonde', 'show-otp', 'manage-account', 'alter-user-account-activation-status', 'get-user-exists','show-password-reset-log','show-login-summary'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Admin'],
                    ],
                    [
                        'actions' => ['create', 'update-phone', 'update-email'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['DepartmentHead'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'view', 'change-account-status'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['UniversityRegistrar'],
                    ],
                    [
                        'actions' => ['signup', 'create2', 'login', 'get-account-status'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ]
            ]
        ];
    }

    public function actionManageAccount()
    {
        if (Yii::$app->user->can('Admin')) {
            return $this->renderAjax('includes/__usersList', ['empId' => isset($_GET['empId']) ? $_GET['empId'] : null]);
        }
    }

    public function actionChangeAccountStatus()
    {

        $userId = isset($_POST['userId']) && !empty($_POST['userId']) ? intval($_POST['userId']) : die('Uknown user ID');
        $option = isset($_POST['option']) && !empty($_POST['option']) ? intval($_POST['option']) : die('Uknown Status change');

        $model = $this->findModel($userId);
        // to approve, student must have at least one un-approved admission request
        $model->getPassword();
        $model->username = $model->getUsername();
        $model->status = $option;
        if ($option == $model::STATUS_ACTIVE) {
            $admissionRequest = $model->getStudents()
                ->andFilterWhere(['std_semester_status' => ['NOT APPROVED', 'ACTIVE']])
                ->all();
            if (is_null($admissionRequest) || count($admissionRequest) > 1) {
                return "unable to approve request!, Duplicate admission request exists...";
            }
            $admissionRequest[0]->std_semester_status = "APPROVED";
            $admissionRequest[0]->update(false);
            $model->activated = 1;
            $model->activated_at = time();
            $model->activated_by = Yii::$app->user->id;
        } else {
            $admissionRequest = $model->getStudents()->andFilterWhere(['std_group_realId' => NULL])->all();
            if (is_null($admissionRequest) || count($admissionRequest) > 1 || count($admissionRequest) == 0) {
                return "unable to approve request!, Such account is not approved...";
            }
            $model->activated = 0;
            $model->username = md5($model->username);
            $admissionRequest[0]->std_semester_status = "NOT APPROVED";
            $admissionRequest[0]->update(false);
        }

        if ($model->update(false)) {
            return 1;
        }

        return json_encode($model->getErrors());

        // if ($model->students) {//student has no non-approved admission request:
        //     return "Student admitted with this account.";
        // }else{

        // }

    }

    public function actionSignup()
    {
        $model = new User();
        return $this->renderAjax('registerEmploy-mini', ['model' => $model]);
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    private function checIfExists($model)
    {
        return User::find()
            ->where([
                'first_name' => $model->first_name,
                'middle_name' => $model->middle_name,
                'last_name' => $model->last_name,
            ])
            ->count() > 0;
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    public function actionGetUserExists()
    {
        $key = isset($_POST['key']) && !empty($_POST['key']) ? (string)$_POST['key'] : die('No input');

        $user = User::find()
            ->where(['like', 'first_name', $key])
            ->orWhere(['like', 'middle_name', $key])
            ->orWhere(['like', 'last_name', $key])
            ->all();
        if ($user && count($user) > 0) {
            foreach ($user as $key) {
                echo "<pre>{$key->first_name} {$key->middle_name} {$key->last_name} </pre>";
            }
        } else {
            echo "Search NOT found for {$key}";
        }
    }


    public function actionShowLoginSummary()
    {
        $logins = new Logins();
        return $this->renderPartial('includes/_login-summary',['report' => $logins->top10LoginSummary()]);
    }


    public function actionResetPasswordWonde()
    {
        // get user Id
        $userId = isset($_POST['userId']) && !empty($_POST['userId']) ? intval($_POST['userId']) : die("<p class='bg-danger'>Invalid Parameter provided!</p>");

        // reset user password
        $userModel = new User1();
        $us = $this->findModel($userId);
        $dPassword = $userModel->getPassword();
        $dPassworHash = Yii::$app->security->generatePasswordHash($dPassword);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $result = Yii::$app->db->createCommand()
                ->update('user', ['password_hash' => $dPassworHash,'last_password_changed_date' => null], ['id' => $userId])
                ->execute();
            if ($result) {
                // create log.
                $ip = $_SERVER["REMOTE_ADDR"];
                $admin = User1::findOne(Yii::$app->user->identity->id);
                $his = strtoupper($us->gender) == 'FEMALE' ? 'her' : 'his';
                $message = "User account password reset succes, 
                    {$us->first_name} {$us->middle_name[0]} [User Name : {$us->username}] can sign in {$his} account with default password. Machine IP from: {$ip}
                    Operation performed by {$admin->first_name} (<i>with admin Role</i>) on  <span class='bg-warning'> " . date('r') . " </span>";
                $this->logToPasswordReset($message, $admin, $us, $his);
                $transaction->commit();
                return $message;
            }
        } catch (ErrorException $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }
    }



    public function actionShowOtp()
    {
        $userId = isset($_POST['userId']) && !empty($_POST['userId']) ? intval($_POST['userId']) : die("<p class='bg-danger'>Invalid Parameter provided!</p>");

        $modelUser = User1::findOne($userId);

        return "<p class='bg-warning'>
                    Full Name : {$modelUser->first_name} {$modelUser->middle_name} {$modelUser->last_name}<br>
                    OTP : {$modelUser->OTP}
                </p>";
    }
    public function actionShowPasswordResetLog()
    {
        return nl2br(file_get_contents('uploads/passwordResetLog.txt'));
    }
    private function logToPasswordReset($message, $admin, $us, $his)
    {
        $ip = $_SERVER["REMOTE_ADDR"];
        $file = "uploads/passwordResetLog.txt";
        $message = "User account password reset succes, {$us->first_name} {$us->middle_name[0]} [User Name : {$us->username}] can sign in {$his} account with default password. Operation performed by {$admin->first_name} {$admin->middle_name} on" . date('r') . " By Machine IP : {$ip}";
        $handler = fopen($file, 'a');
        fwrite($handler, "\n");
        fwrite($handler, $message);
        fclose($handler);
    }

    public function actionUpdatePhone()
    {
        $userId = isset($_POST['userId']) && !empty($_POST['userId']) ? intval($_POST['userId']) : die("<p class='bg-danger'>Invalid Parameter provided!</p>");
        $phone = isset($_POST['phone']) && !empty($_POST['phone']) ? $_POST['phone'] : die("<p class='bg-danger'>Invalid Parameter provided!</p>");
        $model = $this->findModel($userId);
        $model->phone_mobile = $phone;
        if ($model->save(false)) {
            echo json_encode(['status' => 1, 'message' => 'Phone number updated successfully']);
        } else {
            echo json_encode($model->getErrors());
        }
    }

    public function actionUpdateEmail()
    {
        $userId = isset($_POST['userId']) && !empty($_POST['userId']) ? intval($_POST['userId']) : die("<p class='bg-danger'>Invalid Parameter provided!</p>");
        $email = isset($_POST['email']) && !empty($_POST['email']) ? $_POST['email'] : die("<p class='bg-danger'>Invalid Parameter provided!</p>");
        $model = $this->findModel($userId);
        $model->email = $email;
        if ($model->save(false)) {
            echo json_encode(['status' => 1, 'message' => 'User Email address updated successfully']);
        } else {
            echo json_encode($model->getErrors());
        }
    }

    public function actionGetUserProfileInfo()
    {
        $userId = isset($_POST['userId']) && !empty($_POST['userId']) ? intval($_POST['userId']) : die("<p class='bg-danger'>Invalid Parameter provided!</p>");
        return $this->renderPartial('user-profile', ['model' => $this->findModel($userId)]);
    }
    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // public function actionCreate()
    // {
    //     $model = new User();

    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         return $this->redirect(['view', 'id' => $model->id]);
    //     } else {
    //         return $this->render('create', [
    //             'model' => $model,
    //         ]);
    //     }
    // }


    //Role indicates what user type is gonna created, 
    // 1->INSTRUCTOR
    // 2->DEPARTMENT-HEAD
    // 3->STUDENT
    // 4->REGISRAR
    // 5->TECHNICAL ASSISTANT
    public function actionAlterUserAccountActivationStatus()
    { //ActivateUserAccount => activate-user-account
        $userId = isset($_POST['userId']) && !empty($_POST['userId']) ? intval($_POST['userId']) : die('INVALID_REQUEST');

        $userModel = $this->findModel($userId);

        $userModel->activated = (is_null($userModel->activated) || $userModel->activated == $userModel::INACTIVE_ACCOUNT) ? $userModel::ACTIVE_ACCOUNT : $userModel::INACTIVE_ACCOUNT; //alter to reverse status

        $userModel->status = $userModel::STATUS_INACTIVE ? $userModel::STATUS_ACTIVE : $userModel::STATUS_INACTIVE;

        if ($userModel->save(false)) {
            if ($userModel->getUserRole2($userModel->id) == '-' && $userModel->activated == $userModel::ACTIVE_ACCOUNT) {
                return $this->redirect(['/admin/assignment/view', 'id' => $userModel->id]);
            } else {
                // revok role
                // $roleAss
            }
        } else {
            Yii::$app->session->setFlash('type', 'error');
            Yii::$app->session->setFlash('message', json_encode($userModel->getErrors()));
        }
        return $this->redirect(['user/view', 'id' => $userModel->id]);
    }
    public function actionCreate($role)
    {
        if (!(Yii::$app->user->can('Admin') || Yii::$app->user->can('DepartmentHead'))) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $model = new User();
        $model->userRole = $role;
        if (empty(Yii::$app->authManager->getRole($model->userRole))) {
            echo "Invalid Role selected. operation halted";
            die();
        }

        if ($model->load(Yii::$app->request->post())) {
            //do something befor saving the data, getUsername , ----

            $model->first_name = ucfirst(strtolower(trim($model->first_name)));
            $model->middle_name = ucfirst(strtolower(trim($model->middle_name)));
            $model->last_name = ucfirst(strtolower(trim($model->last_name)));

            $transaction = Yii::$app->db->beginTransaction();
            $model->username = md5($this->getUsername());
            $model->created_at = date('Y-m-d');
            // $model->created_at = new \yii\db\Expression('NOW()');
            $model->created_by = Yii::$app->user->identity->id;
            $model->updated_by = Yii::$app->user->identity->id;
            $model->updated_at = $model->created_at;

            $model->activated = NULL;
            $model->status = 2;

            $model->OTP = $model->getOTP();

            if ($this->checIfExists($model)) {
                echo "<pre>";
                echo "Such User Information is already Exists, Please contact registrar if this is mistake!";
                die();
            }

            $model->setPassword($this->getPassword());
            $model->generateAuthKey();
            $auth = Yii::$app->authManager;
            $role = $auth->getRole($model->userRole);
            if ($model->save()) { //we can use switch here,
                $userId = $model->id;
                $auth->assign($role, $userId);
                $transaction->commit();
                if (strtoupper($role->name) == strtoupper('student')) {
                    //creating user account for student
                    return $this->redirect([Yii::$app->urlManager->createUrl('student/create'), 'id' => $userId]);
                } else if (strtoupper($role->name) == strtoupper('instructor') || strtoupper($role->name) == strtoupper('Technical Assistant')) {
                    // create account for instructor or tecnical assistnt
                    return $this->redirect([Yii::$app->urlManager->createUrl('instructor/create'), 'id' => $userId]);
                } else if (strtoupper($role->name) == strtoupper('departmentHead')) {
                    //creating user account for dept.head.
                    return $this->redirect([Yii::$app->urlManager->createUrl('department-head/create'), 'id' => $userId]);
                }
                //die();
            } else {

                $transaction->rollBack();
                var_dump($model->errors);
                die();
            }
            //return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    // \Yii::$app->authManager->getRolesByUser($user_id);  to get roles of the user by its ID

    // \Yii::$app->authManager-> getAssignment($role,$rule_id)

    public function actionCreate2()
    {
        $model = new User();
        if ($model->load(Yii::$app->request->post())) {

            $model->first_name = ucfirst(strtolower(trim($model->first_name)));
            $model->middle_name = ucfirst(strtolower(trim($model->middle_name)));
            $model->last_name = ucfirst(strtolower(trim($model->last_name)));

            $transaction = Yii::$app->db->beginTransaction();
            $model->username = md5($this->getUsername());
            //$model->date_of_join = date('Y-m-d');
            $model->created_at = (string)date('Y-m-d h:i:s');
            $model->created_by = Yii::$app->user->isGuest ? null : Yii::$app->user->identity->id;
            $model->updated_by = $model->created_by;
            $model->updated_at = $model->created_at;
            $otpp = $model->getOTP();
            $model->OTP = $otpp;

            $model->activated = NULL;
            $model->status = 2;

            $model->setPassword($this->getPassword());
            $model->generateAuthKey();
            $auth = Yii::$app->authManager;
            $role = $auth->getRole($model->userRole);

            if ($this->checIfExists($model)) {
                echo "<pre>";
                echo "Such User Information is already Exists, Please contact registrar if this is mistake!";
                die();
            }

            if ($model->save()) {
                $userId = $model->id;
                $auth->assign($role, $userId);

                Yii::$app->session->setFlash(
                    'message',
                    "
                    <p>Account created successfully, Please follow up your account activation status by providing OTP below. </p>
                    <p class='bg-info'><b>Note: </b> This OTP is used till you get valid user name and password.</p>
                    <pre>OTP: {$otpp}</pre>"
                );

                $transaction->commit();
                $model = null;
                return $this->render('accountStatus', ['model' => new User()]);
                // echo json_encode([1,$model->OTP]);
                // return;
            } else {
                $transaction->rollBack();
                var_dump($model->getErrors());
                die();
            }
        } else {
            return $this->render('registerEmploy', ['model' => $model]);
        }
    }

    private function log($type, $user)
    {
        $action = $type == 'IN' ? 'Logged In to' : 'Signed Out from';
        // $user = $this->user;
        $date = date('d-m-Y');
        $time = date("h:i:s");
        $full_name = "{$user->first_name} {$user->middle_name} {$user->last_name}";
        $msg = "\n[{$type}]: [{$full_name}] with user id=[{$user->id}] has successfully [{$action}] the system on [{$date}] @ [{$time}] from machine with IP address: " . $_SERVER['REMOTE_ADDR'];
        $log_file = fopen('uploads/login-log.wt', 'a');
        fwrite($log_file, $msg);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            //return $this->goHome();
            return $this->redirect(['/site/dashboard']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {

            if ($model->login()) {
                $this->log("IN", Yii::$app->user->identity);
                return $this->redirect(['/site/'])->send();
            }

            if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                // return ActiveForm::validate($model);
                return $this->redirect(['/site/login'])->send();
            }
        } else {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('login', [
                    'model' => $model,
                ]);
                // return $this->redirect('login', [
                //     'model' => $model,
                // ]);
            }
        }
    }

    public function actionValidateUserLogin()
    {
        $model = new LoginForm();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionGetAccountStatus()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            // var_dump();die();
            if (isset($_POST['User']['search1']) && !empty($_POST['User']['search1'])) {
                $user = User::find()->where(['OTP' => $_POST['User']['search1']])->one();

                if ($user) {
                    if ($user->activated == 1 && $user->status == 10) {
                        echo "<div class='alert alert-success'> 
                                <p>Account approved and activated. Please use the following login credentials to login to system.</p>
                                <pre>
            User Name : <span style='color: white; background-color: #333;'>{$user->username}</span><br>
            Password : <button style='color: white; background-color: #333;' title='Put here to see your default passwrod' onmouseover=\"$('#id__').show();\" onmouseleave = \"$('#id__').hide();\">here</button>
            <span id='id__' style='display:none; color: white; background-color: #333;'>wt_p0000</span>
                                </pre>
                                <p class='alert-warning'>These login credentials are for onetime use, and you have to change it as you login for the first time. Your OTP is no more valid afterwards.</p>
                            </div>";
                    } else {
                        die("<span class='bg-warning'>Account NOT approved and activated yet. Please re-check it later.</span>");
                    }
                } else {
                    die("<span>Account with such OTP not found or your account has already been activated.</span>");
                }
            } else if (isset($_POST['User']['search2']) && !empty($_POST['User']['search2'])) {
                // activated account and approved account...
                // filter those accounts that are approved only
                $user = User::find()
                    ->where(['username' => $_POST['User']['search2']])
                    ->andWhere(['<>', 'status', 2])
                    ->one();
                if ($user) {
                    if ($user->activated == 1) {
                        // account activated and safe
                        echo "<span class='bg-success'>Account approved and activate. You can use it for login.</span>";
                        die();
                    } else if (is_null($user->activated)) {
                        // not approved yet.
                        echo "<span class='bg-warning'>Account not approved yet.</span>";
                        die();
                    } else {
                        // account de-activated. Please contact administrator
                        echo "<span class='bg-danger'>Your account has problem. Please contact administrator.</span>";
                        die();
                    }
                } else {
                    echo "<span>Account with such USERNAME not found.</span>";
                    die();
                }
            } else {
                echo "<span class='bg-danger'>No valid input detected.</span>";
                die();
            }
        } else {
            if (Yii::$app->request->isAjax) { //if (Yii::$app->request->isAjax) {
                return $this->renderAjax('accountStatus', ['model' => $model]);
            }
            echo "<span class='bg-danger'>No input detected.</span>";
            die();
        }
    }

    private function getUsername()
    {
        return \Yii::$app->params['college_accr'] . date('his') . rand(9, 100);
    }

    private function getPassword()
    {
        return 'wt_p0000';
    }

    private function getOTP()
    { //25
        // str_pad(input, 3);
        $rr = rand(99999, 100000000);
        $dd = date('Ymdhis');
        $OTP = '';
        $app = $rr . $dd;
        for ($i = 0; $i < 20; $i++) {
            $OTP .= $app[$i];
        }
        return strtoupper('S' . $OTP . 'WT');
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('Admin')) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $model = $this->findModel($id);

        if (Yii::$app->user->can('Admin')) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    return $this->redirect(['index']);
                } else {
                    var_dump($model->getErrors());
                    die();
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            throw new NotFoundHttpException("Error Processing Request", 1);
        }
    }

    /**
     * Deletes an existing User model.
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
            throw new NotFoundHttpException("Error Processing Request", 1);
        }
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
