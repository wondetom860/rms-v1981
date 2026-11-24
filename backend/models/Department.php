<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "department".
 *
 * @property integer $id
 * @property integer $dept_head
 * @property string $dept_name
 * @property string $dept_location
 * @property string $dept_phone
 * @property string $dept_code
 * @property string $dept_id_prefix
 * @property integer $dept_created_by
 * @property string $dept_created_at
 * @property string $dept_updated_at
 * @property integer $dept_updated_by
 * @property integer $status
 *
 * @property Course[] $courses
 * @property CourseCatalog[] $courseCatalogs
 * @property DepartmentHead[] $departmentHeads
 * @property Instructor[] $instructors
 * @property Student[] $students
 */
class Department extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'department';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dept_created_by', 'dept_updated_by','status'], 'integer'],
            [['dept_name', 'dept_id_prefix','dept_location', 'dept_phone', 'dept_code', 'dept_created_by', 'dept_created_at', 'dept_updated_at', 'dept_updated_by'], 'required'],
            [['dept_created_at', 'dept_updated_at'], 'safe'],
            [['dept_name', 'dept_location'], 'string', 'max' => 100],
            [['dept_phone'], 'string', 'max' => 25],
            [['dept_code'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'dept_head' => Yii::t('rms', 'Department Head'),
            'dept_id_prefix' => Yii::t('rms', 'Prefix'),
            'dept_name' => Yii::t('rms', 'Name'),
            'dept_location' => Yii::t('rms', 'Location'),
            'dept_phone' => Yii::t('rms', 'Phone'),
            'dept_code' => Yii::t('rms', 'Code'),
            'status' => Yii::t('rms', 'Status'),
            'dept_created_by' => Yii::t('rms', 'Created By'),
            'dept_created_at' => Yii::t('rms', 'Created At'),
            'dept_updated_at' => Yii::t('rms', 'Updated At'),
            'dept_updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourses()
    {
        return $this->hasMany(Course::class, ['course_dept_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHod()
    {
        return $this->hasOne(DepartmentHead::class, ['dh_dept_relId' => 'id'])->andFilterWhere(['dh_status' => 1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseCatalogs()
    {
        return $this->hasMany(CourseCatalog::class, ['cocat_dept_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentHeads()
    {
        return $this->hasMany(DepartmentHead::class, ['dh_dept_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstructors()
    {
        return $this->hasMany(Instructor::class, ['inst_dep_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(Student::class, ['std_dept_relId' => 'id']);
    }

    public function getDeptCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'dept_created_by']);
    }

    public function getDeptUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'dept_updated_by']);
    }
}
