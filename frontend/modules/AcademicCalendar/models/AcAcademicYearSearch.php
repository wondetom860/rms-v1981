<?php

namespace frontend\modules\AcademicCalendar\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\AcademicCalendar\models\AcAcademicYear;

/**
 * AcAcademicYearSearch represents the model behind the search form of `frontend\modules\AcademicCalendar\models\AcAcademicYear`.
 */
class AcAcademicYearSearch extends AcAcademicYear
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'college_relId', 'start_date', 'end_date', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['school_year', 'semester', 'season', 'remark', 'data'], 'safe'],
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
        $query = AcAcademicYear::find();

        // add conditions that should always apply here

        $query->orderBy(['end_date' => SORT_DESC]);
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
            'college_relId' => $this->college_relId,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        $query->andFilterWhere(['like', 'school_year', $this->school_year])
            ->andFilterWhere(['like', 'semester', $this->semester])
            ->andFilterWhere(['like', 'season', $this->season])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
