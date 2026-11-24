<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SemesterEvaluationPoints;

/**
 * SemesterEvaluationPointsSearch represents the model behind the search form about `backend\models\SemesterEvaluationPoints`.
 */
class SemesterEvaluationPointsSearch extends SemesterEvaluationPoints
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['sem_eval_point_desc', 'semevPoint_semev_relId', 'remark'], 'safe'],
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
        $query = SemesterEvaluationPoints::find();

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
            'semevPoint_semev_relId'=>$this->semevPoint_semev_relId,
        ]);

        $query->andFilterWhere(['like', 'sem_eval_point_desc', $this->sem_eval_point_desc])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
