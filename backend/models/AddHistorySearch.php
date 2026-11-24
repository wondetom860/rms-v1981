<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AddHistory;

/**
 * AddHistorySearch represents the model behind the search form of `backend\models\AddHistory`.
 */
class AddHistorySearch extends AddHistory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ah_srd2_relId', 'ah_std_relId', 'ah_course_relId', 'ah_acadYear_relId', 'ah_sem_relId', 'ah_addWithGroup_relId', 'ah_coff_relId', 'ah_approved', 'ah_approved_by', 'ah_approved_at', 'ah_created_by', 'ah_updated_by'], 'integer'],
            [['ah_created_at', 'ah_updated_at','stdId','first_name','courseTitle'], 'safe'],
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
        $query = AddHistory::find()
        ->innerJoin('course','course.id='.self::tableName().'.ah_course_relId')
        ->innerJoin('student','student.id='.self::tableName().'.ah_std_relId')
        ->innerJoin('user','user.id=student.std_user_relId')
        ->innerJoin('course_offering2_detail','course_offering2_detail.id='.self::tableName().'.ah_coff_relId')
        ;

        // add conditions that should always apply here

        if (\Yii::$app->user->can('Instructor')) {
            $instructor = Instructor::findOne(['inst_user_relId' => \Yii::$app->user->identity->id]);
            // Laod courses offered for the instructor yet
            $query->andFilterWhere(['course_offering2_detail.coff2det_inst_relId' => $instructor->id]);
        }else if (\Yii::$app->user->can('DepartmentHead')) {
            $instructor = Instructor::findOne(['inst_user_relId' => \Yii::$app->user->identity->id]);
            // Load all his dept courses 
            $query->andFilterWhere(['course.course_dept_relId' => $instructor->inst_dep_relId]);
        }else if (\Yii::$app->user->can('Student')) {
            // Laod all requests made by the student.
            $query->andFilterWhere(['student.std_user_relId' => \Yii::$app->user->identity->id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // order the history by the academic year, semester.
        $query->orderBy(['ah_acadYear_relId' => SORT_DESC,'ah_sem_relId' => SORT_DESC]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (isset($this->ah_coff_relId) && !empty($this->ah_coff_relId)) {
            $query->andFilterWhere(['course_offering2_detail.coff2det_inst_relId' => $this->ahCoffRel->coff2det_inst_relId,]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            
            'ah_srd2_relId' => $this->ah_srd2_relId,
            'ah_std_relId' => $this->ah_std_relId,
            'ah_course_relId' => $this->ah_course_relId,
            'ah_acadYear_relId' => $this->ah_acadYear_relId,
            'ah_sem_relId' => $this->ah_sem_relId,
            'ah_addWithGroup_relId' => $this->ah_addWithGroup_relId,
            'ah_approved' => $this->ah_approved,
            'ah_approved_by' => $this->ah_approved_by,
        ]);

        $query->andFilterWhere(['student.std_regId'=>$this->stdId]);
        $query->andFilterWhere(['LIKE','course.course_title',$this->courseTitle]);

        $query->andFilterWhere(['OR',
            ['LIKE','user.first_name', $this->first_name],
            ['LIKE','user.middle_name', $this->first_name],
            ['LIKE','user.last_name', $this->first_name]
        ]);

        return $dataProvider;
    }
}
