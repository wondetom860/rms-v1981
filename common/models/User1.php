<?php

namespace common\models;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $gender
 * @property string $eth_group_relId
 * @property string $auth_key
 * @property string $user_type
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $nationality
 * @property integer $place_of_birth_address_relId
 * @property integer $place_of_residence_address_relId
 * @property string $dob
 * @property string $dob_ET
 * @property string $photo_num
 * @property string $created_at
 * @property string $updated_at
 * @property integer $updated_by
 * @property integer $created_by
 *
 * @property AcadProfileChange[] $acadProfileChanges
 * @property AcadProfileChange[] $acadProfileChanges0
 * @property AcadProfileChange[] $acadProfileChanges1
 * @property AcademicCalander[] $academicCalanders
 * @property AcademicCalander[] $academicCalanders0
 * @property AcademicCalanderAction[] $academicCalanderActions
 * @property AcademicCalanderAction[] $academicCalanderActions0
 * @property AcademicQualification[] $academicQualifications
 * @property AcademicQualification[] $academicQualifications0
 * @property AcademicRank[] $academicRanks
 * @property AcademicRank[] $academicRanks0
 * @property AddHistory[] $addHistories
 * @property AddHistory[] $addHistories0
 * @property AddHistory[] $addHistories1
 * @property Attendance[] $attendances
 * @property ClassLevel[] $classLevels
 * @property ClassLevel[] $classLevels0
 * @property ContAssessCatagory2[] $contAssessCatagory2s
 * @property ContAssessCatagory2[] $contAssessCatagory2s0
 * @property ContinuousAssessment2[] $continuousAssessment2s
 * @property ContinuousAssessment2[] $continuousAssessment2s0
 * @property Course[] $courses
 * @property Course[] $courses0
 * @property CourseCatalog[] $courseCatalogs
 * @property CourseCatalog[] $courseCatalogs0
 * @property CourseOffering2Detail[] $courseOffering2Details
 * @property CourseOffering2Detail[] $courseOffering2Details0
 * @property CoursePrerequisit[] $coursePrerequisits
 * @property CoursePrerequisit[] $coursePrerequisits0
 * @property DepartmentHead[] $departmentHeads
 * @property DepartmentHead[] $departmentHeads0
 * @property EducationInformation[] $educationInformations
 * @property EducationInformation[] $educationInformations0
 * @property InstanceMarkAssignment[] $instanceMarkAssignments
 * @property InstanceMarkAssignment[] $instanceMarkAssignments0
 * @property Instructor[] $instructors
 * @property Instructor[] $instructors0
 * @property Instructor[] $instructors1
 * @property MakeupExamRequest[] $makeupExamRequests
 * @property MakeupExamRequest[] $makeupExamRequests0
 * @property PostSecondaryEducation[] $postSecondaryEducations
 * @property PostSecondaryEducation[] $postSecondaryEducations0
 * @property Program[] $programs
 * @property Program[] $programs0
 * @property ProjectCourseSetup[] $projectCourseSetups
 * @property ProjectCourseSetup[] $projectCourseSetups0
 * @property ProjectCourseSetup[] $projectCourseSetups1
 * @property ProjectGroupMembers[] $projectGroupMembers
 * @property ProjectGroupMembers[] $projectGroupMembers0
 * @property ProjectGroupMembers[] $projectGroupMembers1
 * @property ReadmissionHistory[] $readmissionHistories
 * @property ReadmissionHistory[] $readmissionHistories0
 * @property ReassessmentHistory[] $reassessmentHistories
 * @property Region[] $regions
 * @property Region[] $regions0
 * @property ScholarshipType[] $scholarshipTypes
 * @property ScholarshipType[] $scholarshipTypes0
 * @property SchoolYear[] $schoolYears
 * @property SchoolYear[] $schoolYears0
 * @property SecSpecRecomend[] $secSpecRecomends
 * @property SecSpecRecomend[] $secSpecRecomends0
 * @property SecondarySchoolAttended[] $secondarySchoolAttendeds
 * @property SecondarySchoolAttended[] $secondarySchoolAttendeds0
 * @property SelfSponsoredPaymentHistory[] $selfSponsoredPaymentHistories
 * @property Semester[] $semesters
 * @property Semester[] $semesters0
 * @property SemesterEvaluation2[] $semesterEvaluation2s
 * @property SemesterEvaluation2Detail[] $semesterEvaluation2Details
 * @property SemesterRegistration2[] $semesterRegistration2s
 * @property SemesterRegistration2[] $semesterRegistration2s0
 * @property SemesterRegistration2[] $semesterRegistration2s1
 * @property SemesterRegistration2Detail[] $semesterRegistration2Details
 * @property SemesterRegistration2Detail[] $semesterRegistration2Details0
 * @property SemesterRegistration2Detail[] $semesterRegistration2Details1
 * @property SemesterRegistration2Detail[] $semesterRegistration2Details2
 * @property SemesterRegistrationControlac[] $semesterRegistrationControlacs
 * @property SemesterRegistrationControlac[] $semesterRegistrationControlacs0
 * @property SemesterRegistrationControld[] $semesterRegistrationControlds
 * @property SemesterRegistrationControld[] $semesterRegistrationControlds0
 * @property Specialization[] $specializations
 * @property Specialization[] $specializations0
 * @property StdStatusCalculationControlAc[] $stdStatusCalculationControlAcs
 * @property StdStatusCalculationControlAc[] $stdStatusCalculationControlAcs0
 * @property StdStatusCalculationControlDefault[] $stdStatusCalculationControlDefaults
 * @property StdStatusCalculationControlDefault[] $stdStatusCalculationControlDefaults0
 * @property Student[] $students
 * @property Student[] $students0
 * @property Student[] $students1
 * @property StudentSemesterStatusHistory[] $studentSemesterStatusHistories
 * @property StudentSemesterStatusHistory[] $studentSemesterStatusHistories0
 * @property StudentType[] $studentTypes
 * @property StudentType[] $studentTypes0
 * @property StudentWithdrawalHistory[] $studentWithdrawalHistories
 * @property StudentWithdrawalHistory[] $studentWithdrawalHistories0
 * @property AddressInfo $placeOfBirthAddressRel
 * @property AddressInfo $placeOfResidenceAddressRel
 * @property WithdrawalReason[] $withdrawalReasons
 * @property WithdrawalReason[] $withdrawalReasons0
 */
