<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CourseCatalog;

/**
 * CourseCatalogSearch represents the model behind the search form about `backend\models\CourseCatalog`.
 */
class CourseCatalogSearch extends CourseCatalog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cocat_admission_relId', 'cocat_program_relId', 'cocat_dept_relId', 'cocat_clslvl_relId', 'cocat_spec_relId', 'cocat_sem_relId', 'cocat_status', 'cocat_created_by', 'cocat_updated_by','cocatClslvlRel.clslvl_num','course_category_relId'], 'integer'],
            [['cocat_created_at', 'cocat_updated_at','cocatSemRel.semester_name','cocatAdmissionRel.admission_name','cocatProgramRel.program_name','cocatDeptRel.dept_name','cocatSpecRel.spec_name','cocatCourseRel.course_code','cocatCourseRel.course_title'], 'safe'],
        ];
    }

    public function attributes(){
        return array_merge(parent::attributes(),[
            'cocatSemRel.semester_name',
            'cocatCourseRel.course_title',
            'cocatCourseRel.course_code',
            'cocatAdmissionRel.admission_name',
            'cocatProgramRel.program_name',
            'cocatDeptRel.dept_name',
            'cocatSpecRel.spec_name',
            'cocatClslvlRel.clslvl_num',
        ]);
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
        $query = CourseCatalog::find();

        // add conditions that should always apply here

        if (Yii::$app->user->can('Student')) {
            $student = Student::findOne(['std_user_relId' => \Yii::$app->user->identity->id, 'std_semester_status' => 'ACTIVE']);
            $dept = 0;
            if ($student) {
                $dept = $student->stdGroupReal->gs_dept_relId;
            }
            $query->andFilterWhere(['cocat_dept_relId' => $dept]);
            
        }
        $query = $query->joinWith(['cocatCourseRel','cocatSemRel','cocatAdmissionRel','cocatProgramRel','cocatDeptRel','cocatSpecRel','cocatClslvlRel']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],
            'sort' => [
                'defaultOrder' => [
                    'cocat_dept_relId' => SORT_ASC, 
                    'cocat_clslvl_relId' => SORT_ASC,
                ]
            ],
        ]);
        $dataProvider->sort->attributes['cocatAdmRel.admission_name'] = [
            'asc' => ['admission_name' => SORT_ASC],
            'desc' => ['admission_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['cocatProgRel.program_name'] = [
            'asc' => ['program_name' => SORT_ASC],
            'desc' => ['program_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['cocatClslvlRel.clslvl_num'] = [
            'asc' => ['clslvl_num' => SORT_ASC],
            'desc' => ['clslvl_num' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['cocatDeptRel.dept_name'] = [
            'asc' => ['dept_name' => SORT_ASC],
            'desc' => ['dept_name' => SORT_DESC],
        ];
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cocat_admission_relId' => $this->cocat_admission_relId,
            'cocat_program_relId' => $this->cocat_program_relId,
            'cocat_dept_relId' => $this->cocat_dept_relId,
            'cocat_clslvl_relId' => $this->cocat_clslvl_relId,
            'cocat_spec_relId' => $this->cocat_spec_relId,
            'cocat_sem_relId' => $this->cocat_sem_relId,
            'cocat_created_by' => $this->cocat_created_by,
            'cocat_created_at' => $this->cocat_created_at,
            'cocat_updated_by' => $this->cocat_updated_by,
            'course_category_relId' => $this->course_category_relId,
        ]);

        $query->andFilterWhere(['LIKE', 'admission_name', $this->getAttribute('cocatAdmissionRel.admission_name')]);
        $query->andFilterWhere(['LIKE', 'course_title', $this->getAttribute('cocatCourseRel.course_title')]);
        $query->andFilterWhere(['LIKE', 'course_code', $this->getAttribute('cocatCourseRel.course_code')]);
        $query->andFilterWhere(['LIKE', 'program_name', $this->getAttribute('cocatProgramRel.program_name')]);
        $query->andFilterWhere(['LIKE', 'dept_name', $this->getAttribute('cocatDeptRel.dept_name')]);
        $query->andFilterWhere(['cocat_status'=> $this->getAttribute('cocat_status')]);
        $query->andFilterWhere(['semester_name'=>$this->getAttribute('cocatSemRel.semester_name')]);
        $query->orFilterWhere(['semester_name'=>NULL]);
        $query->andFilterWhere(['LIKE', 'spec_name', $this->getAttribute('cocatSpecRel.spec_name')]);
        $query->orFilterWhere(['cocat_spec_relId'=>NULL]);
        $query->andFilterWhere(['LIKE', 'clslvl_num', $this->getAttribute('cocatClslvlRel.clslvl_num')]);
        $query->orFilterWhere(['clslvl_num'=>NULL]);

        return $dataProvider;
    }
}
