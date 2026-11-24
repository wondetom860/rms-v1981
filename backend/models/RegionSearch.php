<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Region;

/**
 * RegionSearch represents the model behind the search form about `backend\models\Region`.
 */
class RegionSearch extends Region
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'region_created_by', 'region_updated_by'], 'integer'],
            [['region_name', 'region_created_at', 'region_updated_at'], 'safe'],
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
        $query = Region::find();

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
            'region_created_by' => $this->region_created_by,
            'region_created_at' => $this->region_created_at,
            'region_updated_by' => $this->region_updated_by,
            'region_updated_at' => $this->region_updated_at,
        ]);

        $query->andFilterWhere(['like', 'region_name', $this->region_name]);

        return $dataProvider;
    }
}
