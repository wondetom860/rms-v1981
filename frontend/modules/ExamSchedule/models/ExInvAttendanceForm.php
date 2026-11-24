<?php

namespace frontend\modules\ExamSchedule\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "ex_inv_attendance_form".
 *
 * @property int $id
 * @property int $date_time_slot_relId
 * @property int $exam_slot_relId
 * @property int $inviglator_relId
 * @property string $assigned_as
 * @property int|null $status
 * @property int|null $approved
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string|null $data
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $inviglatorRel
 * @property ExDateTimeSchedule $dateTimeSlotRel
 * @property ExExamRoomSlot $examSlotRel
 */
class ExInvAttendanceForm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ex_inv_attendance_form';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_time_slot_relId', 'exam_slot_relId', 'inviglator_relId', 'assigned_as', 'created_at', 'created_by'], 'required'],
            [['date_time_slot_relId', 'exam_slot_relId', 'inviglator_relId', 'status', 'approved', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['assigned_as', 'data'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['inviglator_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['inviglator_relId' => 'id']],
            [['date_time_slot_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ExDateTimeSchedule::class, 'targetAttribute' => ['date_time_slot_relId' => 'id']],
            [['exam_slot_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ExExamRoomSlot::class, 'targetAttribute' => ['exam_slot_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'date_time_slot_relId' => Yii::t('rms', 'Date Time Slot Rel ID'),
            'exam_slot_relId' => Yii::t('rms', 'Exam Slot Rel ID'),
            'inviglator_relId' => Yii::t('rms', 'Inviglator Rel ID'),
            'assigned_as' => Yii::t('rms', 'Assigned As'),
            'status' => Yii::t('rms', 'Status'),
            'approved' => Yii::t('rms', 'Approved'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
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
     * Gets query for [[InviglatorRel]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getInviglatorRel()
    {
        return $this->hasOne(User::class, ['id' => 'inviglator_relId']);
    }

    /**
     * Gets query for [[DateTimeSlotRel]].
     *
     * @return \yii\db\ActiveQuery|ExDateTimeScheduleQuery
     */
    public function getDateTimeSlotRel()
    {
        return $this->hasOne(ExDateTimeSchedule::class, ['id' => 'date_time_slot_relId']);
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
     * {@inheritdoc}
     * @return ExInvAttendanceFormQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExInvAttendanceFormQuery(get_called_class());
    }
}
