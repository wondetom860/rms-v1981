<?php

namespace frontend\modules\AcStEva\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\AcStEva\models\AseCriteriaCategory;

/**
 * AseCriteriaCategorySearch represents the model behind the search form of `frontend\modules\AcStEva\models\AseCriteriaCategory`.
 */
class AseCriteriaCategorySearch extends AseCriteriaCategory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'created_at', 'updated_at', 'updated_by'], 'integer'],
            [['category_name', 'data'], 'safe'],
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
        $query = AseCriteriaCategory::find();

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
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'category_name', $this->category_name]);

        return $dataProvider;
    }
}
