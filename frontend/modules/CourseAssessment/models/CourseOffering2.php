<?php

namespace frontend\modules\CourseAssessment\models;

use backend\models\SchoolYear;
use backend\models\Semester;
use backend\models\SemesterRegistration2Detail;
use Yii;
use \backend\models\User;
use backend\models\WGroupSetup;

/**
 * This is the model class for table "course_offering2".
 *
 * @property int $id
 * @property int $coff2_acadYear_relId
 * @property int $coff2_sem_relId
 * @property int $coff2_group_relId Group Setup
 *
 * @property SchoolYear $coff2AcadYearRel
 * @property WGroupSetup $coff2GroupRel
 * @property Semester $coff2SemRel
 * @property CourseOffering2Detail[] $courseOffering2Details
 * @property SemesterRegistration2Detail[] $semesterRegistration2Details
 */
class CourseOffering2 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'course_offering2';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['coff2_acadYear_relId', 'coff2_sem_relId', 'coff2_group_relId'], 'required'],
            [['coff2_acadYear_relId', 'coff2_sem_relId', 'coff2_group_relId'], 'integer'],
            [['coff2_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['coff2_acadYear_relId' => 'id']],
            [['coff2_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['coff2_group_relId' => 'id']],
            [['coff2_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['coff2_sem_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'coff2_acadYear_relId' => Yii::t('rms', 'Coff2 Acad Year Rel ID'),
            'coff2_sem_relId' => Yii::t('rms', 'Coff2 Sem Rel ID'),
            'coff2_group_relId' => Yii::t('rms', 'Coff2 Group Rel ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2AcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'coff2_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2GroupRel()
    {
        return $this->hasOne(WGroupSetup::class, ['id' => 'coff2_group_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2SemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'coff2_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseOffering2Details()
    {
        return $this->hasMany(CourseOffering2Detail::class, ['coff2det_coff2_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2Details()
    {
        return $this->hasMany(SemesterRegistration2Detail::class, ['srd2_coff_relId' => 'id']);
    }
}
