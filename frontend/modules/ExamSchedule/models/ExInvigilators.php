<?php

namespace frontend\modules\ExamSchedule\models;

use Yii;
use common\models\User;
use backend\models\CourseOffering2Detail;
use backend\models\WGroupSetup;

/**
 * This is the model class for table "ex_invigilators".
 *
 * @property int $id
 * @property int $exam_room_slot_relId
 * @property int $section_relId
 * @property int $offering_detail_relId
 * @property int $staff_relId
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string|null $data
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property ExExamRoomSlot $examRoomSlotRel
 * @property CourseOffering2Detail $offeringDetailRel
 * @property WGroupSetup $sectionRel
 * @property User $staffRel
 */
class ExInvigilators extends \yii\db\ActiveRecord
{

    public $staffs;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ex_invigilators';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['exam_room_slot_relId', 'staff_relId', 'created_at', 'created_by'], 'required'],
            [['exam_room_slot_relId', 'section_relId', 'offering_detail_relId', 'created_at', 'staff_relId', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['data', 'staffs'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['exam_room_slot_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ExExamRoomSlot::class, 'targetAttribute' => ['exam_room_slot_relId' => 'id']],
            [['offering_detail_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2Detail::class, 'targetAttribute' => ['offering_detail_relId' => 'id']],
            [['staff_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['staff_relId' => 'id']],
            [['section_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['section_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'exam_room_slot_relId' => Yii::t('rms', 'Exam Room'),
            'section_relId' => Yii::t('rms', 'Section'),
            'staff' => Yii::t('rms', 'Invigilators'),
            'offering_detail_relId' => Yii::t('rms', 'Course'),
            'staff_relId' => Yii::t('rms', 'Invigilator'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function createInvigilators()
    {
        // var_dump($this->section_relId);exit;
        $staffIds = json_decode($this->staffs, true);
        $message = "";
        $totalAssigned = 0;
        if ($staffIds) {
            foreach ($staffIds as $key => $value) {
                $user = User::find()->where(["id" => $value])->one();
                if (
                    in_array($value, $this->getAllocatedStaffsInthisDateTimeSchedule()) ||
                    in_array($value, $this->getScheduledCourseInstructors())
                ) {
                    $message .= "{$user->getFullName()} cannot be assigned as invigilator, Assigned for the same date or course instructor.";
                } else {
                    $model = new ExInvigilators();
                    $model->staff_relId = $value;
                    $model->exam_room_slot_relId = $this->exam_room_slot_relId;
                    $model->section_relId = $this->section_relId;
                    $model->offering_detail_relId = $this->offering_detail_relId;

                    $model->created_at = time();
                    $model->created_by = Yii::$app->user->id;

                    $model->getData();

                    if ($model->save()) {
                        $totalAssigned++;
                        continue;
                    } else {
                        $message .= "{$user->getFullName()} cannot be assigned as invigilator, Error: " . json_encode($model->errors);
                    }
                }
            }
        } else {
            $message = "No staff selected.";
        }

        return [$totalAssigned, $message];
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'exam_room_slot_relId' => $this->exam_room_slot_relId,
            'offering_detail_relId' => $this->offering_detail_relId,
            'section_relId' => $this->section_relId,
            'staff_relId' => $this->staff_relId,
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    public function getrecentInvigilationScehdule($userId)
    {
        $asses = self::find()
            ->innerJoinWith('examRoomSlotRel')
            ->innerJoin('ex_date_time_schedule', 'ex_date_time_schedule.id=ex_exam_room_slot.date_time_slot_relId')
            ->where([
                'staff_relId' => $userId,
                'ex_date_time_schedule.exam_schedule_entry_relId' => $this->examRoomSlotRel->dateTimeSlotRel->exam_schedule_entry_relId,
            ])
            ->orderBy(['date' => SORT_DESC])
            ->all();
        if ($asses) {
            return ['as' => 'INVIGILATOR', 'date' => date('dMY', $asses[0]->examRoomSlotRel->dateTimeSlotRel->date)];
        }

        return null;
    }

    public function getRecentExamSchedule($userId, $string = false)
    {
        $user = User::findOne($userId);

        $asSuperVisor = $this->examRoomSlotRel->dateTimeSlotRel->getRecentAssignment($userId, 'SUPERVISOR');
        $asStandBy = $this->examRoomSlotRel->dateTimeSlotRel->getRecentAssignment($userId, 'STANDBY');
        $asInvigilator = $this->getrecentInvigilationScehdule($userId);
        $assignments = array_merge($asStandBy ?? [], $asSuperVisor ?? [], $asInvigilator ?? []);
        if ($string) {
            $stringg = "";
            foreach ($assignments as $key => $value) {
                if (!empty($value)) {
                    $stringg .= " $value ";
                }
            }
            return $stringg;
        }
        return $assignments;
    }

    /**
     * Summary of getAllocatedStaffsInthisDateTimeSchedule
     * returns array of userIds
     * @return array
     */
    public function getAllocatedStaffsInthisDateTimeSchedule()
    {
        return $this->examRoomSlotRel->dateTimeSlotRel->getAllocatedStaffsInthisDateTimeSchedule();
    }
    /**
     * Summary of getScheduledCourseInstructors
     * returns List of instructors whose course id scheduled for this dateTimeScehdule
     * @return array
     */
    public function getScheduledCourseInstructors()
    {
        return $this->examRoomSlotRel->getScheduledCourseInstructors();
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[StaffRel]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getStaffRel()
    {
        return $this->hasOne(User::class, ['id' => 'staff_relId']);
    }

    /**
     * Gets query for [[ExamRoomSlotRel]].
     *
     * @return \yii\db\ActiveQuery|ExExamRoomSlotQuery
     */
    public function getExamRoomSlotRel()
    {
        return $this->hasOne(ExExamRoomSlot::class, ['id' => 'exam_room_slot_relId']);
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
     * {@inheritdoc}
     * @return ExInvigilatorsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExInvigilatorsQuery(get_called_class());
    }
}
