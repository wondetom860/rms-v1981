<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "course_offering".
 *
 * @property integer $id
 * @property integer $coff_cocat_relId
 * @property integer $coff_course_relId
 * @property integer $coff_acadYear_relId
 * @property integer $coff_group_relId
 * @property integer $coff_instr_relId
 * @property integer $coff_TA_relId
 * @property integer $coff_updated_by
 * @property string $coff_updated_at
 * @property integer $coff_created_by
 * @property string $coff_created_at
 *
 * @property SchoolYear $coffAcadYearRel
 * @property CourseCatalog $coffCocatRel
 * @property Course $coffCourseRel
 * @property User $coffCreatedBy
 * @property StudentGroupHistory $coffGroupRel
 * @property User $coffUpdatedBy
 * @property SemesterRegistration[] $semesterRegistrations
 */
class CourseOffering extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_offering';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // 'coff_group_relId', removed from the required list.
        // 'coff_instr_relId', 'coff_TA_relId', removed from the required list
        return [
            [['coff_cocat_relId', 'coff_course_relId', 'coff_acadYear_relId', 'coff_updated_by', 'coff_updated_at', 'coff_created_by', 'coff_created_at'], 'required'],
            [['coff_cocat_relId', 'coff_course_relId', 'coff_acadYear_relId', 'coff_group_relId', 'coff_instr_relId', 'coff_TA_relId', 'coff_updated_by', 'coff_created_by'], 'integer'],
            [['coff_updated_at', 'coff_created_at'], 'safe'],
            [['coff_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['coff_acadYear_relId' => 'id']],
            [['coff_cocat_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseCatalog::class, 'targetAttribute' => ['coff_cocat_relId' => 'id']],
            [['coff_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['coff_course_relId' => 'id']],
            [['coff_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['coff_created_by' => 'id']],
            [['coff_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => StudentGroupHistory::class, 'targetAttribute' => ['coff_group_relId' => 'id']],
            [['coff_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['coff_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'coff_cocat_relId' => Yii::t('rms', 'Course catalog No.'),
            'coff_course_relId' => Yii::t('rms', 'Course'),
            'coff_acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'coff_group_relId' => Yii::t('rms', 'Group'),
            'coff_instr_relId' => Yii::t('rms', 'Assigned Instructor'),
            'coff_TA_relId' => Yii::t('rms', 'Assigned Tech. Ass.'),
            'coff_updated_by' => Yii::t('rms', 'Updated By'),
            'coff_updated_at' => Yii::t('rms', 'Updated At'),
            'coff_created_by' => Yii::t('rms', 'Created By'),
            'coff_created_at' => Yii::t('rms', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoffAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'coff_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoffCocatRel()
    {
        return $this->hasOne(CourseCatalog::class, ['id' => 'coff_cocat_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoffCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'coff_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoffCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'coff_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoffGroupRel()
    {
        return $this->hasOne(StudentGroupHistory::class, ['id' => 'coff_group_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoffUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'coff_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrations()
    {
        return $this->hasMany(SemesterRegistration::class, ['semreg_coff_relId' => 'id']);
    }
}
