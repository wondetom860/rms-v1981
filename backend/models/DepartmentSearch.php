<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Department;

/**
 * DepartmentSearch represents the model behind the search form about `backend\models\Department`.
 */
class DepartmentSearch extends Department
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'dept_created_by', 'dept_updated_by'], 'integer'],
            [['dept_name', 'dept_location', 'dept_phone', 'dept_code', 'dept_created_at', 'dept_updated_at'], 'safe'],
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
        $query = Department::find();

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
            'dept_created_by' => $this->dept_created_by,
            'dept_created_at' => $this->dept_created_at,
            'dept_updated_at' => $this->dept_updated_at,
            'dept_updated_by' => $this->dept_updated_by,
        ]);

        $query->andFilterWhere(['like', 'dept_name', $this->dept_name])
            ->andFilterWhere(['like', 'dept_location', $this->dept_location])
            ->andFilterWhere(['like', 'dept_phone', $this->dept_phone])
            ->andFilterWhere(['like', 'dept_code', $this->dept_code]);

        return $dataProvider;
    }
}
