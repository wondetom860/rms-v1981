<?php

namespace backend\modules\SemesterEvaluation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\SemesterEvaluation\models\WEvaluationPoints;

/**
 * WEvaluationPointsSearch represents the model behind the search form about `backend\modules\SemesterEvaluation\models\WEvaluationPoints`.
 */
class WEvaluationPointsSearch extends WEvaluationPoints
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ep_created_by', 'ep_updated_by'], 'integer'],
            [['ep_desc', 'ep_ceated_at', 'ep_updated_at', 'data'], 'safe'],
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
        $query = WEvaluationPoints::find();

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
            'ep_ceated_at' => $this->ep_ceated_at,
            'ep_created_by' => $this->ep_created_by,
            'ep_updated_at' => $this->ep_updated_at,
            'ep_updated_by' => $this->ep_updated_by,
        ]);

        $query->andFilterWhere(['like', 'ep_desc', $this->ep_desc])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
