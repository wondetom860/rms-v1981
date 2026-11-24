<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AuthOrg8n;

/**
 * AuthOrg8nSearch represents the model behind the search form of `common\models\AuthOrg8n`.
 */
class AuthOrg8nSearch extends AuthOrg8n
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'updated_by', 'created_by'], 'integer'],
            [['name', 'abrvn', 'logo', 'website', 'email', 'phone1', 'phone2', 'active_from', 'active_to', 'lic_entry', 'updated_at', 'created_at', 'data'], 'safe'],
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
        $query = AuthOrg8n::find();

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
            'active_from' => $this->active_from,
            'active_to' => $this->active_to,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'abrvn', $this->abrvn])
            ->andFilterWhere(['like', 'logo', $this->logo])
            ->andFilterWhere(['like', 'website', $this->website])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone1', $this->phone1])
            ->andFilterWhere(['like', 'phone2', $this->phone2])
            ->andFilterWhere(['like', 'lic_entry', $this->lic_entry])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
