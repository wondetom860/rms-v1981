<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "program".
 *
 * @property integer $id
 * @property integer $program_adm_relId
 * @property string $program_name
 * @property string $total_crdt_hrs//
 * @property integer $program_status
 * @property string $program_desc
 * @property string $program_duration
 * @property integer $program_created_by
 * @property string $program_created_at
 * @property integer $program_updated_by
 * @property string $program_updated_at
 *
 * @property CourseCatalog[] $courseCatalogs
 * @property Program $programAdmRel
 * @property Program[] $programs
 * @property User $programCreatedBy
 * @property User $programUpdatedBy
 * @property Student[] $students
 */
class Program extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'program';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['program_adm_relId', 'program_name', 'program_created_by', 'program_created_at', 'program_updated_by', 'program_updated_at','program_duration'], 'required'],
            [['program_adm_relId', 'program_status','program_duration', 'program_created_by', 'program_updated_by','total_crdt_hrs'], 'integer'],
            [['program_desc','program_prefix'], 'string'],
            [['program_created_at', 'program_updated_at'], 'safe'],
            [['program_name'], 'string', 'max' => 40],
            [['program_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['program_adm_relId' => 'id']],
            [['program_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['program_created_by' => 'id']],
            [['program_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['program_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'program_adm_relId' => Yii::t('rms', 'Admission Type'),
            'program_name' => Yii::t('rms', 'Program Name'),
            'program_status' => Yii::t('rms', 'Program Status'),
            'program_desc' => Yii::t('rms', 'Program Desc'),
            'total_crdt_hrs' => Yii::t('rms', 'Total Credit Hrs'),
            'program_duration' => Yii::t('rms', 'Program Duration in Year'),
            'program_created_by' => Yii::t('rms', 'Program Created By'),
            'program_created_at' => Yii::t('rms', 'Program Created At'),
            'program_updated_by' => Yii::t('rms', 'Program Updated By'),
            'program_updated_at' => Yii::t('rms', 'Program Updated At'),
        ];
    }

    public function getDegreeNomenclature(){
        return $this->programAdmRel->admission_name.' '.$this->program_name;
    }

    public function getDegreeNomenclature_am(){
        return $this->programAdmRel->admission_name.' '.$this->program_name;
    }

    public function getProgramHeader(){
        return $this->programAdmRel->admission_name[0].$this->program_prefix;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseCatalogs()
    {
        return $this->hasMany(CourseCatalog::class, ['cocat_program_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramAdmRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'program_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrograms()
    {
        return $this->hasMany(Program::class, ['program_adm_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'program_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'program_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(Student::class, ['std_prog_relId' => 'id']);
    }
}
