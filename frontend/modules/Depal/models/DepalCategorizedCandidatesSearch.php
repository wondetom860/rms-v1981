<?php

namespace frontend\modules\Depal\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\Depal\models\DepalCategorizedCandidates;

/**
 * DepalCategorizedCandidatesSearch represents the model behind the search form of `frontend\modules\Depal\models\DepalCategorizedCandidates`.
 */
class DepalCategorizedCandidatesSearch extends DepalCategorizedCandidates
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'setting_relId', 'category_relId', 'finalized', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['criteria_name', 'candidates', 'data'], 'safe'],
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
        $query = DepalCategorizedCandidates::find();

        // add conditions that should always apply here

        $query->orderBy(['setting_relId' => SORT_DESC]);
        
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
            'setting_relId' => $this->setting_relId,
            'category_relId' => $this->category_relId,
            'finalized' => $this->finalized,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'criteria_name', $this->criteria_name])
            ->andFilterWhere(['like', 'candidates', $this->candidates])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
