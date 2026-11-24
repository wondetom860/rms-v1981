<?php

namespace frontend\modules\AcStEva\models;

use Yii;
use backend\models\User;

/**
 * This is the model class for table "ase_criteria_bundle".
 *
 * @property int $id
 * @property string $code 
 * @property int $eval_type_relId
 * @property string $evaluator_role
 * @property string $candidate_staff_role
 * @property float $average_point
 * @property float $average_percentage
 * @property int|null $evaluate_per_course
 * @property string $criteria_IDS 
 * @property string $employment_types
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $createdBy
 * @property AseEvaluationType $evalTypeRel
 * @property User $updatedBy
 * @property AseEvaluationControlSetting[] $aseEvaluationControlSettings
 * @property AseEvaluationResult[] $aseEvaluationResults
 */
class AseCriteriaBundle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ase_criteria_bundle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'eval_type_relId', 'evaluator_role', 'candidate_staff_role', 'average_percentage', 'criteria_IDS', 'created_at', 'created_by', 'data','employment_types'], 'required'],
            [['eval_type_relId', 'evaluate_per_course', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['average_point', 'average_percentage'], 'number'],
            [['criteria_IDS','employment_types'], 'safe'],
            [['data'], 'string'],
            [['code'], 'string', 'max' => 15],
            [['evaluator_role', 'candidate_staff_role'], 'string', 'max' => 25],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['eval_type_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AseEvaluationType::class, 'targetAttribute' => ['eval_type_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'employment_types' => Yii::t('rms', 'Employment Types'),
            'code' => Yii::t('rms', 'Code'),
            'eval_type_relId' => Yii::t('rms', 'Evaluation Type'),
            'evaluator_role' => Yii::t('rms', 'Evaluator Role'),
            'candidate_staff_role' => Yii::t('rms', 'Role of Staff to be evaluated'),
            'average_point' => Yii::t('rms', 'Average Point'),
            'average_percentage' => Yii::t('rms', 'Average Percentage'),
            'evaluate_per_course' => Yii::t('rms', 'Per Course?'),
            'criteria_IDS' => Yii::t('rms', 'Applicable Criteria'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData(){
        $this->data = md5(serialize((object)[
            'code'=>$this->code,
            'criteria_IDS'=>$this->criteria_IDS,
            'employment_types'=>$this->employment_types,
            'evaluator_role'=>$this->evaluator_role,
            'candidate_staff_role'=>$this->candidate_staff_role,
            'average_percentage'=>$this->average_percentage,
            'eval_type_relId'=>intval($this->eval_type_relId),
            'evaluate_per_course'=>intval($this->evaluate_per_course),
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ]));
    }

    public function checkIfExists($id = null){
        if ($id) {
            return self::find()->where(['evaluator_role' => $this->evaluator_role,'candidate_staff_role' => $this->candidate_staff_role])->andWhere(" id <> $id ")->one();
        }
        return self::findOne(['evaluator_role' => $this->evaluator_role,'candidate_staff_role' => $this->candidate_staff_role]);
    }

    public function getRoles(){
        $userModel = new User();
        return $userModel->getRoleTypes();
    }

    public function getBundleCode(){
        $similar = $this->find()->where([
            'evaluator_role' => $this->evaluator_role,
            'candidate_staff_role' => $this->candidate_staff_role,
        ])->count();
        $this->code = strtoupper(substr($this->evaluator_role, 0,2)).strtoupper(substr($this->candidate_staff_role, 0,2))."-".($similar+1);
        return;
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
     * Gets query for [[EvalTypeRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvalTypeRel()
    {
        return $this->hasOne(AseEvaluationType::class, ['id' => 'eval_type_relId']);
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

    /**
     * Gets query for [[AseEvaluationControlSettings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAseEvaluationControlSettings()
    {
        return $this->hasMany(AseEvaluationControlSetting::class, ['criteria_bundle_relId' => 'id']);
    }

    /**
     * Gets query for [[AseEvaluationResults]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAseEvaluationResults()
    {
        return $this->hasMany(AseEvaluationResult::class, ['criteria_bundle_relId' => 'id']);
    }
}
