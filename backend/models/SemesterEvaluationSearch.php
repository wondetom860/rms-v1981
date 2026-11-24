<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SemesterEvaluation;

/**
 * SemesterEvaluationSearch represents the model behind the search form about `backend\models\SemesterEvaluation`.
 */
class SemesterEvaluationSearch extends SemesterEvaluation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'semev_acadYear_relId', 'semev_sem_relId', 'semev_evaluator_relId'], 'integer'],
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
        $query = SemesterEvaluation::find();

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
            'semev_acadYear_relId' => $this->semev_acadYear_relId,
            'semev_sem_relId' => $this->semev_sem_relId,
            'semev_evaluator_relId' => $this->semev_evaluator_relId,
        ]);

        return $dataProvider;
    }
}
