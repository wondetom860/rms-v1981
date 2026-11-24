<?php

namespace backend\modules\SemesterEvaluation\models;

use Yii;

/**
 * This is the model class for table "w_semester_evaluation_detail".
 *
 * @property integer $id
 * @property integer $sed_se_relId
 * @property integer $sed_ep_relId
 * @property double $sed_point_given
 * @property string $data
 *
 * @property WEvaluationPoints $sedEpRel
 * @property WSemesterEvaluation $sedSeRel
 */
class WSemesterEvaluationDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_semester_evaluation_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sed_se_relId', 'sed_ep_relId', 'sed_point_given', 'data'], 'required'],
            [['sed_se_relId', 'sed_ep_relId'], 'integer'],
            [['sed_point_given'], 'number'],
            [['data'], 'string'],
            [['sed_ep_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WEvaluationPoints::className(), 'targetAttribute' => ['sed_ep_relId' => 'id']],
            [['sed_se_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WSemesterEvaluation::className(), 'targetAttribute' => ['sed_se_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('decris', 'ID'),
            'sed_se_relId' => Yii::t('decris', 'Sed Se Rel ID'),
            'sed_ep_relId' => Yii::t('decris', 'Sed Ep Rel ID'),
            'sed_point_given' => Yii::t('decris', 'Sed Point Given'),
            'data' => Yii::t('decris', 'Data'),
        ];
    }

    public function getData($modelObj){
        $mainData = (object)[
            'sed_point_given'=>floatval($modelObj->sed_point_given),
            'sed_se_relId'=>intval($modelObj->sed_se_relId),
            'sed_ep_relId'=>intval($modelObj->sed_ep_relId)
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered($modelObj){
        return $this->getData($modelObj) == $modelObj->data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSedEpRel()
    {
        return $this->hasOne(WEvaluationPoints::className(), ['id' => 'sed_ep_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSedSeRel()
    {
        return $this->hasOne(WSemesterEvaluation::className(), ['id' => 'sed_se_relId']);
    }
}
