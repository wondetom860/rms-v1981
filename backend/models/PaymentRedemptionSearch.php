<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PaymentRedemption;

/**
 * PaymentRedemptionSearch represents the model behind the search form of `backend\models\PaymentRedemption`.
 */
class PaymentRedemptionSearch extends PaymentRedemption
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'acadYear_relId', 'sem_relId', 'std_relId', 'approved_by', 'approved_at', 'used', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['reason', 'comments', 'data'], 'safe'],
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
        $query = PaymentRedemption::find()
            ->orderBy(['acadYear_relId' => SORT_DESC,'sem_relId' => SORT_DESC, 'approved_by' => SORT_ASC]);

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
            'acadYear_relId' => $this->acadYear_relId,
            'sem_relId' => $this->sem_relId,
            'std_relId' => $this->std_relId,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'used' => $this->used,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'comments', $this->comments]);

        return $dataProvider;
    }
}
