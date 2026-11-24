<?php

namespace frontend\modules\AcStEva\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\AcStEva\models\AseSemesterEvaluation;

/**
 * AseSemesterEvaluationSearch represents the model behind the search form of `frontend\modules\AcStEva\models\AseSemesterEvaluation`.
 */
class AseSemesterEvaluationSearch extends AseSemesterEvaluation
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'academic_year_relId', 'start_date', 'end_date', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['evaluation_header', 'criteria_bundle_IDS', 'data'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = AseSemesterEvaluation::find();

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
            'academic_year_relId' => $this->academic_year_relId,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'evaluation_header', $this->evaluation_header])
            ->andFilterWhere(['like', 'criteria_bundle_IDS', $this->criteria_bundle_IDS])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
