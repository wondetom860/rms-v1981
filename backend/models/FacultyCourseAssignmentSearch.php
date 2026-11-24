<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\FacultyCourseAssignment;

/**
 * FacultyCourseAssignmentSearch represents the model behind the search form of `backend\models\FacultyCourseAssignment`.
 */
class FacultyCourseAssignmentSearch extends FacultyCourseAssignment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'inst_relId', 'created_at', 'created_by', 'updated_at', 'updated_by', 'academicYear',], 'integer'],
            [['assign_as','remark', 'course'], 'safe'],
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
        $query = FacultyCourseAssignment::find()
            ->innerJoin('course_offering2_detail','course_offering2_detail.id = faculty_course_assignment.coff2det_relId')
            ->innerJoin('course','course.id = course_offering2_detail.coff2det_course_relId')
            ->innerJoin('ac_active_programs','ac_active_programs.id = course_offering2_detail.coff2det_acProg_relId');

        // add conditions that should always apply here

        if (\Yii::$app->user->can('DepartmentHead')) {
            $inst = Instructor::findOne(['inst_user_relId' => \Yii::$app->user->id]);
            $query->andFilterWhere(['course.course_dept_relId' => $inst->inst_dep_relId]);
        }
        $query->groupBy('coff2det_relId');
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
            'coff2det_relId' => $this->coff2det_relId,
            'inst_relId' => $this->inst_relId,
            'ac_active_programs.academic_year_relId' => $this->academicYear,
        ]);

        $query->andFilterWhere(['like', 'assign_as', $this->assign_as]);
        $query->andFilterWhere(['like', 'course.course_code', $this->course]);

        return $dataProvider;
    }
}
