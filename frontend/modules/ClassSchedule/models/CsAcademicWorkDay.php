<?php

namespace frontend\modules\ClassSchedule\models;

use Yii;
use \backend\models\User;

/**
 * This is the model class for table "cs_academic_work_day".
 *
 * @property int $id
 * @property string $day_name
 * @property string $class_start_at begin time of first class
 * @property int $duration_one_class duration of a class in minutes
 * @property string $lunch_time eg 12:30-1:20
 * @property string $class_end_time
 * @property int $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property CsClassTimeDistribution[] $csClassTimeDistributions
 */
class CsAcademicWorkDay extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cs_academic_work_day';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['day_name', 'class_start_at', 'duration_one_class', 'lunch_time', 'class_end_time', 'created_at', 'created_by', 'data'], 'required'],
            [['class_start_at', 'class_end_time'], 'safe'],
            [['duration_one_class', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['data'], 'string'],
            [['day_name', 'lunch_time'], 'string', 'max' => 25],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'day_name' => Yii::t('rms', 'Week Day'),
            'class_start_at' => Yii::t('rms', 'Class Start At'),
            'duration_one_class' => Yii::t('rms', 'Duration One Class'),
            'lunch_time' => Yii::t('rms', 'Lunch Time'),
            'class_end_time' => Yii::t('rms', 'Class End Time'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function checkExists(){
        return $this->find()->where(['day_name' => $this->day_name,])->count() > 0;
    }

    public function getData(){
        $mainData = (object)[
            'day_name'=>$this->day_name,
            'class_start_at'=>$this->class_start_at,
            'class_end_time'=>$this->class_end_time,
            'lunch_time'=>$this->lunch_time,
            'duration_one_class'=>intval($this->duration_one_class),
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered(){
        return $this->getData() == $this->data;
    }

    public function isToday(){
        return $this->day_name == date('l');
    }

    public function distributeClassTime(){

        $dt = strtotime($this->class_start_at);

        $dtEnd = strtotime($this->class_end_time);

        $start = $dt;
        $round = 1;

        $this->duration_one_class = intval($this->duration_one_class);

        if ($start > $dtEnd) {
            throw new \Exception("Invalid Date Interval for start and end date.", 1); 
        }

        while ($start < $dtEnd) {
            $_start = date('H:i',$start);
            $start += ($this->duration_one_class*60);
            $_end = date('H:i',$start);

            $lunch = "{$_start}-{$_end}" == $this->lunch_time ? true:false;

            $modelClassDistribution = new CsClassTimeDistribution();

            $modelClassDistribution->work_day_relId = $this->id;
            $modelClassDistribution->shift = date('A',$start) == 'AM' ?'Morning' : 'Afternoon';
            $modelClassDistribution->class_count = $lunch?null:$round;
            $modelClassDistribution->start_time = $_start;
            $modelClassDistribution->end_time = $_end;

            $exist = $modelClassDistribution->checkExists();
            if ($exist && count($exist) > 0) {
                $exist->start_time = $_start;
                $exist->end_time = $_end;
                $exist->updated_by = \Yii::$app->user->identity->id; 
                $exist->updated_at = time(); 
                $exist->update();
            }else{
               $modelClassDistribution->created_by = \Yii::$app->user->identity->id; 
               $modelClassDistribution->created_at = time(); 

               $modelClassDistribution->data = $modelClassDistribution->getData();

               if ($modelClassDistribution->save()) {
                   // continue;
               }else{
                    // var_dump($modelClassDistribution->getErrors());die();
                    throw new \Exception(json_encode($modelClassDistribution->getErrors()), 1); 
               }
            }

            $start += 600;
            $round += $lunch?0:1;//jump lunch time
        }        
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
    public function getCsClassTimeDistributions()
    {
        return $this->hasMany(CsClassTimeDistribution::className(), ['work_day_relId' => 'id']);
    }
}
