<?php

namespace backend\models;

use frontend\modules\AcademicCalendar\models\AcActivePrograms;
use Yii;

/**
 * This is the model class for table "admission".
 *
 * @property integer $id
 * @property string $admission_name
 * @property integer $admission_status
 * @property integer $admission_created_by
 * @property string $admission_created_at
 * @property integer $admission_updated_by
 * @property string $admission_updated_at
 *
 * @property CourseCatalog[] $courseCatalogs
 * @property Student[] $students
 * @property AcActivePrograms[] $academicPrograms
 */
class Admission extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admission';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admission_name', 'admission_created_by', 'admission_created_at', 'admission_updated_by', 'admission_updated_at'], 'required'],
            [['admission_status', 'admission_created_by', 'admission_updated_by'], 'integer'],
            [['admission_created_at', 'admission_updated_at'], 'safe'],
            [['admission_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'admission_name' => Yii::t('rms', 'Name'),
            'admission_status' => Yii::t('rms', 'Status'),
            'admission_created_by' => Yii::t('rms', 'Created By'),
            'admission_created_at' => Yii::t('rms', 'Created At'),
            'admission_updated_by' => Yii::t('rms', 'Updated By'),
            'admission_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseCatalogs()
    {
        return $this->hasMany(CourseCatalog::class, ['cocat_admission_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(Student::class, ['std_admission_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicPrograms()
    {
        return $this->hasMany(AcActivePrograms::class, ['admission_relId' => 'id']);
    }
}
