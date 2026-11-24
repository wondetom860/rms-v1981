<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "specialization".
 *
 * @property integer $id
 * @property integer $spec_adm_relId
 * @property integer $spec_prog_relId
 * @property integer $spec_dep_relId
 * @property string $spec_name
 * @property string $spec_desc
 * @property string $spec_code
 * @property integer $spec_status
 * @property integer $spec_created_by
 * @property string $spec_created_at
 * @property string $spec_updated_at
 * @property integer $spec_updated_by
 *
 * @property CourseCatalog[] $courseCatalogs
 * @property Admission $specAdmRel
 * @property User $specCreatedBy
 * @property Department $specDepRel
 * @property Program $specProgRel
 * @property User $specUpdatedBy
 * @property Student[] $students
 */
class Specialization extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'specialization';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spec_adm_relId', 'spec_prog_relId', 'spec_dep_relId', 'spec_name', 'spec_created_by', 'spec_created_at', 'spec_updated_at', 'spec_updated_by'], 'required'],
            [['spec_adm_relId', 'spec_prog_relId', 'spec_dep_relId', 'spec_status', 'spec_created_by', 'spec_updated_by'], 'integer'],
            [['spec_desc','spec_code'], 'string'],
            [['spec_created_at', 'spec_updated_at'], 'safe'],
            [['spec_name'], 'string', 'max' => 250],
            [['spec_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['spec_adm_relId' => 'id']],
            [['spec_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['spec_created_by' => 'id']],
            [['spec_dep_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['spec_dep_relId' => 'id']],
            [['spec_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['spec_prog_relId' => 'id']],
            [['spec_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['spec_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'spec_code' => Yii::t('rms', 'Code'),
            'spec_adm_relId' => Yii::t('rms', 'Admission'),
            'spec_prog_relId' => Yii::t('rms', 'Program'),
            'spec_dep_relId' => Yii::t('rms', 'Department'),
            'spec_name' => Yii::t('rms', 'Specialization/Section Name'),
            'spec_desc' => Yii::t('rms', 'Specialization Descrption'),
            'spec_status' => Yii::t('rms', 'Specialization Status'),
            'spec_created_by' => Yii::t('rms', 'Created By'),
            'spec_created_at' => Yii::t('rms', 'Created At'),
            'spec_updated_at' => Yii::t('rms', 'Updated At'),
            'spec_updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseCatalogs()
    {
        return $this->hasMany(CourseCatalog::class, ['cocat_spec_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecAdmRel()
    {
        // if (!Yii::$app->user->can('Admin')) {
        //     echo "<p class='bg-danger'> Invalid Access!! (::)</p>";die();
        // }
        return $this->hasOne(Admission::class, ['id' => 'spec_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'spec_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecDepRel()
    {
        return $this->hasOne(Department::class, ['id' => 'spec_dep_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecProgRel()
    {
        return $this->hasOne(Program::class, ['id' => 'spec_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'spec_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(Student::class, ['std_spec_relId' => 'id']);
    }
}
