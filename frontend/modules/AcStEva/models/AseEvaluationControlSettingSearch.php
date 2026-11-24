<?php

namespace frontend\modules\AcStEva\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\AcStEva\models\AseEvaluationControlSetting;

/**
 * AseEvaluationControlSettingSearch represents the model behind the search form of `frontend\modules\AcStEva\models\AseEvaluationControlSetting`.
 */
class AseEvaluationControlSettingSearch extends AseEvaluationControlSetting
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'dept_plan_relId', 'finalized', 'created_by', 'created_at', 'updated_at', 'updated_by', 'course_relId','academic_year'], 'integer'],
            [['evaluators', 'data', 'course', 'candidate_staff_relId',], 'safe'],
            [['total_percentage'], 'number'],
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
        $query = AseEvaluationControlSetting::find()
            ->innerJoin('ase_department_evaluation_schedule', 'ase_department_evaluation_schedule.id=ase_evaluation_control_setting.dept_plan_relId')
            ->rightJoin('course', 'course.id=ase_evaluation_control_setting.course_relId')
            ->innerJoin('user', 'user.id=ase_evaluation_control_setting.candidate_staff_relId')
            ->innerJoin('ase_semester_evaluation', 'ase_semester_evaluation.id=ase_department_evaluation_schedule.header_relId')
            ->select(['ase_evaluation_control_setting.*', 'course.course_code', 'user.first_name', 'user.last_name']);

        // add conditions that should always apply here

        if (\Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => \Yii::$app->user->id]);
            $query->andFilterWhere(['dept_relId' => $inst->inst_dep_relId]);
        }
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
            'dept_relId' => $this->dept_plan_relId,
            'ase_semester_evaluation.id' => $this->academic_year,
            'total_percentage' => $this->total_percentage,
            'finalized' => $this->finalized,
        ]);

        $query->andFilterWhere(['OR',
            ['like', 'course.course_code', $this->course],
            ['like', 'course.course_title', $this->course],
        ]);
        $query->andFilterWhere([
            'OR',
            ['like', 'user.first_name', $this->candidate_staff_relId],
            ['like', 'user.last_name', $this->candidate_staff_relId]
        ]);

        return $dataProvider;
    }
}
