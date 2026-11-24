<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "semester_registration_controlac".
 *
 * @property integer $id
 * @property integer $src_acadYear_relId
 * @property integer $src_adm_relId
 * @property integer $src_prog_relId
 * @property integer $src_sem_relId
 * @property integer $src_clslvl_relId
 * @property integer $src_dept_relId
 * @property integer $src_spec_relId
 * @property integer $src_group_relId
 * @property double $src_max_crdthr
 * @property double $src_min_crdthr
 * @property string $src_remark
 * @property integer $src_created_by
 * @property string $src_created_at
 * @property string $src_updated_at
 * @property integer $src_updated_by
 *
 * @property SchoolYear $srcAcadYearRel
 * @property WGroupSetup $srcGroupRel
 * @property DepartmentHead $srcDeptRel
 * @property Admission $srcAdmRel
 * @property Program $srcProgRel
 * @property Semester $srcSemRel
 * @property ClassLevel $srcClslvlRel
 * @property User $srcCreatedBy
 * @property User $srcUpdatedBy
 * @property Specialization $srcSpecRel
 */
class SemesterRegistrationControlac extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_registration_controlac';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['src_acadYear_relId', 'src_adm_relId', 'src_prog_relId', 'src_sem_relId', 'src_clslvl_relId', 'src_max_crdthr', 'src_min_crdthr', 'src_created_by', 'src_created_at', 'src_updated_at'], 'required'],
            [['src_acadYear_relId', 'src_adm_relId', 'src_prog_relId', 'src_sem_relId', 'src_clslvl_relId', 'src_dept_relId', 'src_spec_relId', 'src_group_relId', 'src_created_by', 'src_updated_by'], 'integer'],
            [['src_max_crdthr', 'src_min_crdthr'], 'number'],
            [['src_remark'], 'string'],
            [['src_created_at', 'src_updated_at'], 'safe'],
            [['src_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['src_acadYear_relId' => 'id']],
            [['src_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['src_group_relId' => 'id']],
            [['src_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['src_dept_relId' => 'id']],
            [['src_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['src_adm_relId' => 'id']],
            [['src_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['src_prog_relId' => 'id']],
            [['src_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['src_sem_relId' => 'id']],
            [['src_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['src_clslvl_relId' => 'id']],
            [['src_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['src_created_by' => 'id']],
            [['src_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['src_updated_by' => 'id']],
            [['src_spec_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Specialization::class, 'targetAttribute' => ['src_spec_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'src_acadYear_relId' => Yii::t('rms', 'Acad Year'),
            'src_adm_relId' => Yii::t('rms', 'Admission'),
            'src_prog_relId' => Yii::t('rms', 'Program'),
            'src_sem_relId' => Yii::t('rms', 'Semester'),
            'src_clslvl_relId' => Yii::t('rms', 'ClassLevel'),
            'src_dept_relId' => Yii::t('rms', 'Department'),
            'src_spec_relId' => Yii::t('rms', 'Specialization'),
            'src_group_relId' => Yii::t('rms', 'Group'),
            'src_max_crdthr' => Yii::t('rms', 'Max Crdthr'),
            'src_min_crdthr' => Yii::t('rms', 'Min Crdthr'),
            'src_remark' => Yii::t('rms', 'Remark'),
            'src_created_by' => Yii::t('rms', 'Src Created By'),
            'src_created_at' => Yii::t('rms', 'Src Created At'),
            'src_updated_at' => Yii::t('rms', 'Src Updated At'),
            'src_updated_by' => Yii::t('rms', 'Src Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'src_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcGroupRel()
    {
        return $this->hasOne(WGroupSetup::class, ['id' => 'src_group_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'src_dept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcAdmRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'src_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcProgRel()
    {
        return $this->hasOne(Program::class, ['id' => 'src_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'src_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'src_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'src_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'src_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcSpecRel()
    {
        return $this->hasOne(Specialization::class, ['id' => 'src_spec_relId']);
    }
}
