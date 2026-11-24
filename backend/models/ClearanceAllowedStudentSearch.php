<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ClearanceAllowedStudent;

/**
 * ClearanceAllowedStudentSearch represents the model behind the search form of `backend\models\ClearanceAllowedStudent`.
 */
class ClearanceAllowedStudentSearch extends ClearanceAllowedStudent
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['cas_full_name', 'cas_regId', 'cas_acadYear', 'cas_reason', 'cas_semester', 'cas_admission', 'cas_program', 'cas_department', 'cas_clslvl', 'cas_spec', 'created_at', 'created_by', 'updated_by', 'updated_at'], 'safe'],
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
        $query = ClearanceAllowedStudent::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        $query->orderBy([
            'id' => SORT_DESC
        ]);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cas_reason' => $this->cas_reason,
        ]);

        $query->andFilterWhere(['like', 'cas_full_name', $this->cas_full_name])
            ->andFilterWhere(['like', 'cas_regId', $this->cas_regId])
            ->andFilterWhere(['like', 'cas_acadYear', $this->cas_acadYear])
            ->andFilterWhere(['like', 'cas_semester', $this->cas_semester])
            ->andFilterWhere(['like', 'cas_admission', $this->cas_admission])
            ->andFilterWhere(['like', 'cas_program', $this->cas_program])
            ->andFilterWhere(['like', 'cas_department', $this->cas_department])
            ->andFilterWhere(['like', 'cas_clslvl', $this->cas_clslvl])
            ->andFilterWhere(['like', 'cas_spec', $this->cas_spec]);

        return $dataProvider;
    }
}
