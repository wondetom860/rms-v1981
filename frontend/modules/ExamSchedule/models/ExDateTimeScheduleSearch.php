<?php

namespace frontend\modules\ExamSchedule\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\ExamSchedule\models\ExDateTimeSchedule;

/**
 * ExDateTimeScheduleSearch represents the model behind the search form of `frontend\modules\ExamSchedule\models\ExDateTimeSchedule`.
 */
class ExDateTimeScheduleSearch extends ExDateTimeSchedule
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'date', 'start_time', 'end_time', 'created_by', 'exam_schedule_entry_relId', 'created_at', 'updated_at', 'updated_by'], 'integer'],
            [['standbys', 'supervisors', 'data', 'shift'], 'safe'],
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
        $query = ExDateTimeSchedule::find();

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
            'exam_schedule_entry_id' => $this->exam_schedule_entry_relId,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ]);

        $query->andFilterWhere(['like', 'standbys', $this->standbys])
            ->andFilterWhere(['like', 'supervisors', $this->supervisors])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
