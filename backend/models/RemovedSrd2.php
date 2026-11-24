<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "removed_srd2".
 *
 * @property int $id
 * @property int $semreg_relId
 * @property int $std_relId
 * @property int $coff2det_relId
 * @property string|null $srd2_serialized
 * @property string|null $cont_ass_marks
 * @property float|null $total_mark
 * @property int|null $grade_relId
 * @property int|null $hod_approved
 * @property int|null $hod_approved_at
 * @property int|null $hod_approved_by
 * @property int|null $reg_approved
 * @property int|null $reg_approved_at
 * @property int|null $reg_approved_by
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property SemesterRegistration2 $semregRel
 * @property CourseOffering2Detail $coff2detRel
 * @property Student $stdRel
 * @property User $hodApprovedBy
 * @property User $regApprovedBy
 * @property User $createdBy
 * @property User $updatedBy
 * @property GradeScale $gradeRel
 */
class RemovedSrd2 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'removed_srd2';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['semreg_relId', 'std_relId', 'created_at', 'created_by', 'data'], 'required'],
            [['semreg_relId', 'std_relId', 'grade_relId', 'hod_approved', 'hod_approved_at', 'hod_approved_by', 'reg_approved', 'reg_approved_at', 'reg_approved_by', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['srd2_serialized', 'cont_ass_marks', 'data'], 'string'],
            [['total_mark'], 'number'],
            [['semreg_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterRegistration2::class, 'targetAttribute' => ['semreg_relId' => 'id']],
            [['std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['std_relId' => 'id']],
            [['hod_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['hod_approved_by' => 'id']],
            [['reg_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['reg_approved_by' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['grade_relId'], 'exist', 'skipOnError' => true, 'targetClass' => GradeScale::class, 'targetAttribute' => ['grade_relId' => 'id']],
            [['coff2det_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2Detail::class, 'targetAttribute' => ['coff2det_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'semreg_relId' => Yii::t('rms', 'Registration Entry'),
            'std_relId' => Yii::t('rms', 'Student Name'),
            'coff2det_relId' => Yii::t('rms', 'Course Offering'),
            'srd2_serialized' => Yii::t('rms', 'Srd2 Serialized'),
            'cont_ass_marks' => Yii::t('rms', 'Cont Ass Marks'),
            'total_mark' => Yii::t('rms', 'Total Mark'),
            'grade_relId' => Yii::t('rms', 'Letter Grade'),
            'hod_approved' => Yii::t('rms', 'Hod Approved'),
            'hod_approved_at' => Yii::t('rms', 'Hod Approved At'),
            'hod_approved_by' => Yii::t('rms', 'Hod Approved By'),
            'reg_approved' => Yii::t('rms', 'Reg Approved'),
            'reg_approved_at' => Yii::t('rms', 'Reg Approved At'),
            'reg_approved_by' => Yii::t('rms', 'Reg Approved By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'semreg_relId' => $this->semreg_relId,
            'std_relId' => $this->std_relId,
            'cont_ass_marks' => $this->cont_ass_marks,
            'srd2_serialized' => $this->srd2_serialized,
            'coff2det_relId' => $this->coff2det_relId,
            'grade_relId' => $this->grade_relId,
            'total_mark' => $this->total_mark,
            'hod_approved' => $this->hod_approved,
            'reg_approved' => $this->reg_approved,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]));
    }

    /**
     * Gets query for [[SemregRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSemregRel()
    {
        return $this->hasOne(SemesterRegistration2::class, ['id' => 'semreg_relId']);
    }

    /**
     * Gets query for [[CourseRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'course_relId']);
    }

    /**
     * Gets query for [[Coff2detRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detRel()
    {
        return $this->hasOne(CourseOffering2Detail::class, ['id' => 'coff2det_relId']);
    }

    /**
     * Gets query for [[StdRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'std_relId']);
    }

    /**
     * Gets query for [[HodApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHodApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'hod_approved_by']);
    }

    /**
     * Gets query for [[RegApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'reg_approved_by']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[GradeRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGradeRel()
    {
        return $this->hasOne(GradeScale::class, ['id' => 'grade_relId']);
    }
}
