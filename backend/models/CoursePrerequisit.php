<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "course_prerequisit".
 *
 * @property integer $id
 * @property integer $preq_adm_relId
 * @property integer $preq_prog_relId
 * @property integer $preq_dept_relId
 * @property integer $preq_spec_relId
 * @property integer $preq_main_course_relId
 * @property integer $preq_preq_course_relId
 * @property integer $preq_created_by
 * @property string $preq_created_at
 * @property integer $preq_updated_by
 * @property string $preq_updated_at
 * @property string $remark
 *
 * @property Course $preqMainCourseRel
 * @property Course $preqPreqCourseRel
 * @property User $preqCreatedBy
 * @property Admission $preqAdmRel
 * @property Program $preqProgRel
 * @property Department $preqDeptRel
 * @property Specialization $preqSpecRel
 * @property User $preqUpdatedBy
 */
class CoursePrerequisit extends \yii\db\ActiveRecord
{
    public $preqFilterDept = null;
    /**
     * @inheritdoc
     */
    public static function tableName()
    { 
        return 'course_prerequisit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['preq_adm_relId', 'preq_prog_relId', 'preq_dept_relId', 'preq_spec_relId', 'preq_main_course_relId', 'preq_preq_course_relId', 'preq_created_by', 'preq_updated_by'], 'integer'],
            [['preq_main_course_relId', 'preq_created_by', 'preq_created_at', 'preq_updated_by', 'preq_updated_at'], 'required'],

            [['preq_created_at', 'preq_updated_at',], 'safe'],

            [['remark'], 'string'],

            [['preq_main_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['preq_main_course_relId' => 'id']],
            [['preq_preq_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['preq_preq_course_relId' => 'id']],
            [['preq_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['preq_created_by' => 'id']],
            [['preq_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['preq_adm_relId' => 'id']],
            [['preq_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['preq_prog_relId' => 'id']],
            [['preq_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['preq_dept_relId' => 'id']],
            [['preq_spec_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Specialization::class, 'targetAttribute' => ['preq_spec_relId' => 'id']],
            [['preq_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['preq_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'preqAdmRel.admission_name' => Yii::t('rms', 'Admission'),
            'preq_adm_relId' => Yii::t('rms', 'Admission'),
            'preq_prog_relId' => Yii::t('rms', 'Program'),
            'preqProgRel.program_name' => Yii::t('rms', 'Program'),
            'preq_dept_relId' => Yii::t('rms', 'Department'),
            'preqDeptRel.dept_name' => Yii::t('rms', 'Department'),
            'preq_spec_relId' => Yii::t('rms', 'Specialization'),
            'preqSpecRel.spec_name' => Yii::t('rms', 'Specialization'),
            'preq_main_course_relId' => Yii::t('rms', 'Main Course'),
            'preqMainCourseRel.course_title' => Yii::t('rms', 'Main Course'),
            'preq_preq_course_relId' => Yii::t('rms', 'Prerequisit Course'),
            'preqPreqCourseRel.course_title' => Yii::t('rms', 'Prerequisit Course'),
            'preq_created_by' => Yii::t('rms', 'Created By'),
            'preq_created_at' => Yii::t('rms', 'Created At'),
            'preq_updated_by' => Yii::t('rms', 'Updated By'),
            'preq_updated_at' => Yii::t('rms', 'Updated At'),
            'remark' => Yii::t('rms', 'Remark'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreqMainCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'preq_main_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreqPreqCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'preq_preq_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreqCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'preq_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreqAdmRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'preq_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreqProgRel()
    {
        return $this->hasOne(Program::class, ['id' => 'preq_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreqDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'preq_dept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreqSpecRel()
    {
        return $this->hasOne(Specialization::class, ['id' => 'preq_spec_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreqUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'preq_updated_by']);
    }
}
