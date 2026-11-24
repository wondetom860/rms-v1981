<?php
namespace common\models;
 
use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;
use common\models\User;
 
/**
 * Change password form for current user only
 */
class ChangePasswordForm extends Model
{
    public $id;
    public $password;
    public $_oldpassword;
    public $confirm_password;
 
    /**
     * @var \common\models\User
     */
    private $_user;
 
    /**
     * Creates a form model given a token.
     *
     * @param  string                          $token
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($id, $config = [])
    {
        $this->_user = User::findIdentity($id);
        
        if (!$this->_user) {
            throw new InvalidParamException('Unable to find user!');
        }
        
        $this->id = $this->_user->id;
        parent::__construct($config);
    }
 
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password','confirm_password','_oldpassword'], 'required'],
            [['password','confirm_password'], 'string', 'min' => 5],
            ['confirm_password', 'compare', 'compareAttribute' => 'password'],
            [['password','confirm_password'], 'compare', 'compareValue' => Yii::$app->params['default_password'],'operator' => '!=','message' => 'You can NOT set this as your new password.'],
            ['_oldpassword', 'compare', 'compareAttribute' => 'password', 'operator' => '!=', 'message' => 'New password must be different from the old password.'],
        ];
    }
 
    /**
     * Changes password.
     *
     * @return boolean|string if password was changed.
     */
    public function changePassword()
    {
        // check if old password is correct:
        $user = $this->_user;
        if ($user->validatePassword($this->_oldpassword)) {
            // $user->setPassword($this->password);
            return $this->resetPassword();
        }else{
            return "Your Old Password is NOT correct";
        }
    }

    /**
     * Resets password.
     *
     * @return bool|string if password was reset.
     */
    private function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        $user->generateAuthKey();
        $user->last_password_changed_date = time();
        $user->updated_at = date('Y-m-d h:i:s');
        $user->updated_by = Yii::$app->user->identity->id;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $us = $user;
            if ($user->update()) {
                // create log.
                $ip = $_SERVER["REMOTE_ADDR"];
                $admin = User1::findOne(Yii::$app->user->identity->id);
                $his = strtoupper($us->gender) == 'FEMALE'?'her':'his';
                $message = "User account password changed succesfully, 
                    {$us->first_name} {$us->middle_name[0]} [User Name : {$us->username}] can sign in {$his} account with NEW password. Machine IP from {$ip}
                    Operation performed by SELF on <span class='bg-warning'> ".date('r')." </span>";
                $this->logToPasswordReset($message,$admin,$us,$his);
                $transaction->commit();
                return true;
            }
        } catch (\ErrorException $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }

        return $user->save(false);
    }

    private function logToPasswordReset($message,$admin,$us,$his){
        $file = "uploads/passwordResetLog.txt";
        // $message = "User account password reset succes, {$us->first_name} {$us->middle_name[0]} [User Name : {$us->username}] can sign in {$his} account with default password. Operation performed by {$admin->first_name} {$admin->middle_name} on". date('r');
        $handler = fopen($file, 'a');
        fwrite($handler, "\n");
        fwrite($handler, $message);
        fclose($handler);
    }
}
 