<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\TouchedGrades;

/**
 * TouchedGradesSearch represents the model behind the search form of `backend\models\TouchedGrades`.
 */
class TouchedGradesSearch extends TouchedGrades
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tg_acadYear_relId', 'tg_sem_relId', 'tg_prog_relId', 'tg_clslvl_relId', 'tg_semreg_relId', 'tg_srd2_relId', 'tg_beforeGrade_relId', 'tg_afterGrade_relId', 'tg_created_by', 'tg_updated_by', 'tg_approved_by', 'tg_operation_completed'], 'integer'],
            [['tg_reason', 'tg_created_at', 'tg_updated_at', 'tg_approved_at','courseTitle','first_name'], 'safe'],
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
    public function search($params,$print=NULL)
    {
        $query = TouchedGrades::find()
            ->innerJoin('semester_registration_2_detail','semester_registration_2_detail.id='.$this::tableName().'.tg_srd2_relId')
            ->innerJoin('course','course.id=semester_registration_2_detail.srd2_course_relId')
            ->innerJoin('semester_registration_2','semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
            ->innerJoin('student','student.id=semester_registration_2.semreg_std_relId')
            ->innerJoin('user','user.id=student.std_user_relId')
            ;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (\Yii::$app->user->can('DepartmentHead')) {
            $instructor = Instructor::findOne(['inst_user_relId' => \Yii::$app->user->identity->id]);
            $query->andFilterWhere(['OR',
                ['student.std_dept_relId' => $instructor->inst_dep_relId],
                ['course.course_dept_relId' => $instructor->inst_dep_relId]]);
        }
        $query->orderBy([
            'tg_acadYear_relId' => SORT_DESC,
            'tg_sem_relId' => SORT_DESC,
            'course_title' => SORT_ASC,
            'user.first_name' => SORT_ASC,
            'user.middle_name' => SORT_ASC,
        ]);

        if ($print) {
            $params = $_SESSION['printParams'];
        }else{
            $_SESSION['printParams']  = $params;
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'tg_acadYear_relId' => $this->tg_acadYear_relId,
            'tg_sem_relId' => $this->tg_sem_relId,
            'tg_prog_relId' => $this->tg_prog_relId,
            'tg_clslvl_relId' => $this->tg_clslvl_relId,
            'tg_semreg_relId' => $this->tg_semreg_relId,
            'tg_srd2_relId' => $this->tg_srd2_relId,
            'tg_beforeGrade_relId' => $this->tg_beforeGrade_relId,
            'tg_afterGrade_relId' => $this->tg_afterGrade_relId,
            'tg_created_at' => $this->tg_created_at,
            'tg_created_by' => $this->tg_created_by,
            'tg_updated_at' => $this->tg_updated_at,
            'tg_updated_by' => $this->tg_updated_by,
            'tg_approved_by' => $this->tg_approved_by,
            'tg_approved_at' => $this->tg_approved_at,
            'tg_operation_completed' => $this->tg_operation_completed,
        ]);

        $query->andFilterWhere(['like', 'tg_reason', $this->tg_reason]);
        $query->andFilterWhere(['like', 'course.course_title', $this->courseTitle]);
        $query->andFilterWhere(['OR', 
            ['LIKE','user.first_name',$this->first_name],
            ['LIKE','user.middle_name',$this->first_name],
            ['LIKE','user.last_name',$this->first_name],
        ]);

        return $print ? $query->all() : $dataProvider;
    }
}
