<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\StudentGroup;

/**
 * StudentGroupSearch represents the model behind the search form about `backend\models\StudentGroup`.
 */
class StudentGroupSearch extends StudentGroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'group_name', 'group_created_by', 'group_updated_by'], 'integer'],
            [['group_created_at', 'group_updated_at'], 'safe'],
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
        $query = StudentGroup::find();

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
            'group_name' => $this->group_name,
            'group_created_by' => $this->group_created_by,
            'group_created_at' => $this->group_created_at,
            'group_updated_by' => $this->group_updated_by,
            'group_updated_at' => $this->group_updated_at,
        ]);

        return $dataProvider;
    }
}
