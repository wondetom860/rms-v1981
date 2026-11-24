<?php

namespace backend\modules\SemesterEvaluation\models;

use Yii;

/**
 * This is the model class for table "w_evaluation_points".
 *
 * @property integer $id
 * @property string $ep_desc
 * @property string $ep_ceated_at
 * @property integer $ep_created_by
 * @property string $ep_updated_at
 * @property integer $ep_updated_by
 * @property string $data
 *
 * @property WEvaluationPointEntryTable[] $wEvaluationPointEntryTables
 * @property User $epCreatedBy
 * @property User $epUpdatedBy
 * @property WSemesterEvaluationDetail[] $wSemesterEvaluationDetails
 */
class WEvaluationPoints extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_evaluation_points';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ep_desc', 'ep_ceated_at', 'ep_created_by', 'data'], 'required'],
            [['ep_desc', 'data'], 'string'],
            [['ep_ceated_at', 'ep_updated_at'], 'safe'],
            [['ep_created_by', 'ep_updated_by'], 'integer'],
            [['ep_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['ep_created_by' => 'id']],
            [['ep_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['ep_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('decris', 'ID'),
            'ep_desc' => Yii::t('decris', 'Evaluation Point Desc.'),
            'ep_ceated_at' => Yii::t('decris', 'Ceated At'),
            'ep_created_by' => Yii::t('decris', 'Created By'),
            'ep_updated_at' => Yii::t('decris', 'Updated At'),
            'ep_updated_by' => Yii::t('decris', 'Updated By'),
            'data' => Yii::t('decris', 'Data'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWEvaluationPointEntryTables()
    {
        return $this->hasMany(WEvaluationPointEntryTable::className(), ['epet_ep_relId' => 'id']);
    }

    public function getData($modelObj){
        $mainData = (object)[
            'ep_desc'=>$modelObj->ep_desc,
            'ep_ceated_at'=>$modelObj->ep_ceated_at,
            'ep_updated_at'=>$modelObj->ep_updated_at,
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered($modelObj){
        return $this->getData($modelObj) == $modelObj->data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEpCreatedBy()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'ep_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEpUpdatedBy()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'ep_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWSemesterEvaluationDetails()
    {
        return $this->hasMany(WSemesterEvaluationDetail::className(), ['sed_ep_relId' => 'id']);
    }
}
