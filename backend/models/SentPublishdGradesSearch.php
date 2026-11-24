<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SentPublishdGrades;

/**
 * SentPublishdGradesSearch represents the model behind the search form of `backend\models\SentPublishdGrades`.
 */
class SentPublishdGradesSearch extends SentPublishdGrades
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'semreg_relId', 'date_published', 'reg_approved', 'mail_sent', 'date_sent', 'trail_count', 'acadYear', 'semester', 'class_level'], 'integer'],
            [['recipient_email', 'relation', 'grade_report_path', 'studentName', 'std_regId',], 'safe'],
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
        $query = SentPublishdGrades::find()
            ->innerJoin('semester_registration_2', 'sent_publishd_grades.semreg_relId = semester_registration_2.id')
            ->innerJoin('student', 'semester_registration_2.semreg_std_relId = student.id')
            ->innerJoin('user', 'student.std_user_relId = user.id')
            ->innerJoin('class_level', 'semester_registration_2.semreg_clslvl_relId = class_level.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->orderBy([
            'mail_sent' => SORT_ASC,
            'semester_registration_2.semreg_acadYear_relId' => SORT_DESC,
            'semester_registration_2.semreg_semester_relId' => SORT_DESC,
            'semester_registration_2.semreg_clslvl_relId' => SORT_DESC,
            'user.first_name' => SORT_ASC,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'semester_registration_2.semreg_acadYear_relId' => $this->acadYear,
            'semester_registration_2.semreg_semester_relId' => $this->semester,
            'semester_registration_2.semreg_clslvl_relId' => $this->class_level,
            'semreg_relId' => $this->semreg_relId,
            'date_published' => $this->date_published,
            'reg_approved' => $this->reg_approved,
            'mail_sent' => $this->mail_sent,
            'date_sent' => $this->date_sent,
            'trail_count' => $this->trail_count,
        ]);

        $query->andFilterWhere(['like', 'recipient_email', $this->recipient_email])
            ->andFilterWhere(['like', 'relation', $this->relation])
            ->andFilterWhere(['like', 'user.first_name', $this->studentName])
            ->andFilterWhere(['like', 'student.std_regId', $this->std_regId]);

        return $dataProvider;
    }
}
