<?php

namespace frontend\modules\Reassessment\models;

use Yii;
use common\models\User;
use backend\models\InstanceMarkAssignment;

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
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['ras_ima_relId'], 'exist', 'skipOnError' => true, 'targetClass' => InstanceMarkAssignment::class, 'targetAttribute' => ['ras_ima_relId' => 'id']],
            [['ras_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ReassessmentHistory::class, 'targetAttribute' => ['ras_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    public function validMarl()
    {
        return $this->ras_changed_to <= $this->rasImaRel->ima_possiblePoint;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ras_relId' => Yii::t('rms', 'Request Entry'),
            'ras_ima_relId' => Yii::t('rms', 'Assessment Instance'),
            'ras_original_mark' => Yii::t('rms', 'Original Mark'),
            'ras_changed_to' => Yii::t('rms', 'Changed To'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'ras_relId' => $this->ras_relId,
            'ras_ima_relId' => $this->ras_ima_relId,
            'ras_original_mark' => $this->ras_original_mark,
            'ras_changed_to' => $this->ras_changed_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]));
    }

    public function createENtry($contAs, $ras_id)
    {
        $model = new ReassessorMark();
        $model->ras_ima_relId = $contAs->ca_markAssignment_relId;
        $model->ras_relId = $ras_id;
        $model->ras_original_mark = $contAs->ca_markObtained;

        $model->created_at = time();
        $model->created_by = Yii::$app->user->id;
        $model->getData();

        if ($model->save()) {
            return $model;
        } else {
            return NULL;
        }
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
     * Gets query for [[RasImaRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRasImaRel()
    {
        return $this->hasOne(InstanceMarkAssignment::class, ['id' => 'ras_ima_relId']);
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
