<?php

namespace backend\modules\SemesterEvaluation\models;

use Yii;

/**
 * This is the model class for table "w_evaluation_types".
 *
 * @property integer $id
 * @property string $et_name
 * @property string $et_description
 * @property integer $et_per_course
 * @property string $data
 *
 * @property WEvaluationEntryTable[] $wEvaluationEntryTables
 */
class WEvaluationTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_evaluation_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['et_name', 'et_description'], 'required'],
            [['et_description', 'data'], 'string'],
            [['et_per_course'], 'integer'],
            [['et_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('decris', 'ID'),
            'et_name' => Yii::t('decris', 'Name'),
            'et_description' => Yii::t('decris', 'Description'),
            'et_per_course' => Yii::t('decris', 'Evaluation Per-Course'),
            'data' => Yii::t('decris', 'Data'),
        ];
    }

    public function getData($modelObj){
        $mainData = (object)[
            'et_name'=>$modelObj->et_name,
            'et_description'=>$modelObj->et_description,
            'et_per_course'=>intval($modelObj->et_per_course),
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered($modelObj){
        return $this->getData($modelObj) == $modelObj->data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWEvaluationEntryTables()
    {
        return $this->hasMany(WEvaluationEntryTable::className(), ['eet_et_relId' => 'id']);
    }
}
