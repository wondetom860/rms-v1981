<?php

namespace backend\modules\SemesterEvaluation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\SemesterEvaluation\models\WEvaluationTypes;

/**
 * WEvaluationTypesSearch represents the model behind the search form about `backend\modules\SemesterEvaluation\models\WEvaluationTypes`.
 */
class WEvaluationTypesSearch extends WEvaluationTypes
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'et_per_course'], 'integer'],
            [['et_name', 'et_description', 'data'], 'safe'],
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
        $query = WEvaluationTypes::find();

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
            'et_per_course' => $this->et_per_course,
        ]);

        $query->andFilterWhere(['like', 'et_name', $this->et_name])
            ->andFilterWhere(['like', 'et_description', $this->et_description])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
