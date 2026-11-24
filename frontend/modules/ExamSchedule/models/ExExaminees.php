<?php

namespace frontend\modules\ExamSchedule\models;

use Yii;
use common\models\User;
use backend\models\CourseOffering2Detail;
use backend\models\WGroupSetup;
use backend\models\Student;

/**
 * This is the model class for table "ex_examinees".
 *
 * @property int $id
 * @property int $exam_slot_relId
 * @property int $std_relId
 * @property int $section_relId
 * @property int $offering_detail_relId
 * @property float|null $class_attendance_percent
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $createdBy
 * @property ExExamRoomSlot $examSlotRel
 * @property CourseOffering2Detail $offeringDetailRel
 * @property WGroupSetup $sectionRel
 * @property Student $stdRel
 * @property User $updatedBy
 */
class ExExaminees extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ex_examinees';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['exam_slot_relId', 'std_relId', 'section_relId', 'offering_detail_relId', 'created_at', 'created_by', 'data'], 'required'],
            [['exam_slot_relId', 'std_relId', 'section_relId', 'offering_detail_relId', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['class_attendance_percent'], 'number'],
            [['data'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['exam_slot_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ExExamRoomSlot::class, 'targetAttribute' => ['exam_slot_relId' => 'id']],
            [['offering_detail_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2Detail::class, 'targetAttribute' => ['offering_detail_relId' => 'id']],
            [['section_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['section_relId' => 'id']],
            [['std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['std_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'exam_slot_relId' => Yii::t('rms', 'Exam Slot Rel ID'),
            'std_relId' => Yii::t('rms', 'Std Rel ID'),
            'section_relId' => Yii::t('rms', 'Section Rel ID'),
            'offering_detail_relId' => Yii::t('rms', 'Offering Detail Rel ID'),
            'class_attendance_percent' => Yii::t('rms', 'Class Attendance Percent'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData(){
        $this->data = md5(serialize((object)[
            'exam_slot_relId' => $this->exam_slot_relId,
            'offering_detail_relId' => $this->offering_detail_relId,
            'section_relId' => $this->section_relId,
            'std_relId' => $this->std_relId,
            'class_attendance_percent' => $this->class_attendance_percent,
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    public function assigned($stdId){
        return self::find()
            ->innerJoinWith('examSlotRel')
            ->where([
                'date_time_slot_relId' => $this->examSlotRel->dateTimeSlotRel->id,
                'std_relId' => $stdId,
            ])
            ->one();
    }

    public function getHeaderText2(){
        return $this->examSlotRel->dateTimeSlotRel->getHeaderText();
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[ExamSlotRel]].
     *
     * @return \yii\db\ActiveQuery|ExExamRoomSlotQuery
     */
    public function getExamSlotRel()
    {
        return $this->hasOne(ExExamRoomSlot::class, ['id' => 'exam_slot_relId']);
    }

    /**
     * Gets query for [[OfferingDetailRel]].
     *
     * @return \yii\db\ActiveQuery|CourseOffering2DetailQuery
     */
    public function getOfferingDetailRel()
    {
        return $this->hasOne(CourseOffering2Detail::class, ['id' => 'offering_detail_relId']);
    }

    /**
     * Gets query for [[SectionRel]].
     *
     * @return \yii\db\ActiveQuery|WGroupSetupQuery
     */
    public function getSectionRel()
    {
        return $this->hasOne(WGroupSetup::class, ['id' => 'section_relId']);
    }

    /**
     * Gets query for [[StdRel]].
     *
     * @return \yii\db\ActiveQuery|StudentQuery
     */
    public function getStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'std_relId']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return ExExamineesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExExamineesQuery(get_called_class());
    }
}
