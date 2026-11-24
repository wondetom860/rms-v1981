<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SemesterEvaluationResult;

/**
 * SemesterEvaluationResultSearch represents the model behind the search form about `backend\models\SemesterEvaluationResult`.
 */
class SemesterEvaluationResultSearch extends SemesterEvaluationResult
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ser_semevdet_relId', 'ser_semev_point_relId', 'ser_point_given'], 'integer'],
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
        $query = SemesterEvaluationResult::find();

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
            'ser_semevdet_relId' => $this->ser_semevdet_relId,
            'ser_semev_point_relId' => $this->ser_semev_point_relId,
            'ser_point_given' => $this->ser_point_given,
        ]);

        return $dataProvider;
    }
}
