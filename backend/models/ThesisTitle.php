<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "thesis_title".
 *
 * @property int $id
 * @property int $tt_srd2_relId
 * @property int $tt_std_relId
 * @property string|null $tt_title
 * @property int|null $tt_approved
 * @property int|null $tt_approved_by
 * @property string|null $tt_approved_at
 * @property int|null $tt_hod_approved
 * @property string|null $tt_hod_approved_at
 * @property int|null $tt_hod_approved_by
 * @property int|null $tt_graded
 *
 * @property User $ttApprovedBy
 * @property User $ttHodApprovedBy
 * @property SemesterRegistration2Detail $ttSrd2Rel
 * @property Student $ttStdRel
 */
class ThesisTitle extends \yii\db\ActiveRecord
{ 

    public $std_column;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'thesis_title';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tt_srd2_relId', 'tt_std_relId'], 'required'],
            [['tt_srd2_relId', 'tt_std_relId', 'tt_approved', 'tt_approved_by', 'tt_hod_approved', 'tt_hod_approved_by', 'tt_graded'], 'integer'],
            [['tt_title','std_column'], 'string'],
            [['tt_approved_at', 'tt_hod_approved_at'], 'safe'],
            [['tt_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['tt_approved_by' => 'id']],
            [['tt_hod_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['tt_hod_approved_by' => 'id']],
            [['tt_srd2_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterRegistration2Detail::class, 'targetAttribute' => ['tt_srd2_relId' => 'id']],
            [['tt_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['tt_std_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'tt_srd2_relId' => Yii::t('rms', 'Semester Registration '),
            'tt_std_relId' => Yii::t('rms', 'Student'),
            'std_column' => Yii::t('rms', 'Student'),//
            'tt_title' => Yii::t('rms', 'Thesis Title'),
            'tt_approved' => Yii::t('rms', 'Approved'),
            'tt_approved_by' => Yii::t('rms', 'Approved By'),
            'tt_approved_at' => Yii::t('rms', 'Approved At'),
            'tt_hod_approved' => Yii::t('rms', 'Hod Approved'),
            'tt_hod_approved_at' => Yii::t('rms', 'Hod Approved At'),
            'tt_hod_approved_by' => Yii::t('rms', 'Hod Approved By'),
            'tt_graded' => Yii::t('rms', 'Thesis Graded'),
        ];
    }

    public function getSrd2DetailInfo(){
        $srd2 = $this->ttSrd2Rel;

        return "<b>Course Title : </b>\t<u>".$srd2->srd2CourseRel->getCourseCredits().": ".$srd2->srd2CourseRel->course_title.
            "</u><br><b>Academic Year of course Enrollment : </b>\t<u>".$srd2->srd2GroupRel->gsActiveProgRel->getAcadYearHeader().
            "</u><br><b>Class Level & Semester : </b>\t<u>".$srd2->srd2SemregRel->semregClslvlRel->getClslvlDisp()."-{$srd2->srd2SemregRel->semregSemesterRel->semester_name}
            </u><br> <b>Group/Section: </b><u>{$srd2->srd2GroupRel->getGsDisp()}-{$srd2->srd2GroupRel->gsGroupRel->group_name}</u>";
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTtApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'tt_approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTtHodApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'tt_hod_approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTtSrd2Rel()
    {
        return $this->hasOne(SemesterRegistration2Detail::class, ['id' => 'tt_srd2_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTtStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'tt_std_relId']);
    }
}
