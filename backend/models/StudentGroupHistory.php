<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "student_group_history".
 *
 * @property integer $id
 * @property integer $sgh_stud_relId
 * @property integer $sgh_group_relId
 * @property integer $sgh_acadYear_relId
 * @property string $sgh_semester_relId
 * @property integer $sgh_classlevel
 * @property integer $sgh_created_by
 * @property string $sgh_created_at
 * @property integer $sgh_updated_by
 * @property string $sgh_updated_at
 *
 * @property CourseOffering[] $courseOfferings
 * @property SemesterRegistration[] $semesterRegistrations
 * @property SemesterRegistration[] $semesterRegistrations0
 * @property Student[] $students
 * @property SchoolYear $sghAcadYearRel
 * @property ClassLevel $sghClasslevel
 * @property User $sghCreatedBy
 * @property StudentGroup $sghGroupRel
 * @property Student $sghStudRel
 * @property User $sghUpdatedBy
 */
class StudentGroupHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    // These are not actually stored to database table, but we will use them for filtering students while grouping...
    public $sgh_adm_relId = null;
    public $sgh_prog_relId = null;
    public $sgh_dept_relId = null;
    public $sgh_spec_relId = null;

    // for passing the list as an array:
    public $sgh_stdIds = NULL;
    
    public static function tableName()
    {
        return 'student_group_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sgh_stud_relId','sgh_semester_relId', 'sgh_group_relId', 'sgh_acadYear_relId', 'sgh_classlevel', 'sgh_created_by', 'sgh_updated_by'], 'integer'],
            [[ 'sgh_classlevel', 'sgh_created_at', 'sgh_updated_at', 'sgh_group_relId'], 'required'],
            [['sgh_created_at', 'sgh_updated_at'], 'safe'],
            [['sgh_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::className(), 'targetAttribute' => ['sgh_acadYear_relId' => 'id']],
            [['sgh_classlevel'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::className(), 'targetAttribute' => ['sgh_classlevel' => 'id']],
            [['sgh_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['sgh_created_by' => 'id']],
            [['sgh_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => StudentGroup::className(), 'targetAttribute' => ['sgh_group_relId' => 'id']],
            [['sgh_semester_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::className(), 'targetAttribute' => ['sgh_semester_relId' => 'id']],
            [['sgh_stud_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['sgh_stud_relId' => 'id']],
            [['sgh_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['sgh_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'sgh_stud_relId' => Yii::t('rms', 'Student Full Name'),
            'sgh_group_relId' => Yii::t('rms', 'Group'),
            'sgh_acadYear_relId' => Yii::t('rms', 'Academic year'),
            'sgh_semester_relId' => Yii::t('rms', 'Semester'),
            'sgh_classlevel' => Yii::t('rms', 'Classlevel'),
            'sgh_created_by' => Yii::t('rms', 'Created By'),
            'sgh_created_at' => Yii::t('rms', 'Created At'),
            'sgh_updated_by' => Yii::t('rms', 'Updated By'),
            'sgh_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseOfferings()
    {
        return $this->hasMany(CourseOffering::className(), ['coff_group_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrations()
    {
        return $this->hasMany(SemesterRegistration::className(), ['semreg_addw_group' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrations0()
    {
        return $this->hasMany(SemesterRegistration::className(), ['semreg_group_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(Student::className(), ['std_group_realId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSghAcadYearRel()
    {
        return $this->hasOne(SchoolYear::className(), ['id' => 'sgh_acadYear_relId']);
    }

/**
     * @return \yii\db\ActiveQuery
     */
    public function getSghSemesterRel()
    {
        return $this->hasOne(Semester::className(), ['id' => 'sgh_semester_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSghClasslevel()
    {
        return $this->hasOne(ClassLevel::className(), ['id' => 'sgh_classlevel']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSghCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'sgh_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSghGroupRel()
    {
        return $this->hasOne(StudentGroup::className(), ['id' => 'sgh_group_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSghStudRel()
    {
        return $this->hasOne(Student::className(), ['id' => 'sgh_stud_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSghUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'sgh_updated_by']);
    }
}
