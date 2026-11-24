<?php

namespace frontend\modules\AcademicCalendar\models;

use Yii;
use backend\models\User;
use backend\models\AcademicCalanderAction;

/**
 * This is the model class for table "ac_program_schedule".
 *
 * @property int $id
 * @property int $active_program_relId
 * @property int $action_relId
 * @property int $start_date
 * @property int $end_date
 * @property int $last_extend extend days count
 * @property int $last_extend_status
 * @property string|null $remark
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property AcademicCalanderAction $actionRel
 * @property AcActivePrograms $activeProgramRel
 * @property User $createdBy
 * @property User $updatedBy
 */
class AcProgramSchedule extends \yii\db\ActiveRecord
{
    public $__selected_programs = '';
    public $academic_year = '';
    public $class_year = '';

    public $statuses = [0 => 'Closed', 1 => 'Active', -1 => 'Up Next'];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ac_program_schedule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active_program_relId', 'action_relId', 'start_date', 'end_date', 'last_extend', 'last_extend_status', 'created_at', 'created_by', 'data'], 'required'],
            [['active_program_relId', 'action_relId',  'last_extend', 'last_extend_status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['remark', 'data', '__selected_programs', 'academic_year', 'class_year', 'start_date', 'end_date',], 'string'],
            [['action_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcademicCalanderAction::class, 'targetAttribute' => ['action_relId' => 'id']],
            [['active_program_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcActivePrograms::class, 'targetAttribute' => ['active_program_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
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
            'active_program_relId' => Yii::t('rms', 'Program'),
            'action_relId' => Yii::t('rms', 'Activity'),
            'start_date' => Yii::t('rms', 'Start Date'),
            'end_date' => Yii::t('rms', 'End Date'),
            'last_extend' => Yii::t('rms', 'Last Extend'),
            'last_extend_status' => Yii::t('rms', 'Last Extend Status'),
            'remark' => Yii::t('rms', 'Remark'),
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
            'remark' => $this->remark,
            'active_program_relId' => intval($this->active_program_relId),
            'action_relId' => intval($this->action_relId),
            'start_date' => intval($this->start_date),
            'end_date' => intval($this->end_date),
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    public function getClaaYearHeader()
    {
        return $this->activeProgramRel->getClassLevelHeader();
    }

    public function getProgramHeader()
    {
        return $this->activeProgramRel->academicYearRel->getAcHeader();
    }

    public function getStatus()
    {
        $st = "";
        $s = 1;

        // $acadYearStatus = $this->activeProgramRel->academicYearRel->getStatus();

        // if ((strtoupper($acadYearStatus) != 'ACTIVE') || 0) {
        //     //if academic year is not active, the program scdule must also be not active.
        //     $s = array_keys($this->statuses,$acadYearStatus)[0];
        //     $st = $acadYearStatus;
        // }else{
        //     if(time() > $this->end_date){
        //         $st = "Closed";
        //         $s = 0;
        //     }else if(time() < $this->start_date){
        //         $st = "Up Next";
        //         $s = -1;
        //     }else{
        //         // $s = 1;
        //         $st = "Active";
        //     }
        // }

        if (time() > $this->end_date) {
            $st = "Closed";
            $s = 0;
        } else if (time() < $this->start_date) {
            $st = "Up Next";
            $s = -1;
        } else {
            // $s = 1;
            $st = "Active";
        }

        if ($this->status != $s) {
            $this->status = $s;
            $this->update(false);
        }

        // return $st;
        return $this->status == 1 ? "<span class='bg-success'>{$st}</span>" : "<span class='bg-warning'>{$st}</span>";
    }

    public function validDateInterval()
    {
        return $this->start_date <= $this->end_date;
    }

    public function checkIfExists($id = null)
    {
        // active_program has academic year information with which it is linked and is unique
        return $id ?
            self::find()->where([
                'active_program_relId' => $this->active_program_relId,
                'action_relId' => $this->action_relId,
                'status' => 1
            ])
            ->andWhere(" id <> $id ")
            ->count() > 0
            :
            self::find()->where([
                'active_program_relId' => $this->active_program_relId,
                'action_relId' => $this->action_relId,
                'status' => 1
            ])
            ->count() > 0;
    }

    public function datesInAcademicYearInterval()
    {
        $academicCalendar = $this->activeProgramRel->academicYearRel;

        return $this->start_date >= $academicCalendar->start_date &&
            $this->start_date <= $academicCalendar->end_date &&
            $this->end_date >= $academicCalendar->start_date &&
            $this->end_date <= $academicCalendar->end_date;
    }

    public function checkIfScheduleActive($actionId, $programId)
    {
        $time = time();
        return self::find()
            ->where([
                'action_relId' => $actionId,
                'active_program_relId' => $programId,
            ])->andWhere("{$time} BETWEEN start_date AND end_date")
            ->all();
    }

    public function getScheduledProgramsforActivity($activityName)
    {
        /* this function looks for program schedule entry and returns those programs for which $activity is set on. */
        $time = time();
        return self::find()
            ->innerjoin('academic_calander_action', 'academic_calander_action.id = ac_program_schedule.action_relId')
            ->where([
                'aca_name' => $activityName,
            ])->andWhere("{$time} BETWEEN start_date AND end_date")
            ->all();
    }

    public function checkScheduled($actionId, $programId)
    {
        return self::find()->where(['active_program_relId' => $programId, 'action_relId' => $actionId])->count() > 0;
    }

    public function getSchedule($actionId, $programId, $withStatus = false)
    {
        $schedules = self::find()->where(['active_program_relId' => $programId, 'action_relId' => $actionId])->all();

        $shedule_str = "";
        $__id = 0;
        $status = '';
        if ($schedules && count($schedules) > 0) {
            foreach ($schedules as $schedule) {
                $status = $schedule->getStatus();
                $shedule_str .= $schedule->getScheduleInterval();
                $__id = $schedule->id;
            }
        }

        $shedule_str .= "<a class='btn btn-xs btn-warning pull-right' href='/AcademicCalendar/ac-program-schedule/update?id={$__id}' title='Update this schedule.'>Update</a>";
        return $withStatus ? [$status, $shedule_str] : $shedule_str;
    }

    public function getScheduleInterval()
    {
        return date("d/m/Y", $this->start_date) . "-" . date("d/m/Y", $this->end_date);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActionRel()
    {
        return $this->hasOne(AcademicCalanderAction::class, ['id' => 'action_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveProgramRel()
    {
        return $this->hasOne(AcActivePrograms::class, ['id' => 'active_program_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
