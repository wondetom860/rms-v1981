<?php

namespace backend\modules\SemesterEvaluation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\SemesterEvaluation\models\WSemesterEvaluation;

/**
 * WSemesterEvaluationSearch represents the model behind the search form about `backend\modules\SemesterEvaluation\models\WSemesterEvaluation`.
 */
class WSemesterEvaluationSearch extends WSemesterEvaluation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'se_eet_relId', 'se_reviewee_relId', 'se_acadYear_relId', 'se_sem_relId', 'se_coff2det_relId'], 'integer'],
            [['se_total_point_given'], 'number'],
            [['se_date_started', 'data'], 'safe'],
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
        $query = WSemesterEvaluation::find();

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
            'se_eet_relId' => $this->se_eet_relId,
            'se_reviewee_relId' => $this->se_reviewee_relId,
            'se_total_point_given' => $this->se_total_point_given,
            'se_date_started' => $this->se_date_started,
            'se_acadYear_relId' => $this->se_acadYear_relId,
            'se_sem_relId' => $this->se_sem_relId,
            'se_coff2det_relId' => $this->se_coff2det_relId,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
