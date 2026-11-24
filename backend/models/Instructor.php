<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "instructor".
 *
 * @property integer $id
 * @property integer $inst_user_relId
 * @property integer $inst_dep_relId
 * @property integer $inst_acq_reIId
 * @property integer $inst_acr_relId
 * @property integer $inst_active
 * @property integer $inst_name
 * @property integer $employment_type_relId
 * @property integer $inst_created_by
 * @property string $inst_created_at
 * @property string $inst_updated_at
 * @property integer $inst_updated_by
 *
 * @property DepartmentHead[] $departmentHeads
 * @property EmploymentType $employmentTypeRel
 * @property AcademicQualification $instAcqReI
 * @property AcademicRank $instAcrRel
 * @property User $instCreatedBy
 * @property Department $instDepRel
 * @property User $instUpdatedBy
 * @property User $instUserRel
 * 
 */
class Instructor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $instUserRelId = null;
    public $instName, $inst_name;
    public static function tableName()
    {
        return 'instructor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inst_user_relId', 'inst_dep_relId', 'inst_acq_relId', 'inst_acr_relId', 'inst_active', 'inst_created_by', 'inst_updated_by', 'employment_type_relId'], 'integer'],
            [['inst_acq_relId', 'inst_acr_relId', 'inst_created_by', 'inst_created_at', 'inst_updated_at', 'inst_updated_by'], 'required'],
            [['inst_created_at', 'inst_updated_at', 'inst_name'], 'safe'],
            [['inst_acq_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcademicQualification::class, 'targetAttribute' => ['inst_acq_relId' => 'id']],
            [['employment_type_relId'], 'exist', 'skipOnError' => true, 'targetClass' => EmploymentType::class, 'targetAttribute' => ['employment_type_relId' => 'id']],
            [['inst_acr_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcademicRank::class, 'targetAttribute' => ['inst_acr_relId' => 'id']],
            [['inst_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['inst_created_by' => 'id']],
            [['inst_dep_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['inst_dep_relId' => 'id']],
            [['inst_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['inst_updated_by' => 'id']],
            [['inst_user_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['inst_user_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'employment_type_relId' => Yii::t('rms', 'Employment'),
            'inst_user_relId' => Yii::t('rms', 'Full Name'),
            'inst_dep_relId' => Yii::t('rms', 'Department'),
            'inst_acq_reIId' => Yii::t('rms', 'Acad. Qualification'),
            'inst_acr_relId' => Yii::t('rms', 'Acad. Rank'),
            'inst_active' => Yii::t('rms', 'Active Status'),
            'inst_created_by' => Yii::t('rms', 'Inst. Account Created By'),
            'inst_created_at' => Yii::t('rms', 'Inst Account Created At'),
            'inst_updated_at' => Yii::t('rms', 'Inst Account Updated At'),
            'inst_updated_by' => Yii::t('rms', 'Inst Account Updated By'),
        ];
    }

    public function getAdvisingGsetIds()
    {
        $semesterAdvisorModel = new SemesterAdvisor();
        return $semesterAdvisorModel->getAdvisingGsetIds($this->id);
    }

    public function getAcqQualDisp()
    {
        // echo 'kkl';
        return $this->instAcqRel && !empty($this->instAcqRel->acq_name) ? $this->instAcqRel->acq_name : null;
    }

    public function getAcrRankDisp()
    {
        // echo 'kkl';
        return $this->instAcrRel && !empty($this->instAcrRel->acr_name) ? $this->instAcrRel->acr_name : null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentHeads()
    {
        return $this->hasMany(DepartmentHead::class, ['dh_inst_relId' => 'id']);
    }

    public function getName($userId)
    {
        if (!is_null($userId)) {
            $user = User::findOne($userId);
            return $user->first_name . ' ' . $user->middle_name;
        }
        return '';
    }

    public function getFullName()
    {
        return $this->instUserRel->getFullName();
        // return ucfirst(strtolower($user->first_name))." ".ucfirst(strtolower($user->middle_name));
    }

    public function getFullName_av()
    {
        return $this->instUserRel->getFullName_av();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstAcqRel()
    {
        return $this->hasOne(AcademicQualification::class, ['id' => 'inst_acq_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmploymentTypeRel()
    {
        return $this->hasOne(EmploymentType::class, ['id' => 'employment_type_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstAcrRel()
    {
        return $this->hasOne(AcademicRank::class, ['id' => 'inst_acr_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'inst_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstDepRel()
    {
        return $this->hasOne(Department::class, ['id' => 'inst_dep_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'inst_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstUserRel()
    {
        return $this->hasOne(User::class, ['id' => 'inst_user_relId']);
    }

    public function getInstAdvisions()
    {
        return $this->hasMany(SemesterAdvisor::class, ['sa_inst_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery|array
     */
    public function getCourseOffering2Details($acadYear = null, $sem = null)
    {
        if ($acadYear) {
            return $this->hasMany(\backend\models\CourseOffering2Detail::class, ['coff2det_inst_relId' => 'id'])
                ->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relid')
                ->andFilterWhere(['coff2_acadYear_relId' => $acadYear, 'coff2_sem_relId' => $sem])
                ->all();
        }
        return \backend\models\CourseOffering2Detail::find()
            ->where([
                'OR',
                ['coff2det_inst_relId' => $this->id],
                ['coff2det_TA_relId' => $this->id]
            ]);
        // return $this->hasMany(\backend\models\CourseOffering2Detail::class, ['coff2det_inst_relId' => 'id']);
    }
}
