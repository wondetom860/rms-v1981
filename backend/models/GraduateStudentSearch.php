<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\GraduateStudent;

/**
 * GraduateStudentSearch represents the model behind the search form about `backend\models\GraduateStudent`.
 */
class GraduateStudentSearch extends GraduateStudent
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'gstd_user_relId', 'gstd_adm_relId', 'gstd_prog_relId', 'gstd_dept_relId', 'gstd_spec_relId', 'gstd_sponsorship_relId', 'gstd_doc_collected', 'gsdt_prepared_by', 'gstd_doc_signed_by', 'gstd_doc_signed'], 'integer'],
            [['gstd_adm_year', 'gstd_grad_year', 'gstd_date_of_graduation', 'gstd_doc_collected_at', 'gstd_doc_signed_at'], 'safe'],
            [['gstd_CGPA'], 'number'],
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
    public function search($params,$printMode = NULL)
    {
        $query = GraduateStudent::find()->innerJoin('user','user.id = graduate_student.gstd_user_relId');

        // add conditions that should always apply here
        $query->orderBy('user.first_name');
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
            'graduate_student.id' => $this->id,
            'gstd_user_relId' => $this->gstd_user_relId,
            'gstd_adm_relId' => $this->gstd_adm_relId,
            'gstd_prog_relId' => $this->gstd_prog_relId,
            'gstd_dept_relId' => $this->gstd_dept_relId,
            'gstd_spec_relId' => $this->gstd_spec_relId,
            'gstd_sponsorship_relId' => $this->gstd_sponsorship_relId,
            'gstd_CGPA' => $this->gstd_CGPA,
            'gstd_date_of_graduation' => $this->gstd_date_of_graduation,
            'gstd_doc_collected' => $this->gstd_doc_collected,
            'gstd_doc_collected_at' => $this->gstd_doc_collected_at,
            'gsdt_prepared_by' => $this->gsdt_prepared_by,
            'gstd_doc_signed_by' => $this->gstd_doc_signed_by,
            'gstd_doc_signed_at' => $this->gstd_doc_signed_at,
            'gstd_doc_signed' => $this->gstd_doc_signed,
        ]);

        $query->andFilterWhere(['like', 'gstd_adm_year', $this->gstd_adm_year])
            ->andFilterWhere(['like', 'gstd_grad_year', $this->gstd_grad_year]);

        return $printMode ? $query->all() : $dataProvider;
    }
}
