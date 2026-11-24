<?php

namespace backend\modules\SemesterEvaluation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\SemesterEvaluation\models\WSemesterEvaluationDetail;

/**
 * WSemesterEvaluationDetailSearch represents the model behind the search form about `backend\modules\SemesterEvaluation\models\WSemesterEvaluationDetail`.
 */
class WSemesterEvaluationDetailSearch extends WSemesterEvaluationDetail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sed_se_relId', 'sed_ep_relId'], 'integer'],
            [['sed_point_given'], 'number'],
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
        $query = WSemesterEvaluationDetail::find();

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
            'sed_se_relId' => $this->sed_se_relId,
            'sed_ep_relId' => $this->sed_ep_relId,
            'sed_point_given' => $this->sed_point_given,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
