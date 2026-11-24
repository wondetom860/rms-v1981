<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CourseOffering2Detail;

/**
 * CourseOffering2DetailSearch represents the model behind the search form about `backend\models\CourseOffering2Detail`.
 */
class CourseOffering2DetailSearch extends CourseOffering2Detail
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'coff2det_coff2_relId', 'coff2det_cocat_relId', 'coff2det_dept_relId', 'coff2det_clslvl_relId', 'coff2det_course_relId', 'coff2det_inst_relId', 'coff2det_TA_relId', 'coff2det_created_by', 'coff2det_updated_by'], 'integer'],
            [['coff2det_created_at', 'admission', 'acadYear', 'sem', 'program', 'department', 'clslvl', 'coff2det_updated_at', 'courseTitle', 'inst', 'ta'], 'safe'],
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
    public function search($params, $acadYearId = NULL, $semId = NULL, $print = NULL)
    {
        $query = CourseOffering2Detail::find()
            ->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
            ->innerJoin('course_catalog', 'course_catalog.id = course_offering2_detail.coff2det_cocat_relId')
            ->innerJoin('department', 'department.id = course_catalog.cocat_dept_relId')
            ->innerJoin('w_group_setup', 'w_group_setup.id = course_offering2.coff2_group_relId')
            ->innerJoin('course', 'course.id = course_offering2_detail.coff2det_course_relId');

        // add conditions that should always apply here
        if (Yii::$app->user->can('DepartmentHead')) {
            $instDept = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id])->inst_dep_relId;
            $query = $query->andFilterWhere([
                'OR',
                ['gs_dept_relId' => $instDept,],
                ['course_dept_relId' => $instDept,]
            ]);
        } else if (Yii::$app->user->can('Instructor')) {
            $inst = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
            $query = $query->rightJoin('instructor', 'instructor.id = course_offering2_detail.coff2det_inst_relId')
                ->innerJoin('user', 'user.id=instructor.inst_user_relId')->andFilterWhere(['coff2det_inst_relId' => $inst->id]);
        } else if (Yii::$app->user->can('PGCoordinator')) {
            $query->innerJoin('program', 'program.id=w_group_setup.gs_prog_relId')
                ->andFilterWhere(['program_name' => 'MASTERS']);
        } else {
            // ->where([
            //         'course_offering2.coff2_acadYear_relId' => $acadYearId,
            //         'course_offering2.coff2_sem_relId' => $semId,
            //     ])
            // $query = $query->groupBy('coff2det_course_relId');
        }

        if (!is_null($acadYearId)) {
            $query->andFilterWhere(['gs_acadYear_relId' => $acadYearId,]);
        }
        if (!is_null($semId)) {
            $query->andFilterWhere(['gs_sem_relId' => $semId,]);
        }

        if (!$print) {
            $query = $query->orderBy([
                'course_offering2.coff2_acadYear_relId' => SORT_DESC,
                'course_offering2.coff2_sem_relId' => SORT_DESC,
                'department.dept_name' => SORT_ASC,
                'w_group_setup.gs_group_relId' => SORT_ASC,
                'w_group_setup.gs_clslvl_relId' => SORT_ASC,
                'course.course_title' => SORT_ASC,
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if ($print) {
            $params = $_SESSION['printParams'];
            $query = $query->orderBy([
                'department.dept_name' => SORT_ASC,
                'w_group_setup.gs_clslvl_relId' => SORT_ASC,
                'gs_spec_relId' => SORT_ASC,
            ]);
            $query->groupBy('course.course_code,gs_dept_relId,gs_clslvl_relId,gs_spec_relId');

            // return $_SESSION['printParams'];
        } else {
            $_SESSION['printParams']  = $params;
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $print ? $query : $dataProvider;
        }

        // 'gs_sem_relId' => $semId,

        // grid filtering conditions
        $query->andFilterWhere([
            'course_offering2.coff2_acadYear_relId' => $this->acadYear,
            'course_offering2.coff2_sem_relId' => $this->sem,
            'program' => $this->program,
            'department.dept_name' => $this->department,
            'clslvl' => $this->clslvl,
            'coff2det_coff2_relId' => $this->coff2det_coff2_relId,
            'coff2det_cocat_relId' => $this->coff2det_cocat_relId,
            'course_catalog.cocat_dept_relId' => $this->coff2det_dept_relId,
            'coff2det_course_relId' => $this->coff2det_course_relId,
            'coff2det_inst_relId' => $this->coff2det_inst_relId,
            'coff2det_TA_relId' => $this->coff2det_TA_relId,
            'coff2det_created_by' => $this->coff2det_created_by,
            'coff2det_created_at' => $this->coff2det_created_at,
            'coff2det_updated_at' => $this->coff2det_updated_at,
            'coff2det_updated_by' => $this->coff2det_updated_by,
        ]);
        $query->andFilterWhere(['like', 'course.course_title', $this->courseTitle]);
        $query->andFilterWhere(['OR', ['like', 'user.first_name', $this->inst], ['like', 'user.middle_name', $this->inst]]);
        $query->andFilterWhere(['OR', ['like', 'user.first_name', $this->ta], ['like', 'user.middle_name', $this->ta]]);

        return $print ? $query->all() : $dataProvider;
    }
}
