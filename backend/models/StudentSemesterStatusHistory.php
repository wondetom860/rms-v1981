<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "student_semester_status_history".
 *
 * @property integer $id
 * @property integer $sssh_std_relId
 * @property integer $sssh_acadYear_relId
 * @property integer $sssh_sem_relId
 * @property integer $sssh_clslvl_relId
 * @property integer $sssh_sssn_relId
 * @property integer $sssh_detault_sssn_relId
 * @property integer $sssh_created_by
 * @property integer $sssh_updated_by
 * @property string $sssh_created_at
 * @property string $sssh_updated_at
 *
 *
 * @property Student $ssshStdRel
 * @property Student $ssshDetaultSssnRel
 * @property SchoolYear $ssshAcadYearRel
 * @property Semester $ssshSemRel
 * @property ClassLevel $ssshClslvlRel
 * @property StudentSemesterStatusName $ssshSssnRel
 * @property User $ssshCreatedBy
 * @property User $ssshUpdatedBy
 */
class StudentSemesterStatusHistory extends \yii\db\ActiveRecord
{

    public $admission = null;
    public $program = null;
    public $group = null;
    public $dept = NULL;
    public $spec = NULL;
    public $department= NULL;
    public $_clslvl = NULL;
    public $status = null;
    public $acadYearId2 = NULL;
    public $semId2 = NULL;
    public $nat = NULL;
    public $spon = NULL;
    public $_gender = NULL;
    public $userType = NULL;


    public $limit = NULL;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'student_semester_status_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sssh_std_relId', 'sssh_acadYear_relId', 'sssh_sem_relId', 'sssh_clslvl_relId', 'sssh_sssn_relId', 'sssh_created_by', 'sssh_updated_by', 'sssh_created_at', 'sssh_updated_at'], 'required'],
            [['sssh_std_relId', '_clslvl','sssh_acadYear_relId', 'sssh_sem_relId', 'sssh_clslvl_relId', 'sssh_sssn_relId', 'sssh_detault_sssn_relId', 'sssh_created_by', 'sssh_updated_by'], 'integer'],
            [['sssh_created_at', 'sssh_updated_at'], 'safe'],
            [['sssh_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['sssh_std_relId' => 'id']],
            [['sssh_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['sssh_acadYear_relId' => 'id']],
            [['sssh_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['sssh_sem_relId' => 'id']],
            [['sssh_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['sssh_clslvl_relId' => 'id']],
            [['sssh_sssn_relId'], 'exist', 'skipOnError' => true, 'targetClass' => StudentSemesterStatusName::class, 'targetAttribute' => ['sssh_sssn_relId' => 'id']],
            [['sssh_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['sssh_created_by' => 'id']],
            [['sssh_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['sssh_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'sssh_std_relId' => Yii::t('rms', 'Student'),
            'sssh_acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'sssh_sem_relId' => Yii::t('rms', 'Semester'),
            'sssh_clslvl_relId' => Yii::t('rms', 'Class Level'),
            'sssh_sssn_relId' => Yii::t('rms', 'Semester Status'),
            'sssh_detault_sssn_relId' => Yii::t('rms', 'Default Sem. Status'),
            'sssh_approved' => Yii::t('rms', 'Status'),
            'sssh_approved_by' => Yii::t('rms', 'Created BY'),
            'sssh_approved_at' => Yii::t('rms', 'Created At'),
            'sssh_created_by' => Yii::t('rms', 'Created By'),
            'sssh_updated_by' => Yii::t('rms', 'Updated By'),
            'sssh_created_at' => Yii::t('rms', 'Created At'),
            'sssh_updated_at' => Yii::t('rms', 'Updated At'),
            'admission' => Yii::t('rms', 'Admission'),
            'program' => Yii::t('rms', 'Program'),
            'dept' => Yii::t('rms', 'Department'),
            'spec' => Yii::t('rms', 'Specialization'),
        ];
    }

    public function getModifiedStatus($keySrd2Model){
        return $this->find()
            ->where([
                'sssh_std_relId' => $keySrd2Model->srd2SemregRel->semreg_std_relId,
                'sssh_acadYear_relId' => $keySrd2Model->srd2SemregRel->semreg_acadYear_relId,
                'sssh_sem_relId' => $keySrd2Model->srd2SemregRel->semreg_semester_relId,
                'sssh_clslvl_relId' => $keySrd2Model->srd2SemregRel->semreg_clslvl_relId,
                'sssh_approved' => 1,
            ])
            ->one();
    }

    public function getRegistrarAlert(){
        return $this->find()
            ->where([
                'sssh_approved'=>NULL
            ])
            ->count();
    }

    public function getInstDept(){
        $dept = NULL;
        if (Yii::$app->user->can("DepartmentHead")) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
            $dept = $inst->instDepRel;
        }else{
           $dept = \backend\models\Department::find()->all();
        }

        return \yii\helpers\ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsshStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'sssh_std_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsshAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'sssh_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getssshDetaultSssnRel()
    {
        return $this->hasOne(StudentSemesterStatusName::class, ['id' => 'sssh_detault_sssn_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsshSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'sssh_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsshClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'sssh_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsshSssnRel()
    {
        return $this->hasOne(StudentSemesterStatusName::class, ['id' => 'sssh_sssn_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsshCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'sssh_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsshUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'sssh_updated_by']);
    }
}
