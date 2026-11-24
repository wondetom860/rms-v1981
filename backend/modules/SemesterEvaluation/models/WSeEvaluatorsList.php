<?php

namespace backend\modules\SemesterEvaluation\models;

use Yii;

/**
 * This is the model class for table "w_se_evaluators_list".
 *
 * @property integer $id
 * @property integer $sel_se_relId
 * @property integer $sel_evaluator_relId
 * @property string $se_date_started
 * @property string $se_date_finished
 * @property string $data
 *
 * @property User $selEvaluatorRel
 * @property WSemesterEvaluation $selSeRel
 */
class WSeEvaluatorsList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_se_evaluators_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sel_se_relId', 'sel_evaluator_relId', 'se_date_started', 'data'], 'required'],
            [['sel_se_relId', 'sel_evaluator_relId'], 'integer'],
            [['se_date_started', 'se_date_finished'], 'safe'],
            [['data'], 'string'],
            [['sel_evaluator_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['sel_evaluator_relId' => 'id']],
            [['sel_se_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WSemesterEvaluation::className(), 'targetAttribute' => ['sel_se_relId' => 'id']],
        ];
    }


    public function getData($modelObj){
        $mainData = (object)[
            'sel_se_relId'=> intval($modelObj->sel_se_relId) ,
            'sel_evaluator_relId'=>intval($modelObj->sel_evaluator_relId),
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered($modelObj){
        return $this->getData($modelObj) == $modelObj->data;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('decris', 'ID'),
            'sel_se_relId' => Yii::t('decris', 'Sel Se Rel ID'),
            'sel_evaluator_relId' => Yii::t('decris', 'Sel Evaluator Rel ID'),
            'se_date_started' => Yii::t('decris', 'Se Date Started'),
            'se_date_finished' => Yii::t('decris', 'Se Date Finished'),
            'data' => Yii::t('decris', 'Data'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSelEvaluatorRel()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'sel_evaluator_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSelSeRel()
    {
        return $this->hasOne(WSemesterEvaluation::className(), ['id' => 'sel_se_relId']);
    }
}
