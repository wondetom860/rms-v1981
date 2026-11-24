<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "withdrawal_reason".
 *
 * @property integer $id
 * @property string $withdrawalres_description
 * @property string $withdrawalres_type
 * @property string $withdrawalres_updatedAt
 * @property integer $withdrawalres_createdBy_relId
 * @property string $withdrawalres_createdAt
 * @property integer $withdrawalres_updatedby_relId
 * @property String $data
 * @property integer $remove_course_enrolls
 * @property integer $readmittable
 *
 * @property StudentWithdrawalHistory[] $studentWithdrawalHistories
 * @property WStudentWithdrawalHistory[] $wStudentWithdrawalHistories
 * @property User $withdrawalresCreatedByRel
 * @property User $withdrawalresUpdatedbyRel
 */
class WithdrawalReason extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'withdrawal_reason';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['withdrawalres_updatedAt', 'withdrawalres_type', 'withdrawalres_createdAt','data'], 'safe'],

            [['withdrawalres_createdBy_relId', 'withdrawalres_updatedby_relId','remove_course_enrolls','readmittable'], 'integer'],
            [['withdrawalres_description'], 'string', 'max' => 45],
            [['withdrawalres_createdBy_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['withdrawalres_createdBy_relId' => 'id']],
            [['withdrawalres_updatedby_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['withdrawalres_updatedby_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'withdrawalres_description' => Yii::t('rms', 'Withdrawal Description'),
            'withdrawalres_type' => Yii::t('rms', 'Withdrawal Catagory'),
            'remove_course_enrolls' => Yii::t('rms', 'Remove Enrolled Courses'),
            'readmittable' => Yii::t('rms', 'Readmission Possible'),
            'withdrawalres_updatedAt' => Yii::t('rms', 'Withdrawal Updated At'),
            'withdrawalres_createdBy_relId' => Yii::t('rms', 'Withdrawal Created By Rel ID'),
            'withdrawalres_createdAt' => Yii::t('rms', 'Withdrawalres Created At'),
            'withdrawalres_updatedby_relId' => Yii::t('rms', 'Withdrawalres Updatedby Rel ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentWithdrawalHistories()
    {
        return $this->hasMany(WStudentWithdrawalHistory::class, ['swh_withdrawalreason_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWStudentWithdrawalHistories()
    {
        return $this->hasMany(WStudentWithdrawalHistory::class, ['swh_reason_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWithdrawalresCreatedByRel()
    {
        return $this->hasOne(User::class, ['id' => 'withdrawalres_createdBy_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWithdrawalresUpdatedbyRel()
    {
        return $this->hasOne(User::class, ['id' => 'withdrawalres_updatedby_relId']);
    }
}
