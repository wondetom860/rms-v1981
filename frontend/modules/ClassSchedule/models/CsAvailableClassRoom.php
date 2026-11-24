<?php

namespace frontend\modules\ClassSchedule\models;

use Yii;
use \backend\models\User;

/**
 * This is the model class for table "cs_available_class_room".
 *
 * @property int $id
 * @property string $room
 * @property string|null $description
 * @property int $status 1 for available class room
 * @property int|null $num_of_seats
 * @property int|null $projector
 * @property int|null $projector_board
 * @property int|null $white_board
 * @property int|null $black_board
 * @property int|null $room_pc
 * @property int|null $pulpit
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $updated_at
 * @property int $created_at
 * @property string $data
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property CsClassScheduleDetail[] $csClassScheduleDetails
 */
class CsAvailableClassRoom extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cs_available_class_room';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['room', 'created_by', 'created_at', 'data'], 'required'],
            [['description', 'data'], 'string'],
            [['status', 'num_of_seats', 'projector', 'projector_board', 'white_board', 'black_board', 'room_pc', 'pulpit', 'created_by', 'updated_by', 'updated_at', 'created_at'], 'integer'],
            [['room'], 'string', 'max' => 25],
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
            'id' => 'ID',
            'room' => 'Room',
            'description' => 'Description',
            'status' => 'Status',
            'num_of_seats' => 'Num Of Seats',
            'projector' => 'Projector',
            'projector_board' => 'Projector Board',
            'white_board' => 'White Board',
            'black_board' => 'Black Board',
            'room_pc' => 'Room Pc',
            'pulpit' => 'Pulpit',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'data' => 'Data',
        ];
    }

    public function checkExists(){
        return $this->find()->where(['room' => $this->room])->count() > 0;
    }

    public function getData(){
        $mainData = (object)[
            'room'=>$this->room,
            'num_of_seats'=>$this->num_of_seats,
            'projector'=>$this->projector,
            'white_board'=>$this->white_board,
            'black_board'=>$this->black_board,
            'projector_board'=>$this->projector_board,
            'pulpit'=>$this->pulpit,
            'room_pc'=>$this->room_pc,
            'description'=>$this->description,
            'status'=>intval($this->status),
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered(){
        return $this->getData() == $this->data;
    }
    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[CsClassScheduleDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCsClassScheduleDetails()
    {
        return $this->hasMany(CsClassScheduleDetail::className(), ['room_relId' => 'id']);
    }
}
