<?php

namespace frontend\modules\ClassSchedule\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\ClassSchedule\models\CsAcademicWorkDay;

/**
 * CsAcademicWorkDaySearch represents the model behind the search form of `frontend\modules\ClassSchedule\models\CsAcademicWorkDay`.
 */
class CsAcademicWorkDaySearch extends CsAcademicWorkDay
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'duration_one_class', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['day_name', 'class_start_at', 'lunch_time', 'class_end_time', 'data'], 'safe'],
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
        $query = CsAcademicWorkDay::find();

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
            'class_start_at' => $this->class_start_at,
            'duration_one_class' => $this->duration_one_class,
            'class_end_time' => $this->class_end_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'day_name', $this->day_name])
            ->andFilterWhere(['like', 'lunch_time', $this->lunch_time])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
