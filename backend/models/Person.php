<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "person".
 *
 * @property int $id
 * @property string $ID_NO
 * @property string|null $MIL_ID_NO
 * @property int|null $person_user_relId
 * @property int|null $place_of_birth_relId
 * @property int|null $place_of_residence
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $dob
 * @property string|null $doj
 * @property string $gender
 * @property int|null $ec_relId
 * @property string $profession_type
 * @property string|null $phone_home
 * @property string|null $phone_mobile
 * @property string|null $phone_office
 * @property string|null $phone_office_ext
 * @property string|null $email
 * @property int|null $job_app_relId
 * @property int $status
 * @property string $registration_code
 * @property int|null $nationality
 * @property int|null $region
 * @property string $profile_pic
 * @property int $created_by
 * @property string $created_at
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 * @property string|null $first_name_am
 * @property string|null $middle_name_am
 * @property string|null $last_name_am
 * @property string|null $marital_status
 * @property string|null $dob_am
 * @property string|null $membership_category
 * @property string|null $retirement_date
 * @property int|null $parent_info_relId
 *
 * @property AcademicPromotionHistory[] $academicPromotionHistories
 * @property AcademicRankHistory[] $academicRankHistories
 * @property BattleFieldData[] $battleFieldDatas
 * @property ChildrenInfo[] $childrenInfos
 * @property ElrEmployeeLeaveRecord[] $elrEmployeeLeaveRecords
 * @property ElrEmployeeLeaveRecordEntry[] $elrEmployeeLeaveRecordEntries
 * @property ElrEmployeeLeaveRecordEntry[] $elrEmployeeLeaveRecordEntries0
 * @property EmploymentHistory[] $employmentHistories
 * @property EvlnEvaluatorsList[] $evlnEvaluatorsLists
 * @property EvlnEvalueesList[] $evlnEvalueesLists
 * @property LoqAssignment[] $loqAssignments
 * @property LorsquaDetail[] $lorsquaDetails
 * @property MedalPrizeData[] $medalPrizeDatas
 * @property MilitaryPenaltyHistory[] $militaryPenaltyHistories
 * @property MilitaryProfileHistory[] $militaryProfileHistories
 * @property MilitaryRankIrkenPromotionHistory[] $militaryRankIrkenPromotionHistories
 * @property MilitaryRewardHistory[] $militaryRewardHistories
 * @property MleEvalee[] $mleEvalees
 * @property MleEvaluatorsList[] $mleEvaluatorsLists
 * @property ParentInfo[] $parentInfos
 * @property User $createdBy
 * @property User $personUserRel
 * @property AddressInfo $placeOfBirthRel
 * @property AddressInfo $placeOfResidence
 * @property User $updatedBy
 * @property EmergencyContactInfo $ecRel
 * @property Region $region0
 * @property RmsUserLink[] $rmsUserLinks
 * @property TransferAssignmentData[] $transferAssignmentDatas
 * @property User[] $users
 */
