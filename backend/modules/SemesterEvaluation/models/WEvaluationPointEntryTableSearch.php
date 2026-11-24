<?php

namespace backend\modules\SemesterEvaluation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\SemesterEvaluation\models\WEvaluationPointEntryTable;

/**
 * WEvaluationPointEntryTableSearch represents the model behind the search form about `backend\modules\SemesterEvaluation\models\WEvaluationPointEntryTable`.
 */
class WEvaluationPointEntryTableSearch extends WEvaluationPointEntryTable
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'epet_eet_relId', 'epet_ep_relId'], 'integer'],
            [['epet_max_point'], 'number'],
            [['data'], 'safe'],
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
        $query = WEvaluationPointEntryTable::find();

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
            'epet_eet_relId' => $this->epet_eet_relId,
            'epet_ep_relId' => $this->epet_ep_relId,
            'epet_max_point' => $this->epet_max_point,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
