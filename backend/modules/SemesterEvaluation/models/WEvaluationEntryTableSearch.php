<?php

namespace backend\modules\SemesterEvaluation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\SemesterEvaluation\models\WEvaluationEntryTable;

/**
 * WEvaluationEntryTableSearch represents the model behind the search form about `backend\modules\SemesterEvaluation\models\WEvaluationEntryTable`.
 */
class WEvaluationEntryTableSearch extends WEvaluationEntryTable
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'eet_et_relId', 'eet_created_by', 'eet_updated_by'], 'integer'],
            [['eet_evaluator_role', 'eet_reviewee_role', 'eet_created_at', 'eet_updated_at', 'data'], 'safe'],
            [['eet_max_point'], 'number'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = WEvaluationEntryTable::find();

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
            'eet_et_relId' => $this->eet_et_relId,
            'eet_max_point' => $this->eet_max_point,
            'eet_created_by' => $this->eet_created_by,
            'eet_created_at' => $this->eet_created_at,
            'eet_updated_at' => $this->eet_updated_at,
            'eet_updated_by' => $this->eet_updated_by,
        ]);

        $query->andFilterWhere(['like', 'eet_evaluator_role', $this->eet_evaluator_role])
            ->andFilterWhere(['like', 'eet_reviewee_role', $this->eet_reviewee_role])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
