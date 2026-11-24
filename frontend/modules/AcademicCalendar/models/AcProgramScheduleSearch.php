<?php

namespace frontend\modules\AcademicCalendar\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\AcademicCalendar\models\AcProgramSchedule;

/**
 * AcProgramScheduleSearch represents the model behind the search form of `frontend\modules\AcademicCalendar\models\AcProgramSchedule`.
 */
class AcProgramScheduleSearch extends AcProgramSchedule
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'active_program_relId', 'action_relId', 'start_date', 'end_date', 'last_extend', 'last_extend_status', 'created_at', 'created_by', 'updated_at', 'updated_by','status'], 'integer'],
            [['remark', 'data','academic_year','class_year'], 'safe'],
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
        $query = AcProgramSchedule::find()
            ->innerJoin('ac_active_programs','ac_active_programs.id=ac_program_schedule.active_program_relId');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->andWhere(' ac_active_programs.status <> 0 ');

        $query->orderBy(['end_date' => SORT_DESC,'ac_active_programs.class_level_relId' => SORT_ASC]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ac_active_programs.academic_year_relId' => $this->academic_year,
            'ac_active_programs.class_level_relId' => $this->class_year,
            'active_program_relId' => $this->active_program_relId,
            'action_relId' => $this->action_relId,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            self::tableName().'.status' => $this->status,
            'last_extend' => $this->last_extend,
            'last_extend_status' => $this->last_extend_status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
