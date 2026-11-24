<?php

namespace frontend\modules\AcStEva\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\AcStEva\models\AseCriteriaBundle;

/**
 * AseCriteriaBundleSearch represents the model behind the search form of `frontend\modules\AcStEva\models\AseCriteriaBundle`.
 */
class AseCriteriaBundleSearch extends AseCriteriaBundle
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'eval_type_relId', 'evaluate_per_course', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['code', 'evaluator_role', 'candidate_staff_role', 'criteria_IDS', 'data'], 'safe'],
            [['average_point', 'average_percentage'], 'number'],
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
        $query = AseCriteriaBundle::find();

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
            'id' => $this->id,
            'eval_type_relId' => $this->eval_type_relId,
            'average_point' => $this->average_point,
            'average_percentage' => $this->average_percentage,
            'evaluate_per_course' => $this->evaluate_per_course,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'evaluator_role', $this->evaluator_role])
            ->andFilterWhere(['like', 'candidate_staff_role', $this->candidate_staff_role])
            ->andFilterWhere(['like', 'criteria_IDS', $this->criteria_IDS])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
