<?php

namespace common\models;

use yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model {

    public $id;
    public $username;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $gender;
    public $nationality;
    public $dob;
    public $date_of_join;
    public $user_type;
    public $userRole; //userRole

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'first_name', 'last_name', 'email', 'nationality','userRole'], 'required'],
            [['gender'], 'string'],
            [['dob'], 'safe'],
            [['first_name', 'nationality','userRole'], 'string', 'max' => 100],
            [['user_type'], 'string', 'max' => 20],
            ['username', 'trim'],
            [['username'], 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup() {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->first_name = $this->first_name;
        $user->email = $this->email;
        $user->last_name = $this->last_name;
        $user->dob = $this->dob;
        $user->date_of_join = date('Y-m-d');
        $user->nationality = $this->nationality;
        $user->gender = $this->gender;
        $user->user_type = $this->user_type;
        // $user->status = $this->status;               
        $user->setPassword($this->password);
        $user->generateAuthKey();
        //asign default role
        $auth = Yii::$app->authManager;
        $student = $auth->getRole(isset($this->userRole)?$this->userRole:'Student'); //Get selected user role

        // return var_dump($user);
        // die();
        if ($user->save()) {
            $userId = $user->id;
            $this->id = $user->id;
            $auth->assign($student, $userId);
            return $user;
        }
         else{
            return null;
        }

        // return $user->save() ? $user : null;
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'username' => Yii::t('rms', 'User_name'),
            'first_name' => Yii::t('rms', 'First Name'),
            'last_name' => Yii::t('rms', 'Last Name'),
            'gender' => Yii::t('rms', 'Gender'),
            'user_type' => Yii::t('rms', 'User Type'),
            'auth_key' => Yii::t('rms', 'Auth Key'),
            'password_hash' => Yii::t('rms', 'Password Hash'),
            'password_reset_token' => Yii::t('rms', 'Password Reset Token'),
            'email' => Yii::t('rms', 'Email'),
            'status' => Yii::t('rms', 'Status'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'nationality' => Yii::t('rms', 'Nationality'),
            'dob' => Yii::t('rms', 'Dob'),
            'date_of_join' => Yii::t('rms', 'Date Of Join'),
        ];
    }
}
