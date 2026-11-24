<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Student1;

/**
 * Student1Search represents the model behind the search form about `backend\models\Student1`.
 */
class Student1Search extends Student1
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'std_user_relId', 'std_dept_relId', 'std_spec_relId', 'std_clslvl_relId', 'std_prog_relId', 'std_schlrshpType_relId', 'std_admission_relId', 'std_acadYear_relId', 'std_group_realId', 'std_emergency_contact_relId', 'st_educ_info_relId', 'std_created_by', 'std_updated_by'], 'integer'],
            [['std_regId', 'std_type', 'std_semester_status', 'std_marital_status', 'std_created_at', 'std_updated_at'], 'safe'],
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
        $query = Student1::find();

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
            'std_user_relId' => $this->std_user_relId,
            'std_dept_relId' => $this->std_dept_relId,
            'std_spec_relId' => $this->std_spec_relId,
            'std_clslvl_relId' => $this->std_clslvl_relId,
            'std_prog_relId' => $this->std_prog_relId,
            'std_schlrshpType_relId' => $this->std_schlrshpType_relId,
            'std_admission_relId' => $this->std_admission_relId,
            'std_acadYear_relId' => $this->std_acadYear_relId,
            'std_group_realId' => $this->std_group_realId,
            'std_emergency_contact_relId' => $this->std_emergency_contact_relId,
            'st_educ_info_relId' => $this->st_educ_info_relId,
            'std_created_at' => $this->std_created_at,
            'std_created_by' => $this->std_created_by,
            'std_updated_at' => $this->std_updated_at,
            'std_updated_by' => $this->std_updated_by,
        ]);

        $query->andFilterWhere(['like', 'std_regId', $this->std_regId])
            ->andFilterWhere(['like', 'std_type', $this->std_type])
            ->andFilterWhere(['like', 'std_semester_status', $this->std_semester_status])
            ->andFilterWhere(['like', 'std_marital_status', $this->std_marital_status]);

        return $dataProvider;
    }
}
