<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ThesisTitle;
use Yii;

/**
 * ThesisTitleSearch represents the model behind the search form of `backend\models\ThesisTitle`.
 */
class ThesisTitleSearch extends ThesisTitle
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tt_srd2_relId', 'tt_std_relId', 'tt_approved', 'tt_approved_by', 'tt_hod_approved', 'tt_hod_approved_by', 'tt_graded'], 'integer'],
            [['tt_title', 'tt_approved_at', 'tt_hod_approved_at','std_column'], 'safe'],
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
        $query = ThesisTitle::find()
            ->innerJoin('student', 'student.id=tt_std_relId')
            ->innerJoin('semester_registration_2_detail', 'semester_registration_2_detail.id=tt_srd2_relId')
            ->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
            ->innerJoin('user', 'user.id=student.std_user_relId');

        // add conditions that should always apply here
        if (Yii::$app->user->can('Student')) {
            $student = Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
            $query->andFilterWhere(['tt_std_relId' => $student->id]);
        } else if (Yii::$app->user->can('DepartmentHead')) {
            $instructor = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
            $query->andFilterWhere(['student.std_dept_relId' => $instructor->inst_dep_relId]);
        } else if (Yii::$app->user->can('Instructor')) {
            // $query = NULL;
            $instructor = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
            $modelSemAdvObj = new SemesterAdvisor();
            $gsetIds = $modelSemAdvObj->getAdvisingGsetIds($instructor->id);
            $query->andFilterWhere(['student.std_group_realId' => $gsetIds]);
        } else if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin') || Yii::$app->user->can('clerk')) {;
        } else {
            $query->andFilterWhere(['id' => 0]);
        }

        $query->orderBy(['tt_srd2_relId' => SORT_DESC]);

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
            'semreg_acadYear_relId' => $this->tt_srd2_relId,
            'tt_approved' => $this->tt_approved,
            'tt_graded' => $this->tt_graded,
        ]);

        $query->andFilterWhere(['like', 'tt_title', $this->tt_title]);
        $query->andFilterWhere([
            'OR', 
            ['like', 'user.first_name', $this->std_column], 
            ['like', 'user.middle_name', $this->std_column],
            ['like', 'user.first_name', $this->std_column],
            ['like', 'student.std_regId', $this->std_column]
        ]);

        return $dataProvider;
    }
}
