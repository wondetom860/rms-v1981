<?php

namespace frontend\modules\ClassSchedule\models;

use Yii;
use \backend\models\User;
use \common\components\CommonControllerFunctions as CCF;

/**
 * This is the model class for table "cs_class_time_distribution".
 *
 * @property int $id
 * @property int $work_day_relId
 * @property string $shift
 * @property int $class_count
 * @property string $start_time
 * @property string $end_time
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property CsClassScheduleDetail[] $csClassScheduleDetails
 * @property User $createdBy
 * @property User $updatedBy
 * @property CsAcademicWorkDay $workDayRel
 */
class CsClassTimeDistribution extends \yii\db\ActiveRecord
{

    public $acadYearId;
    public $semId;

    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cs_class_time_distribution';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['work_day_relId', 'shift', 'start_time', 'end_time', 'created_at', 'created_by', 'data'], 'required'],
            [['work_day_relId', 'class_count', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['shift', 'data'], 'string'],
            [['start_time', 'end_time'], 'safe'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['work_day_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CsAcademicWorkDay::className(), 'targetAttribute' => ['work_day_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'work_day_relId' => Yii::t('rms', 'Work Day'),
            'shift' => Yii::t('rms', 'Shift'),
            'class_count' => Yii::t('rms', 'Class Count'),
            'start_time' => Yii::t('rms', 'Start Time'),
            'end_time' => Yii::t('rms', 'End Time'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function checkExists(){
        return $this->find()
            ->where([
                'work_day_relId' => $this->work_day_relId,
                'shift' => $this->shift,
                'class_count' => $this->class_count,
            ])
            ->one();
    }

    public function getData(){
        $mainData = (object)[
            'shift'=>$this->shift,
            'start_time'=>$this->start_time,
            'end_time'=>$this->end_time,
            'work_day_relId'=>intval($this->work_day_relId),
            'class_count'=>intval($this->class_count),
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ];
        return md5(serialize($mainData));
    }

    public function classCount(){
        if (!is_numeric($this->class_count)) {
            return null;
        }
        return $this->class_count == 1? "1<sup>st</sup>" : 
            ($this->class_count == 2? "2<sup>nd</sup>" : 
                ($this->class_count == 3? "3<sup>rd</sup>" : 
                    "{$this->class_count}<sup>th</sup>"));
    }

    public function luncTime(){
        $__start = date('H:i',strtotime($this->start_time));
        $__end = date('H:i',strtotime($this->end_time));
        return $this->workDayRel->lunch_time == ($__start.'-'.$__end);
    }

    public function nowTime(){
        $__start = strtotime($this->start_time);
        // $__start = date('H:i',);
        $__end = strtotime($this->end_time);
        // $__end = date('H:i',);
        $now = strtotime(date('H:i'));

        return $__start <= $now && $now <= $__end;
    }

    public function getStartTime(){
        return date('h:i A',strtotime($this->start_time));
    }

    public function getEndTime(){
        return date('h:i A',strtotime($this->end_time));
    }

    public function checkAltered(){
        return $this->getData() == $this->data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCsClassScheduleDetails($coffdetObj = null,$type=null,$room=null)
    {
        if ($coffdetObj) {
            return $this->hasMany(CsClassScheduleDetail::className(), ['class_time_distrib_relId' => 'id'])
                ->innerJoin('course_offering2_detail','course_offering2_detail.id = cs_class_schedule_detail.coff2det_relId')
                ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                ->andFilterWhere([
                    'coff2_acadYear_relId' => $coffdetObj->coff2detCoff2Rel->coff2_acadYear_relId,
                    'coff2_sem_relId' => $coffdetObj->coff2detCoff2Rel->coff2_sem_relId,
                    'room_relId' => $room,
                ])
                ->all();
        }

        $this->__constructor();
        // return $this->hasMany(CsClassScheduleDetail::className(), ['class_time_distrib_relId' => 'id']);
        return $this->hasMany(CsClassScheduleDetail::className(), ['class_time_distrib_relId' => 'id'])
                ->innerJoin('course_offering2_detail','course_offering2_detail.id = cs_class_schedule_detail.coff2det_relId')
                ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                ->andFilterWhere([
                    'coff2_acadYear_relId' => $this->acadYearId,
                    'coff2_sem_relId' => $this->semId,
                ])
                ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkDayRel()
    {
        return $this->hasOne(CsAcademicWorkDay::className(), ['id' => 'work_day_relId']);
    }
}
