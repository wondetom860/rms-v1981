<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ScholarshipType;

/**
 * ScholarshipTypeSearch represents the model behind the search form about `backend\models\ScholarshipType`.
 */
class ScholarshipTypeSearch extends ScholarshipType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'schType_created_by', 'schType_updated_by','schType_pay'], 'integer'],
            [['schType_name', 'schType_by', 'schType_desc', 'schType_created_at', 'schType_updated_at'], 'safe'],
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
        $query = ScholarshipType::find();

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
            'schType_created_by' => $this->schType_created_by,
            'schType_created_at' => $this->schType_created_at,
            'schType_updated_at' => $this->schType_updated_at,
            'schType_updated_by' => $this->schType_updated_by,
            'schType_pay' => $this->schType_pay,
        ]);

        $query->andFilterWhere(['like', 'schType_name', $this->schType_name])
            ->andFilterWhere(['like', 'schType_by', $this->schType_by])
            ->andFilterWhere(['like', 'schType_desc', $this->schType_desc]);
            // 

        return $dataProvider;
    }
}
