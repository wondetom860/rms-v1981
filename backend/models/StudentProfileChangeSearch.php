<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\StudentProfileChange;

/**
 * StudentProfileChangeSearch represents the model behind the search form of `backend\models\StudentProfileChange`.
 */
class StudentProfileChangeSearch extends StudentProfileChange
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'std_relId', 'approval_status', 'approved_by', 'approved_at', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['std_attribute', 'attribute_label', 'old_value', 'new_value', 'data','reason','relation_name','related_attribute_name'], 'safe'],
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
        $query = StudentProfileChange::find();

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
            'std_relId' => $this->std_relId,
            'approval_status' => $this->approval_status,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'std_attribute', $this->std_attribute])
            ->andFilterWhere(['like', 'attribute_label', $this->attribute_label])
            ->andFilterWhere(['like', 'old_value', $this->old_value])
            ->andFilterWhere(['like', 'new_value', $this->new_value])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
