<?php

namespace backend\modules\SemesterEvaluation\models;

use Yii;

/**
 * This is the model class for table "w_evaluation_entry_table".
 *
 * @property integer $id
 * @property integer $eet_et_relId
 * @property string $eet_evaluator_role
 * @property string $eet_reviewee_role
 * @property double $eet_max_point
 * @property integer $eet_created_by
 * @property string $eet_created_at
 * @property string $eet_updated_at
 * @property integer $eet_updated_by
 * @property string $data
 *
 * @property User $eetCreatedBy
 * @property WEvaluationTypes $eetEtRel
 * @property User $eetUpdatedBy
 * @property WEvaluationPointEntryTable[] $wEvaluationPointEntryTables
 * @property WSemesterEvaluation[] $wSemesterEvaluations
 */
class WEvaluationEntryTable extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_evaluation_entry_table';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['eet_et_relId', 'eet_evaluator_role', 'eet_reviewee_role', 'eet_max_point', 'eet_created_by', 'eet_created_at', 'data'], 'required'],
            [['eet_et_relId', 'eet_created_by', 'eet_updated_by'], 'integer'],
            [['eet_max_point'], 'number'],
            [['eet_created_at', 'eet_updated_at'], 'safe'],
            [['data'], 'string'],
            [['eet_evaluator_role', 'eet_reviewee_role'], 'string', 'max' => 30],
            [['eet_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['eet_created_by' => 'id']],
            [['eet_et_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WEvaluationTypes::className(), 'targetAttribute' => ['eet_et_relId' => 'id']],
            [['eet_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['eet_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('decris', 'ID'),
            'eet_et_relId' => Yii::t('decris', 'Evaluation Type'),
            'eet_evaluator_role' => Yii::t('decris', 'Evaluator Role'),
            'eet_reviewee_role' => Yii::t('decris', 'Reviewee Role'),
            'eet_max_point' => Yii::t('decris', 'Max. Point'),
            'eet_created_by' => Yii::t('decris', 'Created By'),
            'eet_created_at' => Yii::t('decris', 'Created At'),
            'eet_updated_at' => Yii::t('decris', 'Updated At'),
            'eet_updated_by' => Yii::t('decris', 'Updated By'),
            'data' => Yii::t('decris', 'Data'),
        ];
    }


    /*
    // Get evaluation configurations for the current user
    // Takes the current user info model for cross checking...
    // returns array of evaluation types as per the configuration
    */
    public function getEvaluationConfs(){
        if ($this->find()->count() > 0) {//check if configuration if completed before
            $userRole = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
            if (is_array($userRole)) {
                $evaluatorRole = array_shift($userRole)->name;
                return $this->find()->where(['eet_evaluator_role'=>$evaluatorRole])->all();
            }
        }
        throw new \yii\web\NotFoundHttpException("Semester Evaluation Configuration NOT completed yet.", 1);
    }

    public function getData($modelObj){
        $mainData = (object)[
            'eet_et_relId'=>intval($modelObj->eet_et_relId),
            'eet_evaluator_role'=>$modelObj->eet_evaluator_role,
            'eet_reviewee_role'=>$modelObj->eet_reviewee_role,
            'eet_max_point'=>doubleval($modelObj->eet_max_point),
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered($modelObj){
        return $this->getData($modelObj) == $modelObj->data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEetCreatedBy()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'eet_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEetEtRel()
    {
        return $this->hasOne(WEvaluationTypes::className(), ['id' => 'eet_et_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEetUpdatedBy()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'eet_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWEvaluationPointEntryTables()
    {
        return $this->hasMany(WEvaluationPointEntryTable::className(), ['epet_eet_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWSemesterEvaluations()
    {
        return $this->hasMany(WSemesterEvaluation::className(), ['se_eet_relId' => 'id']);
    }
}
