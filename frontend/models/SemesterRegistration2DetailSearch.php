<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SemesterRegistration2Detail;

/**
 * SemesterRegistration2DetailSearch represents the model behind the search form about `frontend\models\SemesterRegistration2Detail`.
 */
class SemesterRegistration2DetailSearch extends SemesterRegistration2Detail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'srd2_semreg_relId', 'srd2_coff_relId', 'srd2_course_relId', 'srd2_grade_relId', 'srd2_coadd_flag', 'srd2_corptd_flag', 'srd2_codrpd_relId', 'srd2_corProject', 'srd2_enrollApproved', 'srd2_enrollApproved_by', 'srd2_group_relId', 'srd2_updated_by', 'srd2_created_by'], 'integer'],
            [['srd2_enrolApprovrd_at', 'srd2_updated_at', 'srd2_created_at'], 'safe'],
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
        $query = SemesterRegistration2Detail::find();

        // add conditions that should always apply here

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
            'id' => $this->id,
            'srd2_semreg_relId' => $this->srd2_semreg_relId,
            'srd2_coff_relId' => $this->srd2_coff_relId,
            'srd2_course_relId' => $this->srd2_course_relId,
            'srd2_grade_relId' => $this->srd2_grade_relId,
            'srd2_coadd_flag' => $this->srd2_coadd_flag,
            'srd2_corptd_flag' => $this->srd2_corptd_flag,
            'srd2_codrpd_relId' => $this->srd2_codrpd_relId,
            'srd2_corProject' => $this->srd2_corProject,
            'srd2_enrollApproved' => $this->srd2_enrollApproved,
            'srd2_enrollApproved_by' => $this->srd2_enrollApproved_by,
            'srd2_enrolApprovrd_at' => $this->srd2_enrolApprovrd_at,
            'srd2_group_relId' => $this->srd2_group_relId,
            'srd2_updated_by' => $this->srd2_updated_by,
            'srd2_updated_at' => $this->srd2_updated_at,
            'srd2_created_at' => $this->srd2_created_at,
            'srd2_created_by' => $this->srd2_created_by,
        ]);

        return $dataProvider;
    }
}
