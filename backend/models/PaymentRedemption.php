<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "payment_redemption".
 *
 * @property int $id
 * @property int $acadYear_relId
 * @property int $sem_relId
 * @property int $std_relId
 * @property string $reason
 * @property int|null $approved_by
 * @property int|null $approved_at
 * @property int|null $used
 * @property string|null $comments
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property SchoolYear $acadYearRel
 * @property Semester $semRel
 * @property Student $stdRel
 * @property User $approvedBy
 * @property User $createdBy
 * @property User $updatedBy
 */
class PaymentRedemption extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_redemption';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['acadYear_relId', 'sem_relId', 'std_relId', 'reason', 'created_at', 'created_by', 'data'], 'required'],
            [['acadYear_relId', 'sem_relId', 'std_relId', 'approved_by', 'approved_at', 'used', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['reason', 'comments', 'data'], 'string'],
            [['acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::className(), 'targetAttribute' => ['acadYear_relId' => 'id']],
            [['sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::className(), 'targetAttribute' => ['sem_relId' => 'id']],
            [['std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['std_relId' => 'id']],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approved_by' => 'id']],
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
            'acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'sem_relId' => Yii::t('rms', 'Semester'),
            'std_relId' => Yii::t('rms', 'Student'),
            'reason' => Yii::t('rms', 'Reason'),
            'approved_by' => Yii::t('rms', 'Approved By'),
            'approved_at' => Yii::t('rms', 'Approved On'),
            'used' => Yii::t('rms', 'Used'),
            'comments' => Yii::t('rms', 'Comments'),
            'created_at' => Yii::t('rms', 'Created On'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated On'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData(){
        $mainData = (object)[
            'acadYear_relId'=>intval($this->acadYear_relId),
            'sem_relId'=>intval($this->sem_relId),
            'std_relId'=>intval($this->std_relId),
            'reason'=>$this->reason,
            'comments'=>$this->comments,
            'used'=>intval($this->used),
            'created_by'=>intval($this->created_by),
            'approved_by'=>intval($this->approved_by),
            'approved_at'=>intval($this->approved_at),
            'updated_by'=>intval($this->updated_by),
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered(){
        return $this->getData() == $this->data;
    }

    public function getRequestExists($acadYearId,$semId,$stdId){
        return $this->find()
            ->where([
                'acadYear_relId' => $acadYearId,
                'sem_relId' => $semId,
                'std_relId' => $stdId,
            ])
            ->one();
    }

    public function exists(){
        if (isset($this->id)) {
            return $this->find()
                ->where([
                    'acadYear_relId' => $this->acadYear_relId,
                    'sem_relId' => $this->sem_relId,
                    'std_relId' => $this->std_relId,
                    'reason' => $this->reason,
                ])
                ->andWhere(['<>','id',$this->id])
                ->count() > 0;
        }
        return $this->find()
            ->where([
                'acadYear_relId' => $this->acadYear_relId,
                'sem_relId' => $this->sem_relId,
                'std_relId' => $this->std_relId,
                'reason' => $this->reason,
            ])
            ->count() > 0;
    }

    public function getRegistrarAlert(){
        // $this->__constructor();
        return $this->find()
            ->where([
                'approved_by'=>NULL,
            ])
            ->count();
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcadYearRel()
    {
        return $this->hasOne(SchoolYear::className(), ['id' => 'acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemRel()
    {
        return $this->hasOne(Semester::className(), ['id' => 'sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdRel()
    {
        return $this->hasOne(Student::className(), ['id' => 'std_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'approved_by']);
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
}
