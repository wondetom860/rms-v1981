<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "semester".
 *
 * @property integer $id
 * @property string $semester_name
 * @property integer $semester_prog_relId
 * @property integer $semester_status
 * @property string $semester_desc
 * @property integer $semester_created_by
 * @property string $semester_created_at
 * @property integer $semester_updated_by
 * @property string $semester_updated_at
 *
 * @property AcademicCalander[] $academicCalanders
 * @property CourseCatalog[] $courseCatalogs
 * @property User $semesterCreatedBy
 * @property Program $semesterProgRel
 * @property User $semesterUpdatedBy
 * @property SemesterRegistration[] $semesterRegistrations
 */
class Semester extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['semester_name', 'semester_created_by', 'semester_created_at', 'semester_updated_by', 'semester_updated_at'], 'required'],
            [['semester_status', 'semester_created_by', 'semester_updated_by'], 'integer'],
            [['semester_desc'], 'string'],
            [['semester_created_at', 'semester_updated_at'], 'safe'],
            [['semester_name'], 'string', 'max' => 100],
            [['semester_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['semester_created_by' => 'id']],

            [['semester_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['semester_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'semester_name' => Yii::t('rms', 'Semester Name'),
            'semester_status' => Yii::t('rms', 'Semester Status'),
            'semester_desc' => Yii::t('rms', 'Semester Desc'),
            'semester_created_by' => Yii::t('rms', 'Semester Created By'),
            'semester_created_at' => Yii::t('rms', 'Semester Created At'),
            'semester_updated_by' => Yii::t('rms', 'Semester Updated By'),
            'semester_updated_at' => Yii::t('rms', 'Semester Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicCalanders()
    {
        return $this->hasMany(AcademicCalander::class, ['acad_sem_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseCatalogs()
    {
        return $this->hasMany(CourseCatalog::class, ['cocat_sem_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'semester_created_by']);
    }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getSemesterProgRel()
    // {
    //     return $this->hasOne(Program::class, ['id' => 'semester_prog_relId']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'semester_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrations()
    {
        return $this->hasMany(SemesterRegistration::class, ['semreg_sem_relId' => 'id']);
    }
}
