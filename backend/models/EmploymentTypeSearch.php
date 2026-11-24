<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EmploymentType;

/**
 * EmploymentTypeSearch represents the model behind the search form of `backend\models\EmploymentType`.
 */
class EmploymentTypeSearch extends EmploymentType
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cont_renewal_interval_month', 'et_created_by', 'et_updated_by'], 'integer'],
            [['et_name', 'et_created_at', 'et_updated_at', 'remarks', 'data'], 'safe'],
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
        $query = EmploymentType::find();

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
            'cont_renewal_interval_month' => $this->cont_renewal_interval_month,
        ]);

        $query->andFilterWhere(['like', 'et_name', $this->et_name])
            ->andFilterWhere(['like', 'remarks', $this->remarks]);

        return $dataProvider;
    }
}
