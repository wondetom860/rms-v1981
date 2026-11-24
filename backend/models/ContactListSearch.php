<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ContactList;

/**
 * ContactListSearch represents the model behind the search form of `backend\models\ContactList`.
 */
class ContactListSearch extends ContactList
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cl_catagory_relId', 'cl_created_by', 'cl_updated_by'], 'integer'],
            [['cl_full_name', 'cl_phone_office', 'cl_phone_mobile', 'cl_phone_ext', 'cl_created_at', 'cl_updated_at', 'data'], 'safe'],
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
        $query = ContactList::find();

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
            'cl_catagory_relId' => $this->cl_catagory_relId,
            'cl_created_at' => $this->cl_created_at,
            'cl_updated_at' => $this->cl_updated_at,
            'cl_created_by' => $this->cl_created_by,
            'cl_updated_by' => $this->cl_updated_by,
        ]);

        $query->andFilterWhere(['like', 'cl_full_name', $this->cl_full_name])
            ->andFilterWhere(['like', 'cl_phone_office', $this->cl_phone_office])
            ->andFilterWhere(['like', 'cl_phone_mobile', $this->cl_phone_mobile])
            ->andFilterWhere(['like', 'cl_phone_ext', $this->cl_phone_ext])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
