<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\StudentType;

/**
 * StudentTypeSearch represents the model behind the search form about `backend\models\StudentType`.
 */
class StudentTypeSearch extends StudentType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'stdType_created_by', 'stdType_updated_by'], 'integer'],
            [['stdType_name', 'stdType_desc', 'stdType_created_at', 'stdType_updated_at'], 'safe'],
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
        $query = StudentType::find();

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
            'stdType_created_by' => $this->stdType_created_by,
            'stdType_created_at' => $this->stdType_created_at,
            'stdType_updated_by' => $this->stdType_updated_by,
            'stdType_updated_at' => $this->stdType_updated_at,
        ]);

        $query->andFilterWhere(['like', 'stdType_name', $this->stdType_name])
            ->andFilterWhere(['like', 'stdType_desc', $this->stdType_desc]);

        return $dataProvider;
    }
}
