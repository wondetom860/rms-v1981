<?php

namespace frontend\modules\GC\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\GC\models\GcStudent;

/**
 * GcStudentSearch represents the model behind the search form of `frontend\modules\GC\models\GcStudent`.
 */
class GcStudentSearch extends GcStudent
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['std_relId', 'id', 'entry_relId', 'graduation_mode', 'total_credit_hr', 'course_count', 'last_semreg_relId', 'reg_approved', 'reg_approved_at', 'reg_approved_by', 'created_at', 'updated_at', 'deleted_at', 'deleted_by', 'created_by', 'updated_by'], 'integer'],
            [['total_gps', 'cgpa', 'sgpa', 'major_gpa'], 'number'],
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
        $query = GcStudent::find();

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
            'entry_relId' => $this->entry_relId,
            'graduation_mode' => $this->graduation_mode,
            'total_credit_hr' => $this->total_credit_hr,
            'course_count' => $this->course_count,
            'total_gps' => $this->total_gps,
            'cgpa' => $this->cgpa,
            'sgpa' => $this->sgpa,
            'major_gpa' => $this->major_gpa,
            'last_semreg_relId' => $this->last_semreg_relId,
            'reg_approved' => $this->reg_approved,
            'reg_approved_at' => $this->reg_approved_at,
            'reg_approved_by' => $this->reg_approved_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'deleted_by' => $this->deleted_by,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
