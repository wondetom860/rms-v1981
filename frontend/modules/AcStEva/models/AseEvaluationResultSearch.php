<?php

namespace frontend\modules\AcStEva\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\AcStEva\models\AseEvaluationResult;

/**
 * AseEvaluationResultSearch represents the model behind the search form of `frontend\modules\AcStEva\models\AseEvaluationResult`.
 */
class AseEvaluationResultSearch extends AseEvaluationResult
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'control_setting_relId', 'criteria_bundle_relId', 'dept_plan_relId', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['completed_staffs', 'data'], 'safe'],
            [['average_total', 'average_percentage'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AseEvaluationResult::find();
        $query->innerJoinWith('deptPlanRel')
            ->innerJoinWith('controlSettingRel')
            ->innerJoin(
                'ase_semester_evaluation',
                'ase_semester_evaluation.id=ase_department_evaluation_schedule.header_relId'
            );
        if (\Yii::$app->user->can('DepartmentHead')) {
            $query->andFilterWhere(['ase_department_evaluation_schedule.created_by' => \Yii::$app->user->identity->id]);
        }

        $query->groupBy(['candidate_staff_relId', 'dept_plan_relId']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ase_semester_evaluation.academic_year_relId' => $this->control_setting_relId,
            'ase_evaluation_result.criteria_bundle_relId' => $this->criteria_bundle_relId,
            'dept_plan_relId' => $this->dept_plan_relId,
            'average_total' => $this->average_total,
            'average_percentage' => $this->average_percentage,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'completed_staffs', $this->completed_staffs]);

        return $dataProvider;
    }
}
