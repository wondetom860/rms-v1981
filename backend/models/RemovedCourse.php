<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "removed_course".
 *
 * @property integer $id
 * @property integer $rc_std_relId
 * @property integer $rc_acadYear_relId
 * @property integer $rc_sem_relId
 * @property integer $rc_course_relId
 * @property integer $rc_coff_relId
 * @property integer $rc_withdrawal_relId
 * @property integer $rc_removal_approved
 * @property integer $rc_romoval_approved_by
 * @property string $rc_removal_approved_at
 *
 * @property SchoolYear $rcAcadYearRel
 * @property CourseOffering2Detail $rcCoffRel
 * @property Course $rcCourseRel
 * @property Semester $rcSemRel
 * @property WStudentWithdrawalHistory $rcWithdrawalRel
 */
class RemovedCourse extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'removed_course';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rc_std_relId', 'rc_acadYear_relId', 'rc_sem_relId', 'rc_course_relId', 'rc_coff_relId'], 'required'],
            [['rc_std_relId', 'rc_acadYear_relId', 'rc_sem_relId', 'rc_course_relId', 'rc_coff_relId', 'rc_withdrawal_relId', 'rc_removal_approved', 'rc_romoval_approved_by'], 'integer'],
            [['rc_removal_approved_at','rc_grade'], 'safe'],
            [['rc_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['rc_acadYear_relId' => 'id']],
            [['rc_coff_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2::class, 'targetAttribute' => ['rc_coff_relId' => 'id']],
            [['rc_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['rc_course_relId' => 'id']],
            [['rc_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['rc_sem_relId' => 'id']],
            [['rc_withdrawal_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WStudentWithdrawalHistory::class, 'targetAttribute' => ['rc_withdrawal_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'rc_std_relId' => Yii::t('rms', 'Rc Std Rel ID'),
            'rc_acadYear_relId' => Yii::t('rms', 'Rc Acad Year Rel ID'),
            'rc_sem_relId' => Yii::t('rms', 'Rc Sem Rel ID'),
            'rc_course_relId' => Yii::t('rms', 'Rc Course Rel ID'),
            'rc_coff_relId' => Yii::t('rms', 'Rc Coff Rel ID'),
            'rc_withdrawal_relId' => Yii::t('rms', 'Rc Withdrawal Rel ID'),
            'rc_removal_approved' => Yii::t('rms', 'Rc Removal Approved'),
            'rc_romoval_approved_by' => Yii::t('rms', 'Rc Romoval Approved By'),
            'rc_removal_approved_at' => Yii::t('rms', 'Rc Removal Approved At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRcAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'rc_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRcCoffRel()
    {
        return $this->hasOne(CourseOffering2Detail::class, ['id' => 'rc_coff_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRcCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'rc_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRcSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'rc_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRcWithdrawalRel()
    {
        return $this->hasOne(WStudentWithdrawalHistory::class, ['id' => 'rc_withdrawal_relId']);
    }
}
