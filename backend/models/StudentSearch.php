<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Student;
 
/**
 * StudentSearch represents the model behind the search form about `backend\models\Student`.
 */
class StudentSearch extends Student
{

    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['stdUserRel.first_name', 'stdDeptRel.dept_name', 'stdSpecRel.spec_name', 'stdClslvlRel.clslvl_name', 'stdSchlrshpTypeRel.schType_name', 'stdAdmissionRel.admission_name', 'stdProgRel.program_name', 'stdAcadYearRel.sch_year_desc']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'std_user_relId', 'std_dept_relId', 'std_spec_relId', 'std_clslvl_relId', 'std_prog_relId', 'std_admission_relId', 'std_acadYear_relId', 'std_group_realId', 'std_created_by', 'std_updated_by', 'std_schlrshpType_relId', 'std_sem_relId'], 'integer'],

            [['std_regId', 'stdUserRel.first_name', 'std_type', 'std_semester_status', 'std_created_at', 'std_updated_at', 'stdDeptRel.dept_name', 'stdSpecRel.spec_name', 'stdClslvlRel.clslvl_name', 'stdSchlrshpTypeRel.schType_name', 'stdAdmissionRel.admission_name', 'stdProgRel.program_name', 'stdAcadYearRel.sch_year_desc'], 'safe'],
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

    public function searchW($admId, $progId, $deptId, $clslvlId, $specId)
    {
        return $this->find()
            ->innerJoin('user', 'user.id=student.std_user_relId')
            ->where([
                'std_admission_relId' => $admId,
                'std_prog_relId' => $progId,
                'std_dept_relId' => $deptId,
                'std_clslvl_relId' => $clslvlId,
                'std_spec_relId' => $specId,
            ])
            ->orderBy([
                'first_name' => SORT_ASC,
                'middle_name' => SORT_ASC,
                'last_name' => SORT_ASC
            ])
            ->all();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $print = null)
    {
        $query = Student::find();

        // add conditions that should always apply here

        $query = $query->joinWith(['stdUserRel', 'stdDeptRel', 'stdProgRel', 'stdAdmissionRel', 'stdClslvlRel', 'stdSpecRel', 'stdAcadYearRel', 'stdSchlrshpTypeRel',]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 16,
            ],
            // 'sort' => [
            //     'defaultOrder' => [
            //         'stdUserRel.first_name' => SORT_ASC, 
            //     ]
            // ],
        ]);

        if ($print) {
            $params = $_SESSION['printParams'];

            // return $_SESSION['printParams'];
        } else {
            $_SESSION['printParams']  = $params;
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            // return $dataProvider;
            return $print ? $query : $dataProvider;
        }

        // grid filtering conditions
        // $query->andFilterWhere([
        //     'std_user_relId' => $this->std_user_relId,
        //     'std_dept_relId' => $this->std_dept_relId,
        //     'std_spec_relId' => $this->std_spec_relId,
        //     'std_clslvl_relId' => $this->std_clslvl_relId,
        //     'std_prog_relId' => $this->std_prog_relId,
        //     'std_admission_relId' => $this->std_admission_relId,
        //     'std_acadYear_relId' => $this->std_acadYear_relId,
        //     'std_group_realId' => $this->std_group_realId,
        //     'std_created_at' => $this->std_created_at,
        //     'std_created_by' => $this->std_created_by,
        //     'std_updated_at' => $this->std_updated_at,
        //     'std_updated_by' => $this->std_updated_by,
        //     // 'std_schlrshpType_relId' => $this->std_schlrshpType_relId,
        //     // 
        // ]);

        $query->andFilterWhere([
            'OR',
            ['LIKE', 'first_name', $this->getAttribute('stdUserRel.first_name')],
            ['LIKE', 'middle_name', $this->getAttribute('stdUserRel.first_name')]
        ]);

        $query->andFilterWhere(['LIKE', 'program_name', $this->getAttribute('stdProgRel.program_name')]);
        $query->andFilterWhere(['LIKE', 'dept_name', $this->getAttribute('stdDeptRel.dept_name')]);
        $query->andFilterWhere(['LIKE', 'admission_name', $this->getAttribute('stdAdmissionRel.admission_name')]);
        // $query->andFilterWhere(['LIKE', 'clslvl_name', $this->getAttribute('stdClslvlRel.clslvl_name')]);
        $query->andFilterWhere(['sch_year_desc' => $this->getAttribute('stdAcadYearRel.sch_year_desc')]);
        $query->andFilterWhere(['LIKE', 'schType_name', $this->getAttribute('stdSchlrshpTypeRel.schType_name')]);

        $query->andFilterWhere(['like', 'std_regId', $this->std_regId]);

        $query->andFilterWhere(['clslvl_name' => $this->getAttribute('stdClslvlRel.clslvl_name')]);
        $query->andFilterWhere(['std_type' => $this->std_type])
            ->andFilterWhere(['std_sem_relId' => $this->std_sem_relId])
            ->andFilterWhere(['std_group_realId' => $this->std_group_realId])
            ->andFilterWhere(['std_spec_relId' => $this->std_spec_relId])
            ->andFilterWhere(['std_semester_status' => $this->std_semester_status]);

        return $print ? $query->all() : $dataProvider;
    }
}