class User1 extends \yii\db\ActiveRecord
{

    public $id_for_file;
    public $file;
    public $userRole;

    const STATUS_GRADUATED = 5;
    const ACTIVE_ACCOUNT = 1;
    const INACTIVE_ACCOUNT = 2;
    /**
     * @inheritdoc
     */
    // public static function tableName()
    // {
    //     return 'user';
    // }

    /**
     * @inheritdoc
     */


    // std_pg_EC_relId => EMERGENCY CONTACT for the PG student can be Instructor in the college....
    public function rules()
    {
        return [
            [['first_name', 'middle_name', 'nationality', 'dob', 'user_type','phone_mobile'], 'required'],
            [['gender'], 'string'],
            [['status', 'nationality', 'place_of_birth_address_relId', 'place_of_residence_address_relId', 'updated_by', 'created_by','eth_group_relId'], 'integer'],
            [['dob','phone_mobile','phone_home', 'dob_ET'], 'safe'],
            [['email'],'email'],
            [['email'],'unique'],
            [['OTP'],'unique'],
            [['faculty_relId','activated_at','activated_by'],'integer'],
            [['phone_mobile'],'unique'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 100],
            [['auth_key'], 'string', 'max' => 32],
            [['user_type'], 'string', 'max' => 20],
            [['photo_num'], 'string', 'max' => 50],
            [['phone_mobile'], 'string', 'max' => 12],
            [['phone_mobile'], 'string', 'min' => 9],
            [['OTP'], 'string', 'max' => 25],
            ['status', 'default', 'value' => self::STATUS_UNAPPROVED],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_UNAPPROVED, self::STATUS_DELETED,self::STATUS_GRADUATED]],
            [['place_of_birth_address_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\AddressInfo::class, 'targetAttribute' => ['place_of_birth_address_relId' => 'id']],
            [['place_of_residence_address_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\AddressInfo::class, 'targetAttribute' => ['place_of_residence_address_relId' => 'id']],
            [['eth_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\EthnicGroup::class, 'targetAttribute' => ['eth_group_relId' => 'id']],
            // 'minWidth' => 250, 'maxWidth' => 250,'minHeight' => 250, 'maxHeight' => 250,
            ['file', 'image', 'extensions' => 'jpg, gif, png', 'maxSize' => 1024 * 1024 * 2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'username' => Yii::t('rms', 'Username'),
            'first_name' => Yii::t('rms', 'First Name'),
            'middle_name' => Yii::t('rms', 'Middle Name'),
            'last_name' => Yii::t('rms', 'Last Name'),
            'eth_group_relId' => Yii::t('rms', 'Ethnic Group'),
            'gender' => Yii::t('rms', 'Gender'),
            'auth_key' => Yii::t('rms', 'Auth Key'),
            'faculty_relId' => Yii::t('rms', 'Faculty'),
            'user_type' => Yii::t('rms', 'User Type'),
            'password_hash' => Yii::t('rms', 'Password Hash'),
            'password_reset_token' => Yii::t('rms', 'Password Reset Token'),
            'email' => Yii::t('rms', 'Email'),
            'status' => Yii::t('rms', 'Status'),
            'nationality' => Yii::t('rms', 'Nationality'),
            'place_of_birth_address_relId' => Yii::t('rms', 'Place Of Birth'),
            'place_of_residence_address_relId' => Yii::t('rms', 'Place Of Residence'),
            'dob' => Yii::t('rms', 'Dob'),
            'dob_ET' => Yii::t('rms', 'Dob  Et'),
            'photo_num' => Yii::t('rms', 'Photo Num'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'created_by' => Yii::t('rms', 'Created By'),
        ];
    }

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_UNAPPROVED = 2;//default user account status. - NOT_APPROVED_YET


    public function getStatus(){
        return ['STATUS_UNAPPROVED'=>2,'STATUS_ACTIVE'=>10,'STATUS_DELETED'=>0];
    }

    // get alert for un-approved accounts in Registrar home page.
    public function checkregistrarRequest($fac=null){
        return is_null($fac)?$this->find()
            ->where([
                'status'=>self::STATUS_UNAPPROVED,
            ])
            ->count():
            $this->find()
            ->where([
                'faculty_relId' => $fac->fas_faculty_relId,
                'status'=>self::STATUS_UNAPPROVED,
            ])
            ->count();
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    // private function getOTP(){//21
    //     // str_pad(input, 3);
    //     $rr = rand(0,1000);
    //     $dd = date('Y-m-d_his');
    //     $OTP = '';
    //     $app = $rr.'-'.$dd;
    //     for ($i=0; $i < 16; $i++) {$OTP .= $app[$i];}
    //     return strtoupper('S-'. $OTP .'-WT');
    // }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    // public function rules()
    // {
    //     return [
            
    //     ];
    // }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public function getRoleTypes(){//get roles as an array.

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
        
        // $roller = Yii::$app->get('authManager')->getRoles();
        // foreach ($roller as $item) {
        //     $role[$item->name] = $item->name;
        // }
        // return $role;
    }

    public function UploadAttachments($stdModel,$pgei_IDS){
        if (isset($_SESSION['WORK_EXPERIENCE']) && isset($_SESSION['RESEARCHES']) && isset($_SESSION['AWARDS'])) {

            if (!is_null($pgei_IDS)) {
                // $pgei_IDS = $admissionEntriesArray[0];

                $model = $this->findOne($stdModel->std_user_relId);
                if ($model) {
                    $fileNames = $this->saveToFile($model);
                    if (is_array($fileNames)) {
                        $result = Yii::$app->db->createCommand()
                        ->update('pg_education_info',
                            [
                                'pgei_work_experience'=>$fileNames[0],
                                'pgei_reserches'=>$fileNames[1],
                                'pgei_awards'=>$fileNames[2]
                            ],
                            [
                                'id'=>$pgei_IDS
                            ])
                        ->execute();
                        if ($result) {
                            return true;
                        }
                        return false;
                    }
                }
            }
        }
        return false;
    }


    private function saveToFile($model){

        $fileName_WE = $this->getFileName($model,'WORK_EXPERIENCE');
        $fileName_RES = $this->getFileName($model,'RESEARCHES');
        $fileName_AW = $this->getFileName($model,'AWARDS');

        // $msg = $epx == 1?:($epx == 2?$_SESSION['RESEARCHES']:$_SESSION['AWARDS']);
        if (is_string($fileName_WE)) {
           $f = @fopen($fileName_WE, "w");
            if ($f) {
                fwrite($f, $_SESSION['WORK_EXPERIENCE']);
                fclose($f);
            }
        }
        if (is_string($fileName_RES)) {
            $f = @fopen($fileName_RES, "w");
            if ($f) {
                fwrite($f, $_SESSION['RESEARCHES']);
                fclose($f);
            }
        }
        if (is_string($fileName_AW)) {
            $f = @fopen($fileName_AW, "w");
            if ($f) {
                fwrite($f, $_SESSION['AWARDS']);
                fclose($f);
            }
        }

        return [$fileName_WE,$fileName_RES,$fileName_AW];
        
    }

    private function getFileName($model,$option){
       return 'uploads/'.str_replace('/', '', date('Y').'_'.$model->first_name.'_'.$model->middle_name.'_'.$option.'.txt');
    }


    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
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

    private function generateRandomString($length = 10) {
        $bytes = random_bytes(ceil($length / 2));
        $randomString = substr(bin2hex($bytes), 0, $length);
     
        return $randomString;
     }

    public function getOTP(){//25
        // // str_pad(input, 3);
        // $rr = rand(99999,100000000);
        // $dd = date('Ymdhis');
        // $OTP = '';
        // $app = $rr.$dd;
        // for ($i=0; $i < 20; $i++) {$OTP .= $app[$i];}
        $RString = $this->generateRandomString(6);
        return strtoupper('S'. $RString .'WT');
    }


    public function getUsername(){
        return \Yii::$app->params['college_accr'].date('his').rand(9,100);
    }

    public function getPassword(){
        return 'wt_p0000';
    }

    public function checkIfExists2($model){
        return $this->find()
            ->where([
                'first_name'=>$model->first_name,
                'middle_name'=>$model->middle_name,
                'last_name'=>$model->last_name,
            ])
            ->count() > 0;
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
    public function getAcadProfileChanges()
    {
        return $this->hasMany(\backend\models\AcadProfileChange::class, ['apc_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcadProfileChanges0()
    {
        return $this->hasMany(\backend\models\AcadProfileChange::class, ['apc_registrarApproved_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcadProfileChanges1()
    {
        return $this->hasMany(\backend\models\AcadProfileChange::class, ['apc_updated_by' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getFaculty()
    // {
    //      return $this->hasOne(Faculty::class, ['id' => 'faculty_relId']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaceOfRecidenceAddressRel()
    {
        return $this->hasOne(\backend\models\AddressInfo::class, ['id' => 'place_of_residence_address_relId']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicCalanders()
    {
        return $this->hasMany(\backend\models\AcademicCalander::class, ['acad_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicCalanders0()
    {
        return $this->hasMany(\backend\models\AcademicCalander::class, ['acad_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicCalanderActions()
    {
        return $this->hasMany(\backend\models\AcademicCalanderAction::class, ['aca_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(\common\models\User1::class, ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicCalanderActions0()
    {
        return $this->hasMany(\backend\models\AcademicCalanderAction::class, ['aca_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicQualifications()
    {
        return $this->hasMany(\backend\models\AcademicQualification::class, ['acq_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicQualifications0()
    {
        return $this->hasMany(\backend\models\AcademicQualification::class, ['acq_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicRanks()
    {
        return $this->hasMany(\backend\models\AcademicRank::class, ['acr_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicRanks0()
    {
        return $this->hasMany(\backend\models\AcademicRank::class, ['acr_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddHistories()
    {
        return $this->hasMany(\backend\models\AddHistory::class, ['ah_approved_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddHistories0()
    {
        return $this->hasMany(\backend\models\AddHistory::class, ['ah_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddHistories1()
    {
        return $this->hasMany(\backend\models\AddHistory::class, ['ah_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttendances()
    {
        return $this->hasMany(\backend\models\Attendance::class, ['a_instructor_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClassLevels()
    {
        return $this->hasMany(\backend\models\ClassLevel::class, ['clslvl_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClassLevels0()
    {
        return $this->hasMany(\backend\models\ClassLevel::class, ['clslvl_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContAssessCatagory2s()
    {
        return $this->hasMany(\backend\models\ContAssessCatagory2::class, ['cat_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContAssessCatagory2s0()
    {
        return $this->hasMany(\backend\models\ContAssessCatagory2::class, ['cat_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContinuousAssessment2s()
    {
        return $this->hasMany(\backend\models\ContinuousAssessment2::class, ['ca_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContinuousAssessment2s0()
    {
        return $this->hasMany(\backend\models\ContinuousAssessment2::class, ['ca_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourses()
    {
        return $this->hasMany(\backend\models\Course::class, ['course_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourses0()
    {
        return $this->hasMany(\backend\models\Course::class, ['course_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseCatalogs()
    {
        return $this->hasMany(\backend\models\CourseCatalog::class, ['cocat_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseCatalogs0()
    {
        return $this->hasMany(\backend\models\CourseCatalog::class, ['cocat_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseOffering2Details()
    {
        return $this->hasMany(\backend\models\CourseOffering2Detail::class, ['coff2det_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseOffering2Details0()
    {
        return $this->hasMany(\backend\models\CourseOffering2Detail::class, ['coff2det_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoursePrerequisits()
    {
        return $this->hasMany(\backend\models\CoursePrerequisit::class, ['preq_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoursePrerequisits0()
    {
        return $this->hasMany(\backend\models\CoursePrerequisit::class, ['preq_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentHeads()
    {
        return $this->hasMany(\backend\models\DepartmentHead::class, ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentHeads0()
    {
        return $this->hasMany(\backend\models\DepartmentHead::class, ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEducationInformations()
    {
        return $this->hasMany(\backend\models\EducationInformation::class, ['ei_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEthGroupRel()
    {
        return $this->hasOne(\backend\models\EthnicGroup::class, ['id' => 'eth_group_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEducationInformations0()
    {
        return $this->hasMany(\backend\models\EducationInformation::class, ['ei_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstanceMarkAssignments()
    {
        return $this->hasMany(\backend\models\InstanceMarkAssignment::class, ['ima_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstanceMarkAssignments0()
    {
        return $this->hasMany(\backend\models\InstanceMarkAssignment::class, ['ima_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstructors()
    {
        return $this->hasMany(\backend\models\Instructor::class, ['inst_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstructors0()
    {
        return $this->hasMany(\backend\models\Instructor::class, ['inst_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstructors1()
    {
        return $this->hasMany(\backend\models\Instructor::class, ['inst_user_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMakeupExamRequests()
    {
        return $this->hasMany(\backend\models\MakeupExamRequest::class, ['mer_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMakeupExamRequests0()
    {
        return $this->hasMany(\backend\models\MakeupExamRequest::class, ['mer_approved_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostSecondaryEducations()
    {
        return $this->hasMany(\backend\models\PostSecondaryEducation::class, ['pse_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostSecondaryEducations0()
    {
        return $this->hasMany(\backend\models\PostSecondaryEducation::class, ['pse_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrograms()
    {
        return $this->hasMany(\backend\models\Program::class, ['program_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrograms0()
    {
        return $this->hasMany(\backend\models\Program::class, ['program_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCourseSetups()
    {
        return $this->hasMany(\backend\models\ProjectCourseSetup::class, ['pcs_approved_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCourseSetups0()
    {
        return $this->hasMany(\backend\models\ProjectCourseSetup::class, ['pcs_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getProjectCourseSetups1()
    // {
    //     return $this->hasMany(ProjectCourseSetup::class, ['pcs_updated_by' => 'id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getProjectGroupMembers()
    // {
    //     return $this->hasMany(ProjectGroupMembers::class, ['pgm_gradeApproved_by' => 'id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getProjectGroupMembers0()
    // {
    //     return $this->hasMany(ProjectGroupMembers::class, ['pgm_craeted_by' => 'id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getProjectGroupMembers1()
    // {
    //     return $this->hasMany(ProjectGroupMembers::class, ['pgm_updated_by' => 'id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReadmissionHistories()
    {
        return $this->hasMany(\backend\models\WStudentReadmissionHistory::class, ['rh_createdBy' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReadmissionHistories0()
    {
        return $this->hasMany(\backend\models\WStudentReadmissionHistory::class, ['rh_updatedBy' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReassessmentHistories()
    {
        return $this->hasMany(\backend\models\ReassessmentHistory::class, ['ras_approved_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegions()
    {
        return $this->hasMany(\backend\models\Region::class, ['region_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegions0()
    {
        return $this->hasMany(\backend\models\Region::class, ['region_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScholarshipTypes()
    {
        return $this->hasMany(\backend\models\ScholarshipType::class, ['schType_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNationalityRel()
    {
        return $this->hasOne(\backend\models\Nationality::class, ['id' => 'nationality']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScholarshipTypes0()
    {
        return $this->hasMany(\backend\models\ScholarshipType::class, ['schType_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolYears()
    {
        return $this->hasMany(\backend\models\SchoolYear::class, ['sch_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolYears0()
    {
        return $this->hasMany(\backend\models\SchoolYear::class, ['sch_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecSpecRecomends()
    {
        return $this->hasMany(\backend\models\SecSpecRecomend::class, ['ssr_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecSpecRecomends0()
    {
        return $this->hasMany(\backend\models\SecSpecRecomend::class, ['ssr_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecondarySchoolAttendeds()
    {
        return $this->hasMany(\backend\models\SecondarySchoolAttended::class, ['ssa_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecondarySchoolAttendeds0()
    {
        return $this->hasMany(\backend\models\SecondarySchoolAttended::class, ['ssa_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSelfSponsoredPaymentHistories()
    {
        return $this->hasMany(\backend\models\SelfSponsoredPaymentHistory::class, ['ssph_approved_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesters()
    {
        return $this->hasMany(\backend\models\Semester::class, ['semester_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesters0()
    {
        return $this->hasMany(\backend\models\Semester::class, ['semester_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterEvaluation2s()
    {
        return $this->hasMany(\backend\models\SemesterEvaluation2::class, ['semev_evaluee_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterEvaluation2Details()
    {
        return $this->hasMany(\backend\models\SemesterEvaluation2Detail::class, ['semev2det_evaluator_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2s()
    {
        return $this->hasMany(\backend\models\SemesterRegistration2::class, ['semreg_approved_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2s0()
    {
        return $this->hasMany(\backend\models\SemesterRegistration2::class, ['semreg_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2s1()
    {
        return $this->hasMany(\backend\models\SemesterRegistration2::class, ['semreg_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2Details()
    {
        return $this->hasMany(\backend\models\SemesterRegistration2Detail::class, ['srd2_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2Details0()
    {
        return $this->hasMany(\backend\models\SemesterRegistration2Detail::class, ['srd2_enrollApproved_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2Details1()
    {
        return $this->hasMany(\backend\models\SemesterRegistration2Detail::class, ['srd2_gradeApproved_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2Details2()
    {
        return $this->hasMany(\backend\models\SemesterRegistration2Detail::class, ['srd2_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrationControlacs()
    {
        return $this->hasMany(\backend\models\SemesterRegistrationControlac::class, ['src_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrationControlacs0()
    {
        return $this->hasMany(\backend\models\SemesterRegistrationControlac::class, ['src_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrationControlds()
    {
        return $this->hasMany(\backend\models\SemesterRegistrationControld::class, ['src_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrationControlds0()
    {
        return $this->hasMany(\backend\models\SemesterRegistrationControld::class, ['src_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecializations()
    {
        return $this->hasMany(\backend\models\Specialization::class, ['spec_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecializations0()
    {
        return $this->hasMany(\backend\models\Specialization::class, ['spec_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdStatusCalculationControlAcs()
    {
        return $this->hasMany(\backend\models\StdStatusCalculationControlAc::class, ['sssccAC_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdStatusCalculationControlAcs0()
    {
        return $this->hasMany(\backend\models\StdStatusCalculationControlAc::class, ['sssccAC_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdStatusCalculationControlDefaults()
    {
        return $this->hasMany(\backend\models\StdStatusCalculationControlDefault::class, ['sssccd_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdStatusCalculationControlDefaults0()
    {
        return $this->hasMany(\backend\models\StdStatusCalculationControlDefault::class, ['sssccd_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(\backend\models\Student::class, ['std_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents0()
    {
        return $this->hasMany(\backend\models\Student::class, ['std_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents1()
    {
        return $this->hasMany(\backend\models\Student::class, ['std_user_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentSemesterStatusHistories()
    {
        return $this->hasMany(\backend\models\StudentSemesterStatusHistory::class, ['sssh_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentSemesterStatusHistories0()
    {
        return $this->hasMany(\backend\models\StudentSemesterStatusHistory::class, ['sssh_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentTypes()
    {
        return $this->hasMany(\backend\models\StudentType::class, ['stdType_created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentTypes0()
    {
        return $this->hasMany(\backend\models\StudentType::class, ['stdType_updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentWithdrawalHistories()
    {
        return $this->hasMany(\backend\models\WStudentWithdrawalHistory::class, ['swh_createdBy' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentWithdrawalHistories0()
    {
        return $this->hasMany(\backend\models\WStudentWithdrawalHistory::class, ['swh_updatedBy' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaceOfBirthAddressRel()
    {
        return $this->hasOne(\backend\models\AddressInfo::class, ['id' => 'place_of_birth_address_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaceOfResidenceAddressRel()
    {
        return $this->hasOne(\backend\models\AddressInfo::class, ['id' => 'place_of_residence_address_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWithdrawalReasons()
    {
        return $this->hasMany(\backend\models\WithdrawalReason::class, ['withdrawalres_createdBy_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWithdrawalReasons0()
    {
        return $this->hasMany(\backend\models\WithdrawalReason::class, ['withdrawalres_updatedby_relId' => 'id']);
    }
}
