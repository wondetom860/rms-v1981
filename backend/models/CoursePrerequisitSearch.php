<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CoursePrerequisit;

/**
 * CoursePrerequisitSearch represents the model behind the search form about `backend\models\CoursePrerequisit`.
 */
class CoursePrerequisitSearch extends CoursePrerequisit
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'preq_adm_relId', 'preq_prog_relId', 'preq_dept_relId', 'preq_spec_relId', 'preq_main_course_relId', 'preq_preq_course_relId', 'preq_created_by', 'preq_updated_by'], 'integer'],
            [['preq_created_at', 'preq_updated_at','preqProgRel.program_name', 'preqAdmRel.admission_name', 'preqDeptRel.dept_name', 'preqSpecRel.spec_name', 'preqMainCourseRel.course_code', 'preqMainCourseRel.course_title','preqPreqCourseRel.course_title'], 'safe'],
        ];
    }

    public function attributes(){
        return array_merge(parent::attributes(),['preqProgRel.program_name', 'preqAdmRel.admission_name', 'preqDeptRel.dept_name', 'preqSpecRel.spec_name', 'preqMainCourseRel.course_title','preqMainCourseRel.course_code','preqPreqCourseRel.course_title']);
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
        $query = CoursePrerequisit::find();

        // add conditions that should always apply here

        $query = $query->joinWith(['preqMainCourseRel']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (Yii::$app->user->can('DepartmentHead') || Yii::$app->user->can('Instructor')) {
            $instructor = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);

            if ($instructor) {
                $query->groupBy('preq_main_course_relId');
            }else{
                $query = null;
            }
        }else{
            $query->andFilterWhere(['OR',
                ['preq_dept_relId' => NULL],
                ['preq_dept_relId'=> $this->preq_dept_relId]]);
        }


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'preq_adm_relId' => $this->preq_adm_relId,
            'preq_prog_relId' => $this->preq_prog_relId,
            // 'preq_dept_relId' => $this->preq_dept_relId,
            // 'preq_spec_relId' => $this->preq_spec_relId,
            'preq_main_course_relId' => $this->preq_main_course_relId,
            'preq_preq_course_relId' => $this->preq_preq_course_relId,
            // 'preq_created_by' => $this->preq_created_by,
            // 'preq_created_at' => $this->preq_created_at,
            // 'preq_updated_by' => $this->preq_updated_by,
            // 'preq_updated_at' => $this->preq_updated_at,
        ]);

        // $query->andFilterWhere(['like', 'remark', $this->remark]);

        $query->andFilterWhere(['LIKE', 'course_code', $this->getAttribute('preqMainCourseRel.course_code')]);
        // $query->andFilterWhere(['LIKE', 'admission_name', $this->getAttribute('preqAdmRel.admission_name')]);
        $query->andFilterWhere(['LIKE', 'course_title', $this->getAttribute('preqMainCourseRel.course_title')]);
        $query->andFilterWhere(['LIKE', 'course_title', $this->getAttribute('preqPreqCourseRel.course_title')]);
        // $query->andFilterWhere(['LIKE', 'program_name', $this->getAttribute('preqProgRel.program_name')]);
        $query->andFilterWhere(['OR',
            ['preq_spec_relId' => NULL],['preq_spec_relId' => $this->preq_spec_relId]]);

        return $dataProvider;
    }
}
