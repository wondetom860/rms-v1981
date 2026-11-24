<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MilitaryRank;

/**
 * MilitaryRankSearch represents the model behind the search form of `backend\models\MilitaryRank`.
 */
class MilitaryRankSearch extends MilitaryRank
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'mr_category_relId', 'mr_accronym', 'mr_order_count', 'mr_duration_month', 'created_by', 'updated_by'], 'integer'],
            [['mr_rank_name', 'created_at', 'updated_at', 'data'], 'safe'],
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
        $query = MilitaryRank::find();

        $query->orderBy(['mr_order_count' => SORT_ASC]);
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
            'mr_category_relId' => $this->mr_category_relId,
            'mr_order_count' => $this->mr_order_count,
            'mr_duration_month' => $this->mr_duration_month,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'mr_rank_name', $this->mr_rank_name])
            ->andFilterWhere(['like', 'mr_accronym', $this->mr_accronym])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
