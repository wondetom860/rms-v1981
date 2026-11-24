<?php

namespace frontend\modules\TRANSTUD\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\TRANSTUD\models\ExemptedCourse;

/**
 * ExemptedCourseSearch represents the model behind the search form of `frontend\modules\TRANSTUD\models\ExemptedCourse`.
 */
class ExemptedCourseSearch extends ExemptedCourse
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ts_relid', 'course_hours', 'approved', 'created_at', 'updated_at', 'deleted_at', 'deleted_by', 'created_by', 'updated_by'], 'integer'],
            [['course_code', 'course_name', 'grade_obtained', 'equivalent_course_ids', 'data'], 'safe'],
            [['grade_point', 'grade_point_sum'], 'number'],
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
        $query = ExemptedCourse::find();

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
            'ts_relid' => $this->ts_relid,
            'course_hours' => $this->course_hours,
            'grade_point' => $this->grade_point,
            'grade_point_sum' => $this->grade_point_sum,
            'approved' => $this->approved,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'deleted_by' => $this->deleted_by,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'course_code', $this->course_code])
            ->andFilterWhere(['like', 'course_name', $this->course_name])
            ->andFilterWhere(['like', 'grade_obtained', $this->grade_obtained])
            ->andFilterWhere(['like', 'equivalent_course_ids', $this->equivalent_course_ids])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
