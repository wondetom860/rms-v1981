<?php

namespace frontend\modules\ExamSchedule\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "ex_date_time_schedule".
 *
 * @property int $id
 * @property int $date
 * @property int $start_time
 * @property int $end_time
 * @property string $standbys
 * @property string $supervisors
 * @property int $created_by 
 * @property int $exam_schedule_entry_relId
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string|null $data
 * @property string|null $shift ['Morning', 'Afternoon', 'Evening']
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property ExExamScheduleEntry $examScheduleEntryRel
 * @property ExExamRoomSlot[] $exExamRoomSlots
 */
class ExDateTimeSchedule extends \yii\db\ActiveRecord
{
    public $date_str, $start_time_str, $end_time_str;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ex_date_time_schedule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'start_time', 'end_time', 'created_by', 'created_at'], 'required'],
            [['date', 'start_time', 'end_time', 'created_by', 'created_at', 'updated_at', 'updated_by', 'exam_schedule_entry_relId'], 'integer'],
            [['standbys', 'supervisors', 'shift'], 'safe'],
            [['data', 'date_str', 'start_time_str', 'end_time_str'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['exam_schedule_entry_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ExExamScheduleEntry::class, 'targetAttribute' => ['exam_schedule_entry_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'exam_schedule_entry_relId' => Yii::t('rms', 'Exam Schedule Entry'),
            'date' => Yii::t('rms', 'Date'),
            'shift' => Yii::t('rms', 'Shift'),
            'date_str' => Yii::t('rms', 'Date'),
            'start_time' => Yii::t('rms', 'Start Time'),
            'start_time_str' => Yii::t('rms', 'Start Time'),
            'end_time' => Yii::t('rms', 'End Time'),
            'end_time_str' => Yii::t('rms', 'End Time'),
            'standbys' => Yii::t('rms', 'Standbys'),
            'supervisors' => Yii::t('rms', 'Supervisors'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getRecentAssignment($userId, $as)
    {
        $examScheduleEntry = $this->examScheduleEntryRel;
        $dateTimeSchedules = $examScheduleEntry->getExDateTimeSchedules()->orderBy(['date' => SORT_DESC])->all();
        if ($dateTimeSchedules) {
            if ($as == 'SUPERVISOR') {
                foreach ($dateTimeSchedules as $dateTime) {
                    if ($dateTime->supervisors && in_array($userId, json_decode($dateTime->supervisors, true))) {
                        return ['as' => 'Supervisor', 'date' => date('dMY',$dateTime->date)];
                    }
                }
            } else if ($as == 'STANDBY') {
                foreach ($dateTimeSchedules as $dateTime) {
                    if ($dateTime->standbys && in_array($userId, json_decode($dateTime->standbys, true))) {
                        return ['as' => 'Standby', 'date' => date('dMY',$dateTime->date)];
                    }
                }
            }
        } else {
            return null;
        }
    }

    public function getAllocatedStaffsInthisDateTimeSchedule()
    {
        $standBys = $this->standbys ? json_decode($this->standbys,true) : [];
        $suppervisors = $this->supervisors ? json_decode($this->supervisors,true) : [];
        $invigilators = ExInvigilators::find()
            ->innerJoinWith('examRoomSlotRel')
            ->where([
                'date_time_slot_relId' => $this->id
            ])
            ->select('staff_relId')
            ->column();

        return array_unique(array_merge($standBys, $suppervisors, $invigilators));
    }

    public function getDateScheduled()
    {
        $day = date('dMY', $this->date);
        $shift = $this->shift;

        return " $day ($shift) ";
    }

    public function getHeaderText()
    {
        $day = date('D', $this->date);
        $shift = $this->shift;
        $schedule_time = $this->getScheduleTime();
        return "$day $schedule_time";
    }

    public function getScheduleTime()
    {
        return date('h:i A', $this->start_time) . ' - ' . date('h:i A', $this->end_time);
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'exam_schedule_entry_relId' => $this->exam_schedule_entry_relId,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'date' => $this->date,
            'shift' => $this->shift,
            'standbys' => $this->standbys,
            'supervisors' => $this->supervisors,
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    public function checkIfExists($id = null)
    {
        if ($id) {
            return self::find()
                ->where([
                    'date' => $this->date,
                    'shift' => $this->shift,
                ])
                ->andWhere(" id <> $id ")
                ->one();
        }
        return self::findOne([
            'date' => $this->date,
            'shift' => $this->shift,
        ]);
    }

    public function appendStdbyUser($userId)
    {
        // cehck if standbyUser already assigned as supervisor
        if (!is_null($this->supervisors)) {
            $supervisors = json_decode($this->supervisors, true);
            if (in_array($userId, $supervisors)) {
                return false;
            }
        }

        if (is_null($this->standbys)) {
            $this->standbys = json_encode([$userId]);
            return true;
        }
        // check if user is already in the standbys list
        $standbys = json_decode($this->standbys, true);
        if (!in_array($userId, $standbys)) {
            $standbys[] = $userId;
            $this->standbys = json_encode($standbys);
            return true;
        }
        return false;
    }

    public function removeSupervisorUser($userId)
    {
        if (is_null($this->supervisors)) {
            return false;
        }

        // check if user is already in the supervisors list
        $supervisors = json_decode($this->supervisors, true);
        if (in_array($userId, $supervisors)) {
            $supervisors = array_diff($supervisors, [$userId]);
            $this->supervisors = json_encode($supervisors);
            return true;
        }
        return false;
    }

    public function appendSupervisorUser($userId)
    {
        if (is_null($this->supervisors)) {
            $this->supervisors = json_encode([$userId]);
            return true;
        }
        // cehck if supervisorUser already assigned as standby
        if (!is_null($this->standbys)) {
            $standbys = json_decode($this->standbys, true);
            if (in_array($userId, $standbys)) {
                return false;
            }
        }
        // check if user is already in the supervisors list
        $supervisors = json_decode($this->supervisors, true);
        if (!in_array($userId, $supervisors)) {
            $supervisors[] = $userId;
            $this->supervisors = json_encode($supervisors);
            return true;
        }
        return false;
    }

    public function removeStdbyUser($userId)
    {
        if (is_null($this->standbys)) {
            return false;
        }
        // check if user is already in the standbys list
        $standbys = json_decode($this->standbys, true);
        if (in_array($userId, $standbys)) {
            $standbys = array_diff($standbys, [$userId]);
            $this->standbys = json_encode($standbys);
            return true;
        }
        return false;
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
     * Gets query for [[ExamScheduleEntryRel]].
     *
     * @return \yii\db\ActiveQuery|ExExamScheduleEntryQuery
     */
    public function getExamScheduleEntryRel()
    {
        return $this->hasOne(ExExamScheduleEntry::class, ['id' => 'exam_schedule_entry_relId']);
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
     * Gets query for [[ExExamRoomSlots]].
     *
     * @return \yii\db\ActiveQuery|ExExamRoomSlotQuery
     */
    public function getExExamRoomSlots()
    {
        return $this->hasMany(ExExamRoomSlot::class, ['date_time_slot_relId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return ExDateTimeScheduleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExDateTimeScheduleQuery(get_called_class());
    }
}
