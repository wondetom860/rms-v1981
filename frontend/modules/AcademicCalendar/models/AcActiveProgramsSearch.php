<?php

namespace frontend\modules\AcademicCalendar\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\AcademicCalendar\models\AcActivePrograms;

/**
 * AcActiveProgramsSearch represents the model behind the search form of `frontend\modules\AcademicCalendar\models\AcActivePrograms`.
 */
class AcActiveProgramsSearch extends AcActivePrograms
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'academic_year_relId', 'admission_relId', 'program_relId', 'dept_relId', 'class_level_relId', 'semester_relId', 'spec_relId', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by','graduate_class','on_internship'], 'integer'],
            [['data', 'remark'], 'safe'],
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
        $query = AcActivePrograms::find()
            ->innerJoin('ac_academic_year','ac_academic_year.id=ac_active_programs.academic_year_relId');

        // add conditions that should always apply here
        if (\Yii::$app->user->can('DepartmentHead')) {
            $instAcc = \backend\models\Instructor::findOne(['inst_user_relId' => \Yii::$app->user->id]);

            if ($instAcc) {
                $query->andFilterWhere(['dept_relId' => $instAcc->inst_dep_relId]);
            }else{
                $query->andFilterWhere(['dept_relId' => 0]);
            }
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

        $query->orderBy([
            'school_year' => SORT_DESC,
            'semester' => SORT_DESC,
            'class_level_relId' => SORT_DESC,
        ]);

        // grid filtering conditions
        $query->andFilterWhere([
            'academic_year_relId' => $this->academic_year_relId,
            'graduate_class' => $this->graduate_class,
            'on_internship' => $this->on_internship,
            'admission_relId' => $this->admission_relId,
            'program_relId' => $this->program_relId,
            'dept_relId' => $this->dept_relId,
            'class_level_relId' => $this->class_level_relId,
            'semester_relId' => $this->semester_relId,
            'spec_relId' => $this->spec_relId,
            'ac_active_programs.status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        return $dataProvider;
    }
}
