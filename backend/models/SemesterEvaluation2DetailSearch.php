<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SemesterEvaluation2Detail;

/**
 * SemesterEvaluation2DetailSearch represents the model behind the search form about `backend\models\SemesterEvaluation2Detail`.
 */
class SemesterEvaluation2DetailSearch extends SemesterEvaluation2Detail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'semev2det_semev_relId', 'semev2det_evaluator_relId', 'semev2det_completed'], 'integer'],
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
        $query = SemesterEvaluation2Detail::find();

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
            'semev2det_semev_relId' => $this->semev2det_semev_relId,
            'semev2det_evaluator_relId' => $this->semev2det_evaluator_relId,
            'semev2det_completed' => $this->semev2det_completed,
        ]);

        return $dataProvider;
    }
}
