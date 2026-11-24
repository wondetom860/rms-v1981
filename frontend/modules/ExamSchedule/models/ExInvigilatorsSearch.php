<?php

namespace frontend\modules\ExamSchedule\models;

use frontend\modules\ExamSchedule\models\ExInvigilators;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ExInvigilatorsSearch represents the model behind the search form of `frontend\modules\ExamSchedule\models\ExInvigilators`.
 */
class ExInvigilatorsSearch extends ExInvigilators
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'exam_room_slot_relId', 'section_relId', 'staff_relId', 'offering_detail_relId', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['data'], 'safe'],
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
        $query = ExInvigilators::find();

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
            'staff_relId' => $this->staff_relId,
            'exam_room_slot_relId' => $this->exam_room_slot_relId,
            'section_relId' => $this->section_relId,
            'offering_detail_relId' => $this->offering_detail_relId,
        ]);

        return $dataProvider;
    }
}
