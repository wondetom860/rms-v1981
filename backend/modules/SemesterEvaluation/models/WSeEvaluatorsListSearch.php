<?php

namespace backend\modules\SemesterEvaluation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\SemesterEvaluation\models\WSeEvaluatorsList;

/**
 * WSeEvaluatorsListSearch represents the model behind the search form about `backend\modules\SemesterEvaluation\models\WSeEvaluatorsList`.
 */
class WSeEvaluatorsListSearch extends WSeEvaluatorsList
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sel_se_relId', 'sel_evaluator_relId'], 'integer'],
            [['se_date_started', 'se_date_finished', 'data'], 'safe'],
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
        $query = WSeEvaluatorsList::find();

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
            'sel_se_relId' => $this->sel_se_relId,
            'sel_evaluator_relId' => $this->sel_evaluator_relId,
            'se_date_started' => $this->se_date_started,
            'se_date_finished' => $this->se_date_finished,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
