<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SemesterEvaluationDetail;

/**
 * SemesterEvaluationDetailSearch represents the model behind the search form about `backend\models\SemesterEvaluationDetail`.
 */
class SemesterEvaluationDetailSearch extends SemesterEvaluationDetail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'semevdet_semev_relId', 'semevdet_evalType_relId', 'semevdet_completed', 'semevdet_evaluee_relId'], 'integer'],
            [['semevdet_averagePoint'], 'number'],
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
        $query = SemesterEvaluationDetail::find();

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
            'semevdet_semev_relId' => $this->semevdet_semev_relId,
            'semevdet_evalType_relId' => $this->semevdet_evalType_relId,
            'semevdet_completed' => $this->semevdet_completed,
            'semevdet_averagePoint' => $this->semevdet_averagePoint,
            'semevdet_evaluee_relId' => $this->semevdet_evaluee_relId,
        ]);

        return $dataProvider;
    }
}
