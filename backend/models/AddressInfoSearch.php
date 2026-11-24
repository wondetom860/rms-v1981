<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AddressInfo;

/**
 * AddressInfoSearch represents the model behind the search form about `backend\models\AddressInfo`.
 */
class AddressInfoSearch extends AddressInfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'address_country_relId', 'address_region_relId'], 'integer'],
            [['address_zone', 'address_woreda', 'address_town', 'address_kebele', 'address_hourse_no'], 'safe'],
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
        $query = AddressInfo::find();

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
            'address_country_relId' => $this->address_country_relId,
            'address_region_relId' => $this->address_region_relId,
        ]);

        $query->andFilterWhere(['like', 'address_zone', $this->address_zone])
            ->andFilterWhere(['like', 'address_woreda', $this->address_woreda])
            ->andFilterWhere(['like', 'address_town', $this->address_town])
            ->andFilterWhere(['like', 'address_kebele', $this->address_kebele])
            ->andFilterWhere(['like', 'address_hourse_no', $this->address_hourse_no]);

        return $dataProvider;
    }
}
