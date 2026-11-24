<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EmergencyContact;

/**
 * EmergencyContactSearch represents the model behind the search form about `backend\models\EmergencyContact`.
 */
class EmergencyContactSearch extends EmergencyContact
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ec_address_relId'], 'integer'],
            [['ec_first_name', 'ec_last_name', 'ec_relationShip', 'ec_phone_home', 'ec_phone_mobile', 'ec_email'], 'safe'],
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
        $query = EmergencyContact::find();

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
            'ec_address_relId' => $this->ec_address_relId,
        ]);

        $query->andFilterWhere(['like', 'ec_first_name', $this->ec_first_name])
            ->andFilterWhere(['like', 'ec_last_name', $this->ec_last_name])
            ->andFilterWhere(['like', 'ec_relationShip', $this->ec_relationShip])
            ->andFilterWhere(['like', 'ec_phone_home', $this->ec_phone_home])
            ->andFilterWhere(['like', 'ec_phone_mobile', $this->ec_phone_mobile])
            ->andFilterWhere(['like', 'ec_email', $this->ec_email]);

        return $dataProvider;
    }
}
