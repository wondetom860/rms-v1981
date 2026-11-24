<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ReassessmentHistory;
 
/**
 * ReassessmentHistorySearch represents the model behind the search form about `backend\models\ReassessmentHistory`.
 */
class ReassessmentHistorySearch extends ReassessmentHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ras_std_relId', 'ras_course_relId', 'ras_acadYear_relId', 'ras_sem_relId', 'ras_group_relId', 'ras_clslvl_relId', 'ras_bef_grade_relId', 'ras_updated_by', 'ras_aft_grade_relId'], 'integer'],
            [['ras_app_date', 'ras_created_at', 'ras_updated_at','first_name','course_title'], 'safe'],
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
        $query = ReassessmentHistory::find()
            ->innerJoin('course','course.id=reassessment_history.ras_course_relId')
            ->innerJoin('student','student.id=reassessment_history.ras_std_relId')
            ->innerJoin('user','user.id=student.std_user_relId')
            ;

        if (\Yii::$app->user->can('DepartmentHead')) {
            $instructor = Instructor::findOne(['inst_user_relId' => \Yii::$app->user->identity->id]);

            $query->andFilterWhere(['course.course_dept_relId' => $instructor->inst_dep_relId]);
        }else if (\Yii::$app->user->can('Student')) {
            $std = Student::findOne(['std_user_relId' => \Yii::$app->user->identity->id]);
            $query->andFilterWhere(['ras_std_relId' => $std->id]);
        }
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->orderBy(['ras_acadYear_relId' => SORT_DESC,'ras_sem_relId' => SORT_DESC]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ras_std_relId' => $this->ras_std_relId,
            'ras_course_relId' => $this->ras_course_relId,
            'ras_acadYear_relId' => $this->ras_acadYear_relId,
            'ras_sem_relId' => $this->ras_sem_relId,
            'ras_group_relId' => $this->ras_group_relId,
            'ras_clslvl_relId' => $this->ras_clslvl_relId,
            'ras_bef_grade_relId' => $this->ras_bef_grade_relId,
            'ras_app_date' => $this->ras_app_date,
            'ras_created_at' => $this->ras_created_at,
            'ras_updated_at' => $this->ras_updated_at,
            'ras_updated_by' => $this->ras_updated_by,
            'ras_aft_grade_relId' => $this->ras_aft_grade_relId,
        ]);

        $query->andFilterWhere(['LIKE','course.course_title',$this->course_title]);

        $query->andFilterWhere(['OR',
            ['LIKE','user.first_name',$this->first_name],
            ['LIKE','user.middle_name',$this->first_name],
            ['LIKE','user.last_name',$this->first_name],
        ]);

        return $dataProvider;
    }
}
