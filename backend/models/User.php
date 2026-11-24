<?php

namespace backend\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;


/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $gender
 * @property string $eth_group_relId
 * @property string $user_type
 * @property string $auth_key
 * @property string $password_hash
 * @property string $place_of_birth_address_relId
 * @property string $password_reset_token
 * @property string $email
 * @property string $phone_mobile
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $nationality
 * @property string $dob
 * @property string $date_of_join
 *
 * @property AcademicCalander[] $academicCalanders
 * @property ClassLevel[] $classLevels
 * @property CourseCatalog[] $courseCatalogs
 * @property CourseOffering[] $courseOfferings
 * @property SemesterRegistration[] $semesterRegistrations
 * @property SemesterRegistration2[] $semesterRegistration2s
 * @property Specialization[] $specializations
 * @property Student[] $students
 * @property Student[] $students0
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $role;
    /**
     * @inheritdoc
     */
    const STATUS_DELETED = 0;
    const ACTIVE_ACCOUNT = 1;
    const INACTIVE_ACCOUNT = 2;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    const STATUS_UNAPPROVED = 2; //default user account status. - NOT_APPROVED_YET
    public $userRole;
    public $search1 = null;
    public $search2 = null;
    public $fullName = null;
    public $fullName_av = null;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }


    public static function tableName()
    {
        return 'user';
    }

    /**
     * Finds all users by assignment role
     *
     * @param  \yii\rbac\Role $role
     * @return static|null
     */
    public static function findByRole($role_name)
    {
        return static::find()
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = id')
            ->where(['auth_assignment.item_name' => $role_name])
            ->all();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'first_name', 'middle_name', 'password_hash', 'nationality', 'dob', 'userRole'], 'required'],
            [['gender', 'created_at', 'updated_at', 'role'], 'string'],
            [['status', 'nationality', 'eth_group_relId'], 'integer'],
            [['dob', 'date_of_join'], 'safe'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['userRole', 'first_name', 'last_name', 'middle_name'], 'string', 'max' => 100],
            [['user_type'], 'string', 'max' => 20],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['phone_mobile'], 'unique'],
            [['faculty_relId', 'activated_at', 'activated_by'], 'integer'],
            [['OTP'], 'unique'],
            [['email'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_UNAPPROVED],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_UNAPPROVED, self::STATUS_DELETED]],
            [['password_reset_token'], 'unique'],
            [['nationality'], 'exist', 'skipOnError' => true, 'targetClass' => Nationality::class, 'targetAttribute' => ['nationality' => 'id']],
            [['eth_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => EthnicGroup::class, 'targetAttribute' => ['eth_group_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role' => Yii::t('rms', 'Role'),
            'username' => Yii::t('rms', 'Username'),
            'eth_group_relId' => Yii::t('rms', 'Ethnic Group'),
            'first_name' => Yii::t('rms', 'First Name'),
            'last_name' => Yii::t('rms', 'Last Name'),
            'gender' => Yii::t('rms', 'Gender'),
            'user_type' => Yii::t('rms', 'User Type'),
            'auth_key' => Yii::t('rms', 'Auth Key'),
            'password_hash' => Yii::t('rms', 'Password Hash'),
            'password_reset_token' => Yii::t('rms', 'Password Reset Token'),
            'email' => Yii::t('rms', 'Email'),
            'status' => Yii::t('rms', 'Status'),
            'created_at' => Yii::t('rms', 'Registered On'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'nationality' => Yii::t('rms', 'Nationality'),
            'dob' => Yii::t('rms', 'Dob'),
        ];
    }

    public function getUsername()
    {
        return \Yii::$app->params['college_accr'] . date('his') . rand(9, 100);
    }

    public function getPassword()
    {
        return 'wt_p0000';
    }


    public function getDob()
    {
        return date_format(date_create($this->dob), 'd/m/Y');
    }
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    private function generateRandomString($length = 10)
    {
        $bytes = random_bytes(ceil($length / 2));
        $randomString = substr(bin2hex($bytes), 0, $length);

        return $randomString;
    }

    public function getOTP()
    { //25
        // // str_pad(input, 3);
        // $rr = rand(99999,100000000);
        // $dd = date('Ymdhis');
        // $OTP = '';
        // $app = $rr.$dd;
        // for ($i=0; $i < 20; $i++) {$OTP .= $app[$i];}
        $RString = $this->generateRandomString(6);
        return strtoupper('S' . $RString . 'WT');
    }

    public function getRoleTypes()
    { //get roles as an array.
        $role = [];
        if (\Yii::$app->user->isGuest) { //user account signup is allowed only for students:
            $role['Student'] = 'STUDENT';
        } else {
            $roller = Yii::$app->get('authManager')->getRoles();
            if (\Yii::$app->user->can('DepartmentHead')) {
                foreach ($roller as $item) {
                    if (in_array($item->name, ['Instructor', 'Technical Assistant'])) {
                        $role[$item->name] = strtoupper($item->name);
                    }
                }
            } else {
                foreach ($roller as $item) {
                    $role[$item->name] = strtoupper($item->name);
                }
            }
        }

        // sort($role);
        return $role;
    }


    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getUserRole($userId)
    {
        $role = Yii::$app->authManager->getRolesByUser($userId);

        echo $role && count($role) > 0 ? array_keys($role)[0] : '-';
    }

    public function getUserRole2($userId)
    {
        $role = Yii::$app->authManager->getRolesByUser($userId);

        return  $role && count($role) > 0 ? array_keys($role)[0] : '-';
    }

    public function getFullName_am()
    {
        if (is_null($this->first_name_am) || is_null($this->last_name_am)) {
            return "---";
        } else {
            return "{$this->first_name_am} {$this->middle_name_am} {$this->last_name_am}";
        }
    }

    public function getFullName()
    {
        if (is_null($this->fullName)) {
            // for instructors -- (HoDs), get their academic qualification prefix and display their name.
            $prefix = '';
            $inst = Instructor::findOne(['inst_user_relId' => $this->id]);
            if ($inst) {
                $acr = $inst->getAcqQualDisp();
                $prefix = $acr ?  "({$acr})" : '';
                $this->fullName = ucfirst(strtolower(trim($this->first_name))) . ' ' . ucfirst(strtolower($this->middle_name)) . " {$prefix}";
            } else {
                $this->fullName = ucfirst(strtolower(trim($this->first_name))) . ' ' . ucfirst(strtolower($this->middle_name)) . ' ' . ucfirst(strtolower($this->last_name)) . " {$prefix}";
            }
        }
        return $this->fullName;
    }

    public function getFullName_av()
    {
        if (is_null($this->fullName_av)) {
            $this->fullName_av = ucfirst(strtolower($this->first_name)) . ' ' . ucfirst($this->middle_name[0]);
        }
        return $this->fullName_av;
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
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
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

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicCalanders()
    {
        return $this->hasMany(AcademicCalander::class, ['acad_setby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClassLevels()
    {
        return $this->hasMany(ClassLevel::class, ['clslvl_setby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaceOfBirthAddressRel()
    {
        return $this->hasOne(AddressInfo::class, ['id' => 'place_of_birth_address_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNationalityRel()
    {
        return $this->hasOne(Nationality::class, ['id' => 'nationality']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEthGroupRel()
    {
        return $this->hasOne(EthnicGroup::class, ['id' => 'eth_group_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaceOfRecidenceAddressRel()
    {
        return $this->hasOne(AddressInfo::class, ['place_of_recidence_address_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseCatalogs()
    {
        return $this->hasMany(CourseCatalog::class, ['coff_setby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseOfferings()
    {
        return $this->hasMany(CourseOffering::class, ['coff_setBy' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrations()
    {
        return $this->hasMany(SemesterRegistration::class, ['sr_student_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2s()
    {
        return $this->hasMany(SemesterRegistration2::class, ['sr_student_relId' => 'id']);
    }

    public function getUserInstRel()
    {
        return $this->hasOne(Instructor::class, ['inst_user_relId' => 'id']);
    }

    public function getUserStdRel()
    {
        return $this->hasOne(Student::class, ['std_user_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecializations()
    {
        return $this->hasMany(Specialization::class, ['spec_setby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents0()
    {
        return $this->hasMany(Student::class, ['std_setby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(Student::class, ['std_user_relId' => 'id']);
    }
}
