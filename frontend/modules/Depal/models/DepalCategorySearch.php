<?php

namespace frontend\modules\Depal\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\Depal\models\DepalCategory;

/**
 * DepalCategorySearch represents the model behind the search form of `frontend\modules\Depal\models\DepalCategory`.
 */
class DepalCategorySearch extends DepalCategory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['category_name', 'comparison_type', 'attribute', 'data'], 'safe'],
            [['lower_bound', 'upper_bound'], 'number'],
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
        $query = DepalCategory::find();

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
            'lower_bound' => $this->lower_bound,
            'upper_bound' => $this->upper_bound,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'category_name', $this->category_name])
            ->andFilterWhere(['like', 'comparison_type', $this->comparison_type])
            ->andFilterWhere(['like', 'attribute', $this->attribute])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
