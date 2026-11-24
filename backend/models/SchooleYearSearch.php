<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SchoolYear;

/**
 * SchooleYearSearch represents the model behind the search form about `backend\models\SchoolYear`.
 */
class SchooleYearSearch extends SchoolYear
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sch_created_by', 'sch_updated_by'], 'integer'],
            [['sch_year_desc', 'sch_year_status', 'sch_created_at', 'sch_updated_at'], 'safe'],
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
        $query = SchoolYear::find();

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
            'sch_created_by' => $this->sch_created_by,
            'sch_created_at' => $this->sch_created_at,
            'sch_updated_by' => $this->sch_updated_by,
            'sch_updated_at' => $this->sch_updated_at,
        ]);

        $query->andFilterWhere(['like', 'sch_year_desc', $this->sch_year_desc])
            ->andFilterWhere(['like', 'sch_year_status', $this->sch_year_status]);

        return $dataProvider;
    }
}
