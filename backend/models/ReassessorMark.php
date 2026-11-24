<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "reassessor_mark".
 *
 * @property int $id
 * @property int $ras_relId
 * @property int $ras_ima_relId
 * @property float $ras_original_mark
 * @property float|null $ras_changed_to
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $createdBy
 * @property InstanceMarkAssignment $rasImaRel
 * @property ReassessmentHistory $rasRel
 * @property User $updatedBy
 */
class ReassessorMark extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reassessor_mark';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ras_relId', 'ras_ima_relId', 'ras_original_mark', 'created_by', 'created_at', 'data'], 'required'],
            [['ras_relId', 'ras_ima_relId', 'created_by', 'created_at', 'updated_at', 'updated_by'], 'integer'],
            [['ras_original_mark', 'ras_changed_to'], 'number'],
            [['data'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['ras_ima_relId'], 'exist', 'skipOnError' => true, 'targetClass' => InstanceMarkAssignment::className(), 'targetAttribute' => ['ras_ima_relId' => 'id']],
            [['ras_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ReassessmentHistory::className(), 'targetAttribute' => ['ras_relId' => 'id']],
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
            'ras_relId' => Yii::t('rms', 'Ras Rel ID'),
            'ras_ima_relId' => Yii::t('rms', 'Ras Ima Rel ID'),
            'ras_original_mark' => Yii::t('rms', 'Ras Original Mark'),
            'ras_changed_to' => Yii::t('rms', 'Ras Changed To'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
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
     * Gets query for [[RasImaRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasImaRel()
    {
        return $this->hasOne(InstanceMarkAssignment::className(), ['id' => 'ras_ima_relId']);
    }

    /**
     * Gets query for [[RasRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasRel()
    {
        return $this->hasOne(ReassessmentHistory::className(), ['id' => 'ras_relId']);
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
}
