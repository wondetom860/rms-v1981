<?php

namespace frontend\modules\Reassessment\models;

use Yii;
use backend\models\User;
use backend\models\Instructor;

/**
 * This is the model class for table "reassessment_committee".
 *
 * @property int $id
 * @property int $ras_relId
 * @property int $inst_relId
 * @property int|null $approval_status
 * @property int|null $approved_at
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $createdBy
 * @property Instructor $instRel
 * @property ReassessmentHistory $rasRel
 * @property User $updatedBy
 */
class ReassessmentCommittee extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reassessment_committee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ras_relId', 'inst_relId', 'created_at', 'created_by', 'data'], 'required'],
            [['ras_relId', 'inst_relId', 'approval_status', 'approved_at', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['data'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['inst_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Instructor::class, 'targetAttribute' => ['inst_relId' => 'id']],
            [['ras_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ReassessmentHistory::class, 'targetAttribute' => ['ras_relId' => 'id']],
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
            'ras_relId' => Yii::t('rms', 'Request Entry'),
            'inst_relId' => Yii::t('rms', 'Member Name'),
            'approval_status' => Yii::t('rms', 'Approval/Accept'),
            'approved_at' => Yii::t('rms', 'Approved At'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function exists()
    {
        return self::find()->where(['ras_relId' => $this->ras_relId, 'inst_relId' => $this->inst_relId])->count() > 0;
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'ras_relId' => $this->ras_relId,
            'inst_relId' => $this->inst_relId,
            'approval_status' => $this->approval_status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]));
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[InstRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInstRel()
    {
        return $this->hasOne(Instructor::class, ['id' => 'inst_relId']);
    }

    /**
     * Gets query for [[RasRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasRel()
    {
        return $this->hasOne(ReassessmentHistory::class, ['id' => 'ras_relId']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
