<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\WGroupAssignment;

/**
 * WGroupAssignmentSearch represents the model behind the search form about `backend\models\WGroupAssignment`.
 */
class WGroupAssignmentSearch extends WGroupAssignment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'gas_gset_relId', 'gas_std_relId'], 'integer'],
            [['gas_created_at'], 'safe'],
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
        $query = WGroupAssignment::find();

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
            'gas_gset_relId' => $this->gas_gset_relId,
            'gas_std_relId' => $this->gas_std_relId,
            'gas_created_at' => $this->gas_created_at,
        ]);

        return $dataProvider;
    }
}
