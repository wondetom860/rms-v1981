<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\GraduateStudentPointHistory;

/**
 * GraduateStudentPointHistorySearch represents the model behind the search form of `backend\models\GraduateStudentPointHistory`.
 */
class GraduateStudentPointHistorySearch extends GraduateStudentPointHistory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'gph_gstd_relId', 'acadYear', 'semester', 'gph_registration_entry_relId', 'gph_credit_hour_enrolled', 'created_by', 'updated_by', 'approved_by'], 'integer'],
            [['gph_SGPA', 'gph_CGPA', 'gph_average_point'], 'number'],
            [['created_at', 'updated_at', 'approved_at', 'data'], 'safe'],
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
        $query = GraduateStudentPointHistory::find()
            ->innerJoin('graduate_student','graduate_student.id = graduate_student_point_history.gph_gstd_relId')
            ->innerJoin('graduate_student_registration_entry_history','graduate_student_registration_entry_history.id = graduate_student_point_history.gph_registration_entry_relId')
            ->innerJoin('user','user.id = graduate_student.gstd_user_relId');

        // add conditions that should always apply here
            $query->orderBy([
                'user.first_name' => SORT_ASC,
                'graduate_student_registration_entry_history.gphr_acadYear' => SORT_ASC,
            ]);
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

            'gph_gstd_relId' => $this->gph_gstd_relId,
            'gph_registration_entry_relId' => $this->gph_registration_entry_relId,
            'gph_SGPA' => $this->gph_SGPA,
            'gph_CGPA' => $this->gph_CGPA,
            'gph_credit_hour_enrolled' => $this->gph_credit_hour_enrolled,
            'gph_average_point' => $this->gph_average_point,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'approved_at' => $this->approved_at,
            'approved_by' => $this->approved_by,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
