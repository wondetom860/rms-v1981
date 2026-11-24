<?php

namespace frontend\modules\WithdrawalReadmission\models;

use Yii;
use backend\models\Course;

/**
 * This is the model class for table "removed_course".
 *
 * @property int $id
 * @property int $rc_withdrawal_relId
 * @property int $rc_std_relId
 * @property int $rc_acadYear_relId
 * @property int $rc_sem_relId
 * @property int $rc_course_relId
 * @property int $rc_coff_relId
 * @property int|null $rc_removal_approved
 * @property string|null $rc_removal_approved_at
 * @property int|null $rc_romoval_approved_by
 * @property string|null $rc_grade
 *
 * @property SchoolYear $rcAcadYearRel
 * @property CourseOffering2 $rcCoffRel
 * @property Course $rcCourseRel
 * @property User $rcRomovalApprovedBy
 * @property Semester $rcSemRel
 * @property Student $rcStdRel
 * @property WStudentWithdrawalHistory $rcWithdrawalRel
 */
class RemovedCourse extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'removed_course';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rc_withdrawal_relId', 'rc_std_relId', 'rc_acadYear_relId', 'rc_sem_relId', 'rc_course_relId', 'rc_coff_relId'], 'required'],
            [['rc_withdrawal_relId', 'rc_std_relId', 'rc_acadYear_relId', 'rc_sem_relId', 'rc_course_relId', 'rc_coff_relId', 'rc_removal_approved', 'rc_romoval_approved_by'], 'integer'],
            [['rc_removal_approved_at'], 'safe'],
            [['rc_grade'], 'string', 'max' => 3],
            [['rc_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::className(), 'targetAttribute' => ['rc_acadYear_relId' => 'id']],
            [['rc_coff_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2::className(), 'targetAttribute' => ['rc_coff_relId' => 'id']],
            [['rc_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['rc_course_relId' => 'id']],
            [['rc_romoval_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['rc_romoval_approved_by' => 'id']],
            [['rc_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::className(), 'targetAttribute' => ['rc_sem_relId' => 'id']],
            [['rc_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['rc_std_relId' => 'id']],
            [['rc_withdrawal_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WStudentWithdrawalHistory::className(), 'targetAttribute' => ['rc_withdrawal_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'rc_withdrawal_relId' => Yii::t('rms', 'Rc Withdrawal Rel ID'),
            'rc_std_relId' => Yii::t('rms', 'Rc Std Rel ID'),
            'rc_acadYear_relId' => Yii::t('rms', 'Rc Acad Year Rel ID'),
            'rc_sem_relId' => Yii::t('rms', 'Rc Sem Rel ID'),
            'rc_course_relId' => Yii::t('rms', 'Rc Course Rel ID'),
            'rc_coff_relId' => Yii::t('rms', 'Rc Coff Rel ID'),
            'rc_removal_approved' => Yii::t('rms', 'Rc Removal Approved'),
            'rc_removal_approved_at' => Yii::t('rms', 'Rc Removal Approved At'),
            'rc_romoval_approved_by' => Yii::t('rms', 'Rc Romoval Approved By'),
            'rc_grade' => Yii::t('rms', 'Rc Grade'),
        ];
    }

    /**
     * Gets query for [[RcAcadYearRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRcAcadYearRel()
    {
        return $this->hasOne(SchoolYear::className(), ['id' => 'rc_acadYear_relId']);
    }

    /**
     * Gets query for [[RcCoffRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRcCoffRel()
    {
        return $this->hasOne(CourseOffering2::className(), ['id' => 'rc_coff_relId']);
    }

    /**
     * Gets query for [[RcCourseRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRcCourseRel()
    {
        return $this->hasOne(Course::className(), ['id' => 'rc_course_relId']);
    }

    /**
     * Gets query for [[RcRomovalApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRcRomovalApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'rc_romoval_approved_by']);
    }

    /**
     * Gets query for [[RcSemRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRcSemRel()
    {
        return $this->hasOne(Semester::className(), ['id' => 'rc_sem_relId']);
    }

    /**
     * Gets query for [[RcStdRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRcStdRel()
    {
        return $this->hasOne(Student::className(), ['id' => 'rc_std_relId']);
    }

    /**
     * Gets query for [[RcWithdrawalRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRcWithdrawalRel()
    {
        return $this->hasOne(WStudentWithdrawalHistory::className(), ['id' => 'rc_withdrawal_relId']);
    }
}
