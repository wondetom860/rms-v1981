<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Course;

/**
 * CourseSearch represents the model behind the search form about `backend\models\Course`.
 */
class CourseSearch extends Course
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'course_crdthr', 'course_ETCS', 'course_dept_relId', 'course_type', 'course_entry_type', 'course_lecture_hour', 'course_lab_hour', 'course_tutorial_hour', 'course_created_by', 'course_updated_by'], 'integer'],
            [['course_code', 'course_title', 'course_created_at', 'course_updated_at'], 'safe'],
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
        $query = Course::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'course_type' => $this->course_type,
            'course_crdthr' => $this->course_crdthr,
            'course_dept_relId' => $this->course_dept_relId,
            'course_lecture_hour' => $this->course_lecture_hour,
            'course_lab_hour' => $this->course_lab_hour,
            'course_ETCS' => $this->course_ETCS,
            'course_tutorial_hour' => $this->course_tutorial_hour,
            'course_created_by' => $this->course_created_by,
            'course_entry_type' => $this->course_entry_type,
        ]);

        $query->andFilterWhere(['like', 'course_code', $this->course_code])
            ->andFilterWhere(['like', 'course_title', $this->course_title]);

        return $dataProvider;
    }
}
