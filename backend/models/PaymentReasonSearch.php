<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PaymentReason;

/**
 * PaymentReasonSearch represents the model behind the search form about `backend\models\PaymentReason`.
 */
class PaymentReasonSearch extends PaymentReason
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'remark'], 'integer'],
            [['pr_desc'], 'safe'],
            [['pr_amount'], 'number'],
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
        $query = PaymentReason::find();

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
            'pr_amount' => $this->pr_amount,
            'remark' => $this->remark,
        ]);

        $query->andFilterWhere(['like', 'pr_desc', $this->pr_desc]);

        return $dataProvider;
    }
}
