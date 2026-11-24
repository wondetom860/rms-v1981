<?php
namespace common\models;
 
use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;
use common\models\User;
 
/**
 * Change password form for current user only
 */
class ChangeUsernameForm extends Model
{
    public $id;
    public $oldUserName;
    public $newUserName;
    public $confirmNewUserName;
 
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
            [['oldUserName','newUserName','confirmNewUserName'], 'required'],
            [['oldUserName','newUserName','confirmNewUserName'], 'string', 'min' => 7],
            ['confirmNewUserName', 'compare', 'compareAttribute' => 'newUserName'],
        ];
    }
 
    /**
     * Changes password.
     *
     * @return boolean|array if username was changed.
     */
    public function changeUsername()
    {
        $user = $this->_user;

        $this->_user = User::findByUsername($this->newUserName);

        if($user->username != $this->oldUserName){
            return [0,'Old user name is NOT correct.'];
        }

        // var_dump(count($this->_user));die();

        if ($this->_user) {
            return [0,'Such Username already exists. Please select another.'];
        }else{
            $user->username = $this->newUserName;
 
            return $user->save(false);
        }
    }
}
 