class Person extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'person';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_hrms');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ID_NO', 'first_name', 'middle_name', 'last_name', 'dob', 'gender', 'profession_type', 'registration_code', 'created_by', 'created_at', 'data'], 'required'],
            [['person_user_relId', 'place_of_birth_relId', 'place_of_residence', 'ec_relId', 'job_app_relId', 'status', 'nationality', 'region', 'created_by', 'updated_by', 'parent_info_relId'], 'integer'],
            [['dob', 'doj', 'created_at', 'updated_at', 'retirement_date'], 'safe'],
            [['gender', 'profession_type', 'data', 'membership_category'], 'string'],
            [['ID_NO'], 'string', 'max' => 10],
            [['MIL_ID_NO'], 'string', 'max' => 8],
            [['first_name', 'middle_name', 'last_name', 'email', 'first_name_am', 'middle_name_am', 'last_name_am'], 'string', 'max' => 100],
            [['phone_home', 'phone_mobile', 'phone_office'], 'string', 'max' => 15],
            [['phone_office_ext'], 'string', 'max' => 5],
            [['registration_code'], 'string', 'max' => 40],
            [['profile_pic'], 'string', 'max' => 50],
            [['marital_status', 'dob_am'], 'string', 'max' => 35],
            [['ID_NO'], 'unique'],
            [['MIL_ID_NO'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['person_user_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['person_user_relId' => 'id']],
            [['place_of_birth_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AddressInfo::className(), 'targetAttribute' => ['place_of_birth_relId' => 'id']],
            [['place_of_residence'], 'exist', 'skipOnError' => true, 'targetClass' => AddressInfo::className(), 'targetAttribute' => ['place_of_residence' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['ec_relId'], 'exist', 'skipOnError' => true, 'targetClass' => EmergencyContactInfo::className(), 'targetAttribute' => ['ec_relId' => 'id']],
            [['region'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ID_NO' => Yii::t('rms', 'Id No'),
            'MIL_ID_NO' => Yii::t('rms', 'Mil Id No'),
            'person_user_relId' => Yii::t('rms', 'Person User Rel ID'),
            'place_of_birth_relId' => Yii::t('rms', 'Place Of Birth Rel ID'),
            'place_of_residence' => Yii::t('rms', 'Place Of Residence'),
            'first_name' => Yii::t('rms', 'First Name'),
            'middle_name' => Yii::t('rms', 'Middle Name'),
            'last_name' => Yii::t('rms', 'Last Name'),
            'dob' => Yii::t('rms', 'Dob'),
            'doj' => Yii::t('rms', 'Doj'),
            'gender' => Yii::t('rms', 'Gender'),
            'ec_relId' => Yii::t('rms', 'Ec Rel ID'),
            'profession_type' => Yii::t('rms', 'Profession Type'),
            'phone_home' => Yii::t('rms', 'Phone Home'),
            'phone_mobile' => Yii::t('rms', 'Phone Mobile'),
            'phone_office' => Yii::t('rms', 'Phone Office'),
            'phone_office_ext' => Yii::t('rms', 'Phone Office Ext'),
            'email' => Yii::t('rms', 'Email'),
            'job_app_relId' => Yii::t('rms', 'Job App Rel ID'),
            'status' => Yii::t('rms', 'Status'),
            'registration_code' => Yii::t('rms', 'Registration Code'),
            'nationality' => Yii::t('rms', 'Nationality'),
            'region' => Yii::t('rms', 'Region'),
            'profile_pic' => Yii::t('rms', 'Profile Pic'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
            'first_name_am' => Yii::t('rms', 'First Name Am'),
            'middle_name_am' => Yii::t('rms', 'Middle Name Am'),
            'last_name_am' => Yii::t('rms', 'Last Name Am'),
            'marital_status' => Yii::t('rms', 'Marital Status'),
            'dob_am' => Yii::t('rms', 'Dob Am'),
            'membership_category' => Yii::t('rms', 'Membership Category'),
            'retirement_date' => Yii::t('rms', 'Retirement Date'),
            'parent_info_relId' => Yii::t('rms', 'Parent Info Rel ID'),
        ];
    }

    public function getFullName(){
        return $this->first_name.' '.$this->middle_name.' '. $this->last_name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicPromotionHistories()
    {
        return $this->hasMany(AcademicPromotionHistory::className(), ['aph_employee_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicRankHistories()
    {
        return $this->hasMany(AcademicRankHistory::className(), ['acrh_employee_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattleFieldDatas()
    {
        return $this->hasMany(BattleFieldData::className(), ['emp_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildrenInfos()
    {
        return $this->hasMany(ChildrenInfo::className(), ['emp_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getElrEmployeeLeaveRecords()
    {
        return $this->hasMany(ElrEmployeeLeaveRecord::className(), ['person_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getElrEmployeeLeaveRecordEntries()
    {
        return $this->hasMany(ElrEmployeeLeaveRecordEntry::className(), ['person_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getElrEmployeeLeaveRecordEntries0()
    {
        return $this->hasMany(ElrEmployeeLeaveRecordEntry::className(), ['representative_person_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmploymentHistories()
    {
        return $this->hasMany(EmploymentHistory::className(), ['eeh_user_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvlnEvaluatorsLists()
    {
        return $this->hasMany(EvlnEvaluatorsList::className(), ['person_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvlnEvalueesLists()
    {
        return $this->hasMany(EvlnEvalueesList::className(), ['person_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoqAssignments()
    {
        return $this->hasMany(LoqAssignment::className(), ['person_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLorsquaDetails()
    {
        return $this->hasMany(LorsquaDetail::className(), ['employee_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedalPrizeDatas()
    {
        return $this->hasMany(MedalPrizeData::className(), ['emp_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMilitaryPenaltyHistories()
    {
        return $this->hasMany(MilitaryPenaltyHistory::className(), ['pen_emp_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMilitaryProfileHistories()
    {
        return $this->hasMany(MilitaryProfileHistory::className(), ['mph_emp_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMilitaryRankIrkenPromotionHistories()
    {
        return $this->hasMany(MilitaryRankIrkenPromotionHistory::className(), ['mip_emp_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMilitaryRewardHistories()
    {
        return $this->hasMany(MilitaryRewardHistory::className(), ['rwh_emp_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMleEvalees()
    {
        return $this->hasMany(MleEvalee::className(), ['person_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMleEvaluatorsLists()
    {
        return $this->hasMany(MleEvaluatorsList::className(), ['person_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentInfos()
    {
        return $this->hasMany(ParentInfo::className(), ['emp_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonUserRel()
    {
        return $this->hasOne(User::className(), ['id' => 'person_user_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaceOfBirthRel()
    {
        return $this->hasOne(AddressInfo::className(), ['id' => 'place_of_birth_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaceOfResidence()
    {
        return $this->hasOne(AddressInfo::className(), ['id' => 'place_of_residence']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEcRel()
    {
        return $this->hasOne(EmergencyContactInfo::className(), ['id' => 'ec_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion0()
    {
        return $this->hasOne(Region::className(), ['id' => 'region']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRmsUserLinks()
    {
        return $this->hasMany(RmsUserLink::className(), ['person_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransferAssignmentDatas()
    {
        return $this->hasMany(TransferAssignmentData::className(), ['emp_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['user_person_relId' => 'id']);
    }
}
