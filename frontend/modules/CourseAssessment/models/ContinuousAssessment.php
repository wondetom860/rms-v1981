<?php

namespace frontend\modules\CourseAssessment\models;

use Yii;
use \backend\models\User;
use \backend\models\SemesterRegistration2;
use \backend\models\Student;
use \backend\models\CourseOffering2Detail;

/**
 * This is the model class for table "continuous_assessment2".
 *
 * @property int $id
 * @property int $ca_std_relid
 * @property int $ca_semreg_relId
 * @property int $ca_markAssignment_relId
 * @property int $ca_catagorySetup_relId
 * @property float|null $ca_markObtained
 * @property int $ca_coff_relId
 * @property int $ca_created_by
 * @property string $ca_created_at
 * @property int|null $ca_updated_by
 * @property string|null $ca_updated_at
 *
 * @property ContAssessCatagory2 $caCatagorySetupRel
 * @property CourseOffering2Detail $caCoffRel
 * @property InstanceMarkAssignment $caMarkAssignmentRel
 * @property SemesterRegistration2 $caSemregRel
 * @property Student $caStdRel
 * @property User $caCreatedBy
 * @property User $caUpdatedBy
 */
class ContinuousAssessment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'continuous_assessment2';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ca_std_relid', 'ca_semreg_relId', 'ca_markAssignment_relId', 'ca_catagorySetup_relId', 'ca_coff_relId', 'ca_created_by', 'ca_created_at'], 'required'],
            [['ca_std_relid', 'ca_semreg_relId', 'ca_markAssignment_relId', 'ca_catagorySetup_relId', 'ca_coff_relId', 'ca_created_by', 'ca_updated_by'], 'integer'],
            [['ca_markObtained'], 'number'],
            [['ca_created_at', 'ca_updated_at'], 'safe'],
            [['ca_catagorySetup_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ContAssessCatagory::class, 'targetAttribute' => ['ca_catagorySetup_relId' => 'id']],
            [['ca_coff_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2Detail::class, 'targetAttribute' => ['ca_coff_relId' => 'id']],
            [['ca_markAssignment_relId'], 'exist', 'skipOnError' => true, 'targetClass' => InstanceMarkAssignment::class, 'targetAttribute' => ['ca_markAssignment_relId' => 'id']],
            [['ca_semreg_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterRegistration2::class, 'targetAttribute' => ['ca_semreg_relId' => 'id']],
            [['ca_std_relid'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['ca_std_relid' => 'id']],
            [['ca_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ca_created_by' => 'id']],
            [['ca_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ca_updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ca_std_relid' => Yii::t('rms', 'Ca Std Relid'),
            'ca_semreg_relId' => Yii::t('rms', 'Ca Semreg Rel ID'),
            'ca_markAssignment_relId' => Yii::t('rms', 'Ca Mark Assignment Rel ID'),
            'ca_catagorySetup_relId' => Yii::t('rms', 'Ca Catagory Setup Rel ID'),
            'ca_markObtained' => Yii::t('rms', 'Ca Mark Obtained'),
            'ca_coff_relId' => Yii::t('rms', 'Ca Coff Rel ID'),
            'ca_created_by' => Yii::t('rms', 'Ca Created By'),
            'ca_created_at' => Yii::t('rms', 'Ca Created At'),
            'ca_updated_by' => Yii::t('rms', 'Ca Updated By'),
            'ca_updated_at' => Yii::t('rms', 'Ca Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaCatagorySetupRel()
    {
        return $this->hasOne(ContAssessCatagory::class, ['id' => 'ca_catagorySetup_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaCoffRel()
    {
        return $this->hasOne(CourseOffering2Detail::class, ['id' => 'ca_coff_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaMarkAssignmentRel()
    {
        return $this->hasOne(InstanceMarkAssignment::class, ['id' => 'ca_markAssignment_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaSemregRel()
    {
        return $this->hasOne(SemesterRegistration2::class, ['id' => 'ca_semreg_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'ca_std_relid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ca_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ca_updated_by']);
    }
}
