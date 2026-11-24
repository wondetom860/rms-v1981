<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use \backend\models\Instructor;
use DateTime;
use yii\web\IdentityInterface;
use frontend\modules\AcademicCalendar\models\AcActivePrograms;
use mdm\admin\models\searchs\Assignment;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $updated_by
 * @property string $first_name
 * @property string $middle_name
 * @property string $gender
 * @property string $last_password_changed_date
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    const STATUS_UNAPPROVED = 2;

    const ACTIVE_ACCOUNT = 1;
    const INACTIVE_ACCOUNT = 2;

    public $fullName = null;
    public $ac_rank;
    public $mr_rank;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function defaultOrExpiredPassword(){
        $defaultPassword = Yii::$app->params['default_password'];
        // Yii::$app->security->validatePassword($defaultPassword, $this->password_hash)
        $isDefault = $this->last_password_changed_date == NULL;
        // var_dump($isDefault);exit;
        if ($isDefault) {//
            Yii::$app->session->setFlash('error', Yii::t('rms', Yii::$app->params['default_password_message']));
            return true;
        }

        // this case is handled by setting last_password_changed_date to NULL while resetting password.

        // if(Yii::$app->security->validatePassword($defaultPassword, $this->password_hash)){
        //     Yii::$app->session->setFlash('error', Yii::t('rms', Yii::$app->params['default_password_message']));
        //     return true;
        // }


        // check if password is expired.
        $expiredDays = Yii::$app->params['default_password_expired_days'];
        $expiredDaysWarning = Yii::$app->params['default_password_expired_days_warning'];
        $lastPasswordChange = $this->getLastPasswordChangeDate();
        if ($lastPasswordChange === NULL) {
            Yii::$app->session->setFlash('error', Yii::t('rms', Yii::$app->params['default_password_expired_message']));
            return true;
        }
        $lastPasswordChangeTime = strtotime($lastPasswordChange);
        $currentTime = time();
        $daysSinceLastChange = ($currentTime - $lastPasswordChangeTime) / (60 * 60 * 24);
        // var_dump($daysSinceLastChange,$expiredDays);exit;

        if ($daysSinceLastChange >= ($expiredDays - 1)) {
            // password expired
            Yii::$app->session->setFlash('error', Yii::t('rms', Yii::$app->params['default_password_expired_message']));
            return true;
        } elseif ($daysSinceLastChange >= $expiredDaysWarning) {
            // password will expire soon
            $days = $expiredDays - $daysSinceLastChange;
            $d = round($days,0); 
            // var_dump($daysSinceLastChange , $expiredDaysWarning,$d,$expiredDays);
            $date = date('dMY@h:i A', strtotime("+$d days"));
            Yii::$app->session->setFlash('warning', Yii::t('rms', Yii::$app->params['default_password_expired_days_warning_message'], ['days' => $date]));
            return false;
        }
        // if password is not default and not expired, return false.
        if (!$isDefault && $daysSinceLastChange < $expiredDays) {
            return false;
        }
        // if password is default or expired, return true.
        Yii::$app->session->setFlash('info', Yii::t('rms', Yii::$app->params['default_password_expired_redirect_message']));
        // Yii::$app->response->redirect(Yii::$app->params['default_password_expired_redirect']);
        // Yii::$app->response->send();
        return false;
    }

    public function getLastPasswordChangeDate(){
        $lastPasswordChange = $this->last_password_changed_date;
        if ($lastPasswordChange === NULL) {
            return null;
        }
        return date('Y-m-d H:i:s', $lastPasswordChange);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $this->getFullName();
        return [
            TimestampBehavior::class,
        ];
    }

    public function getActiveProgram($withLink=1){
        if ($this->status == self::STATUS_ACTIVE) {
            // /if student
            if (Yii::$app->user->can('Student')) {
                // show academic year, semester, classlevel, depending on group assignemnt :
                // 'status' => \backend\models\Student::STATUS_ACTIVE,
                $stdAccount = \backend\models\Student::find()
                    ->where([
                        'std_user_relId' => Yii::$app->user->id,
                    ])
                    ->all();
                $activeAcc = null;
                if ($stdAccount && count($stdAccount) > 0) {
                    foreach ($stdAccount as $std) {
                        $gset = $std->stdGroupReal;
                        if (is_null($gset)) {
                            return ['type' => 'warning','message' => "Not Assigned to any group/section."];
                        }else{
                            if (is_null($gset->gs_active_prog_relId)) {
                                $modelActiveProgram = new AcActivePrograms();
                                return $modelActiveProgram->checkProgramActive($gset);
                            }else{
                               $program = $gset->gsActiveProgRel;
                               if ($program->status == $program::STATUS_ACTIVE) {
                                    if ($std->std_group_realId != $gset->id) {
                                        $std->std_group_realId = $gset->id;
                                        $std->update(false);
                                    }
                                    return ['type' => 'success','message' => $program->getClassLevelHeader()];
                                }else{
                                    return ['type' => 'warning','message' => $program->getClassLevelHeader()];
                                }
                            }
                        }
                    }
                }
            }else{
                $modelActiveProgram = new AcActivePrograms();
                return $modelActiveProgram->getActiveAcademicYear($withLink);
            }
        }else{
            return ['type' => 'error','message' => "Account Blocked"];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function getRoleTypes2(){//get roles as an array.
        $role = [];
        $roller = Yii::$app->get('authManager')->getRoles();
        foreach ($roller as $item) {
            if (Yii::$app->user->can('DepartmentHead')) {
                if ($item->name ==  'Instructor' || $item->name == 'Technical Assistant') {
                    $role[$item->name] = $item->name;
                }else{
                    continue;
                }
            }
            $role[$item->name] = $item->name;
        }
        return $role;
    }

    public function getUserRoles(){
        $ar = array_keys(Yii::$app->authManager->getRolesByUser($this->id));
        $roles = "";
        foreach ($ar as $role) {
            $roles .= "$role<br>";
        }

        return $roles;
    }

    public function getLastLogin(){
        return $this->_last_login;
    }

    public function getFullName(){
        // var_dump($this);
        if (is_null($this->fullName)) {
            $this->fullName = $this->first_name.' '.$this->middle_name.' '.$this->last_name;
        }
        return $this->fullName;
    }

    public static function getRoleTypes(){//get roles as an array.
        $role = [];
        $roller = Yii::$app->get('authManager')->getRoles();
        if (\Yii::$app->user->can('DepartmentHead')) {
           foreach ($roller as $item) {
                if (in_array($item->name,['Instructor','Technical Assistant'])) {
                    $role[$item->name] = $item->name;
                }
            } 
        }else{
            foreach ($roller as $item) {
                $role[$item->name] = $item->name;
            }
        }
        return $role;
    }

    public static function getRoleTypes3(){//get roles as an array.
        $role = [];
        $roller = Yii::$app->get('authManager')->getRoles();
        foreach ($roller as $item) {
            $role[] = [
                'name' => $item->name,
                'description' => $item->name." _ ".$item->description
            ];
        }
        return $role;
    }

    public function getUserRoleRel(){
       return $this->hasOne(Assignment::class, ['user_id' => 'id']);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    public static function cleanDuplicateHoDAccount(){
        $role = 'DepartmentHead';
        $roleAssignments  = (new yii\db\Query)->from('auth_assignment')
            ->where(" item_name = '{$role}' ")
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        $dupCount = 0;
        // var_dump($roleAssignments);

        if ($roleAssignments && count($roleAssignments) > 0) {
            foreach ($roleAssignments as $assignemnt) {
                // var_dump()
                $inst = Instructor::findOne(['inst_user_relId' => $assignemnt['user_id']]);
                if ($inst) {
                    // echo "<br>Current: ".$inst->getFullName();
                    $dup = Instructor::find()
                        ->innerJoin('user','user.id = instructor.inst_user_relId')
                        ->innerJoin('auth_assignment','user.id = auth_assignment.user_id')
                        ->where(['inst_dep_relId' => $inst->inst_dep_relId,'item_name' => $role])
                        ->andWhere(' inst_user_relId <> '.$assignemnt['user_id'])
                        ->all();
                    if ($dup && count($dup) > 1) {
                        foreach ($dup as $dups) {
                            \Yii::$app->db->createCommand()
                                ->delete('auth_assignment',['user_id' => $dups->inst_user_relId])
                                ->execute();
                        }
                    }
                }else{
                    \Yii::$app->db->createCommand()
                        ->delete('auth_assignment',['user_id' => $assignemnt['user_id']])
                        ->execute();
                }
            }
        }
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
