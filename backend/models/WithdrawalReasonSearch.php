<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\WithdrawalReason;

/**
 * WithdrawalReasonSearch represents the model behind the search form of `frontend\models\WithdrawalReason`.
 */
class WithdrawalReasonSearch extends WithdrawalReason
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'withdrawalres_createdBy_relId', 'withdrawalres_updatedby_relId'], 'integer'],
            [['withdrawalres_description', 'withdrawalres_updatedAt', 'withdrawalres_createdAt'], 'safe'],
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
        $query = WithdrawalReason::find();

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
            'withdrawalres_updatedAt' => $this->withdrawalres_updatedAt,
            'withdrawalres_createdBy_relId' => $this->withdrawalres_createdBy_relId,
            'withdrawalres_createdAt' => $this->withdrawalres_createdAt,
            'withdrawalres_updatedby_relId' => $this->withdrawalres_updatedby_relId,
        ]);

        $query->andFilterWhere(['like', 'withdrawalres_description', $this->withdrawalres_description]);

        return $dataProvider;
    }
}
