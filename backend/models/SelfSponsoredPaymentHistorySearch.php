<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SelfSponsoredPaymentHistory;

/**
 * SelfSponsoredPaymentHistorySearch represents the model behind the search form about `backend\models\SelfSponsoredPaymentHistory`.
 */
class SelfSponsoredPaymentHistorySearch extends SelfSponsoredPaymentHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ssph_std_relId', 'ssph_acadYear_relId', 'ssph_sem_relId', 'ssph_approved', 'ssph_approved_by'], 'integer'],
            [['ssph_approved_at', 'ssph_txn_num', 'ssph_RCT_scan_path'], 'safe'],
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
        $query = SelfSponsoredPaymentHistory::find();

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
            'ssph_std_relId' => $this->ssph_std_relId,
            'ssph_acadYear_relId' => $this->ssph_acadYear_relId,
            'ssph_sem_relId' => $this->ssph_sem_relId,
            'ssph_approved' => $this->ssph_approved,
            'ssph_approved_at' => $this->ssph_approved_at,
            'ssph_approved_by' => $this->ssph_approved_by,
        ]);

        $query->andFilterWhere(['like', 'ssph_txn_num', $this->ssph_txn_num])
            ->andFilterWhere(['like', 'ssph_RCT_scan_path', $this->ssph_RCT_scan_path]);

        return $dataProvider;
    }
}
