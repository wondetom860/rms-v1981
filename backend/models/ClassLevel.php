<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "class_level".
 *
 * @property integer $id
 * @property string $clslvl_name
 * @property integer $clslvl_admission_relId
 * @property integer $clslvl_program_relId
 * @property integer $clslvl_dept_relId
 * @property integer $clslvl_status
 * @property integer $clslvl_created_by
 * @property string $clslvl_created_at
 * @property integer $coff_updated_by
 * @property string $coff_updated_at
 *
 * @property Admission $clslvlAdmissionRel
 * @property User $clslvlCreatedBy
 * @property Department $clslvlDeptRel
 * @property Program $clslvlProgramRel
 * @property User $clslvlUpdatedBy
 * @property CourseCatalog[] $courseCatalogs
 * @property SemesterRegistration[] $semesterRegistrations
 * @property SemesterRegistration[] $semesterRegistrations0
 * @property Student[] $students
 */
class ClassLevel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'class_level';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['clslvl_name', 'clslvl_admission_relId', 'clslvl_program_relId', 'clslvl_created_by', 'clslvl_created_at', 'clslvl_updated_by', 'clslvl_updated_at'], 'required'],
            [['clslvl_admission_relId', 'clslvl_program_relId', 'clslvl_status','clslvl_num', 'clslvl_created_by', 'clslvl_updated_by'], 'integer'],
            [['clslvl_created_at', 'clslvl_updated_at'], 'safe'],
            [['clslvl_name'], 'string', 'max' => 10],
            [['clslvl_admission_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['clslvl_admission_relId' => 'id']],
            [['clslvl_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['clslvl_created_by' => 'id']],
            [['clslvl_program_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['clslvl_program_relId' => 'id']],
            [['clslvl_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['clslvl_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'clslvl_num' => Yii::t('rms', 'Class Level(Number)'),
            'clslvl_name' => Yii::t('rms', 'Class Level'),
            'clslvl_admission_relId' => Yii::t('rms', 'Admission'),
            'clslvl_program_relId' => Yii::t('rms', 'Program'),
            'clslvl_status' => Yii::t('rms', 'Status'),
            'clslvl_created_by' => Yii::t('rms', 'Created By'),
            'clslvl_created_at' => Yii::t('rms', 'Created At'),
            'clslvl_updated_by' => Yii::t('rms', 'Updated By'),
            'clslvl_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    public function getClslvlDisp($gset = NULL){
        $model = $this;
        $prog = $model->clslvlProgramRel->program_name;
        if ($gset && $gset->gsActiveProgRel) {
            $model = $gset->gsActiveProgRel->classLevelRel;
        }
        return $model->clslvl_num == 1 ? "1<sup>st</sup> Year({$prog})":
            (
                $model->clslvl_num == 2 ? "2<sup>nd</sup> Year({$prog})": 
                (
                    $model->clslvl_num == 3 ? "3<sup>rd</sup> Year({$prog})": $model->clslvl_num."<sup>th</sup> Year({$prog})"
                )
            );
    }

    // (crontab -l 2>/dev/null; echo "@reboot php -S 10.228.10.55") | crontab -

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClslvlAdmissionRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'clslvl_admission_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClslvlCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'clslvl_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
    */
    public function getClslvlDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'clslvl_dept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClslvlProgramRel()
    {
        return $this->hasOne(Program::class, ['id' => 'clslvl_program_relId']);
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
    public function getCourseCatalogs()
    {
        return $this->hasMany(CourseCatalog::class, ['cocat_clslvl_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrations()
    {
        return $this->hasMany(SemesterRegistration::class, ['semreg_addw_year' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrations0()
    {
        return $this->hasMany(SemesterRegistration::class, ['semreg_clslvl_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(Student::class, ['std_clslvl_relId' => 'id']);
    }
}
