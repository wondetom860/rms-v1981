<?php

namespace frontend\modules\ExamSchedule\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\ExamSchedule\models\ExExaminees;

/**
 * ExExamineesSearch represents the model behind the search form of `frontend\modules\ExamSchedule\models\ExExaminees`.
 */
class ExExamineesSearch extends ExExaminees
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'exam_slot_relId', 'std_relId', 'section_relId', 'offering_detail_relId', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['class_attendance_percent'], 'number'],
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
        $query = ExExaminees::find();

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
            'exam_slot_relId' => $this->exam_slot_relId,
            'std_relId' => $this->std_relId,
            'section_relId' => $this->section_relId,
            'offering_detail_relId' => $this->offering_detail_relId,
            'class_attendance_percent' => $this->class_attendance_percent,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
