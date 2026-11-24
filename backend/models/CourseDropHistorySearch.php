<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CourseDropHistory;

/**
 * CourseDropHistorySearch represents the model behind the search form about `backend\models\CourseDropHistory`.
 */
class CourseDropHistorySearch extends CourseDropHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cdh_srd2_relId', 'cdh_std_relId', 'cdh_course_relId', 'cdh_acadYear_relId', 'cdh_sem_relId', 'cdh_group_relId', 'cdh_clslvl_relId', 'cdh_approved_by', 'cdh_status', 'cdh_updated_by'], 'integer'],
            [['cdh_created_at', 'cdh_updated_at','stdId','courseTitle','first_name'], 'safe'],
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
        $query = CourseDropHistory::find()
            ->innerJoin('course','course.id=course_drop_history.cdh_course_relId')
            ->innerJoin('student','student.id=course_drop_history.cdh_std_relId')
            ->innerJoin('user','user.id=student.std_user_relId');

        // add conditions that should always apply here
        if (\Yii::$app->user->can('Instructor')) {
            $inst = Instructor::findOne(['inst_user_relId' => \Yii::$app->user->identity->id]);
            $semesterAdvisorModel = new SemesterAdvisor();
            $ids = $semesterAdvisorModel->getAdvisingGsetIds($inst->id);
            if (empty($ids)) {
                $ids = [0];
            }
            $query->andFilterWhere(['student.std_group_realId' => $ids]);
        }

        else if (\Yii::$app->user->can('Student')) {
            $query->andFilterWhere(['student.std_user_relId' => \Yii::$app->user->identity->id]);
        } 

        else if (\Yii::$app->user->can('DepartmentHead')) {
            // code...
            $semesterAdvisorModel = new SemesterAdvisor();
            $inst = Instructor::findOne(['inst_user_relId' => \Yii::$app->user->identity->id]);
            $query->andFilterWhere(['OR',
                    ['course.course_dept_relId' => $inst->inst_dep_relId],
                    ['cdh_group_relId' => $semesterAdvisorModel->getAdvisingGsetIds($inst->id)],
                ]);
        }

        $query->orderBy([
            'cdh_acadYear_relId' => SORT_DESC,
            'cdh_sem_relId' => SORT_DESC,
            'user.first_name' => SORT_ASC,
            'user.middle_name' => SORT_ASC,
        ]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cdh_srd2_relId' => $this->cdh_srd2_relId,
            'cdh_std_relId' => $this->cdh_std_relId,
            'cdh_course_relId' => $this->cdh_course_relId,
            'cdh_acadYear_relId' => $this->cdh_acadYear_relId,
            'cdh_sem_relId' => $this->cdh_sem_relId,
            'cdh_group_relId' => $this->cdh_group_relId,
            'cdh_clslvl_relId' => $this->cdh_clslvl_relId,
            'cdh_approved_by' => $this->cdh_approved_by,
            'cdh_status' => $this->cdh_status,
        ]);

        $query->andFilterWhere(['LIKE',
            'course.course_title',$this->courseTitle
        ]);

        $query->andFilterWhere(['OR',
            ['user.first_name'=>$this->first_name],
            ['user.middle_name'=>$this->first_name],
            ['user.last_name'=>$this->first_name],
        ]);

        return $dataProvider;
    }
}
