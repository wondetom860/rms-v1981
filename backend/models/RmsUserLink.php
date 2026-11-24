<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "rms_user_link".
 *
 * @property int $id
 * @property int $person_relId
 * @property string $rms_user_id
 * @property string|null $rms_inst_id
 * @property string|null $rms_std_id
 * @property string $created_at
 * @property int $created_by
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string $data
 *
 * @property User $createdBy
 * @property Person $personRel
 * @property User $updatedBy
 */
class RmsUserLink extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rms_user_link';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_hrms');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['person_relId', 'rms_user_id', 'created_at', 'created_by', 'data'], 'required'],
            [['person_relId', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['data'], 'string'],
            [['rms_user_id', 'rms_inst_id', 'rms_std_id'], 'string', 'max' => 11],
            [['created_by'], 'exist', 'skipOnError' => false, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['person_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Person::className(), 'targetAttribute' => ['person_relId' => 'id']],
            [['rms_inst_id'], 'exist', 'skipOnError' => true, 'targetClass' => Instructor::className(), 'targetAttribute' => ['rms_inst_id' => 'id']],
            [['rms_std_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['rms_std_id' => 'id']],
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
            'person_relId' => Yii::t('rms', 'Personal Info - HRMS'),
            'rms_user_id' => Yii::t('rms', 'On RMS'),
            'rms_inst_id' => Yii::t('rms', 'Instructor'),
            'rms_std_id' => Yii::t('rms', 'Student'),
            'created_at' => Yii::t('rms', 'Created On'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'updated_at' => Yii::t('rms', 'Updated On'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData(){
        $mainData = (object)[
            'rms_user_id'=>$this->rms_user_id,
            'rms_inst_id'=>$this->rms_inst_id,
            'rms_std_id'=>$this->rms_std_id,
            'person_relId'=>intval($this->person_relId),
            'updated_by'=>intval($this->updated_by),
            'created_by'=>intval($this->created_by),
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered(){
        return $this->getData() == $this->data;
    }

    public function getFullNameRms(){
        $user = User::findOne($this->rms_user_id);

        return $user ? $user->getFullName() : '';
    }

    public function getFullNameHrms(){
        return $this->personRel->getFullName();
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
    public function getRmsUserRel()
    {
        return $this->hasOne(User::className(), ['id' => 'rms_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRmsInstRel()
    {
        return $this->hasOne(Instructor::className(), ['id' => 'rms_inst_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRmsStdRel()
    {
        return $this->hasOne(Student::className(), ['id' => 'rms_std_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonRel()
    {
        return $this->hasOne(Person::className(), ['id' => 'person_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
