<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CalConv;

/**
 * CalConvSearch represents the model behind the search form about `backend\models\CalConv`.
 */
class CalConvSearch extends CalConv
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cc_ger_cal', 'cc_et_cal'], 'integer'],
            [['cc_by'], 'safe'],
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
        $query = CalConv::find();

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
            'cc_ger_cal' => $this->cc_ger_cal,
            'cc_et_cal' => $this->cc_et_cal,
        ]);

        $query->andFilterWhere(['like', 'cc_by', $this->cc_by]);

        return $dataProvider;
    }
}
