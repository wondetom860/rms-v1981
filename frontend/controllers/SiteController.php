<?php

namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    // no need of csrf_validation
    // public $validateCsrfToken = false;
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup', 'dashboard', 'change-username', 'changeusername', 'chenge-password','changepassword','reset-password-w'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'dashboard', 'change-username', 'changeusername', 'chenge-password','changepassword','reset-password-w'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        // if (!Yii::$app->user->isGuest) {
        //        if (Yii::$app->user->can('Instructor')) {
        //             $model = \backend\models\Instructor::findOne(['inst_user_relId' => \Yii::$app->user->id]);
        //             return $this->redirect('_pages/index-instructor', ['model' => $model])->send();
        //        }else if (Yii::$app->user->can('Student')) {
        //             $model = \backend\models\Student::findOne(['inst_user_relId' => \Yii::$app->user->id,'std_semester_status' => 'ACTIVE']);
        //             return $this->redirect('_pages/index-student', ['model' => $model])->send();
        //        }
        //    }   
        return $this->render('dashboard');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $user = Yii::$app->user->identity;
            $this->log('IN', $user);
            return $this->goBack();
        } else {
            $model->password = '';
            if (Yii::$app->request->isAjax) {
                return $this->redirect(['login', 'model' => $model])->send();
            }
            return $this->render('login', [
                'model' => $model,
            ]);
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
        // die();
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        $user = Yii::$app->user->identity;
        $this->log('OUT', $user);
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }


    public function actionResetPasswordW()//reset-password-w
    {

        if (Yii::$app->user->can('Admin')) {
            return $this->render('changePasswordW');
        }

        throw new BadRequestHttpException("InValidAccessException");
    }


    /**
     * Change User password.
     *
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionChangepassword($userId = null)
    {
        $id = is_null($userId) ? Yii::$app->user->id : intval($userId);

        try {
            $model = new \common\models\ChangePasswordForm($id);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if (is_bool($model->changePassword())) {
                $model = new \common\models\ChangePasswordForm($id);
                Yii::$app->session->setFlash('danger', 'Password Changed!, Please login again with your new password');
                return $this->redirect(['index'])->send();
            } else {
                Yii::$app->session->setFlash('warning', 'Error with input. Old password is NOT correct!');
            }
        }

        return $this->render('changePassword', [
            'model' => $model,
        ]);
    }


    public function actionDashboard()
    {
        return $this->render('dashboard2');
    }


    /**
     * Change User name.
     *
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionChangeusername()
    {
        $id = \Yii::$app->user->id;

        try {
            $model = new \common\models\ChangeUsernameForm($id);
        } catch (InvalidArgumentException $e) {
            throw new \yii\web\BadRequestHttpException($e->getMessage());
        }

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $sss = $model->changeUsername();
            if (is_array($sss)) {
                \Yii::$app->session->setFlash('warning', 'ERROR: ' . $sss[1]);
            } else if (is_bool($sss) && $sss) {
                \Yii::$app->session->setFlash('success', 'User Name Changed!, Please login with your new username');
                \Yii::$app->user->logout();
            }
        }

        return $this->render('changeUsername', [
            'model' => new \common\models\ChangeUsernameForm($id),
        ]);
    }
}
