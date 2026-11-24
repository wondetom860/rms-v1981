<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PostSecondaryEducation;

/**
 * PostSecondaryEducationSeasrch represents the model behind the search form about `backend\models\PostSecondaryEducation`.
 */
class PostSecondaryEducationSearch extends PostSecondaryEducation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pse_ei_relId', 'pse_nationality_relId', 'pse_to_year', 'pse_created_by', 'pse_updated_by'], 'integer'],
            [['pse_institute_name', 'pse_from_year', 'pse_certificate_earned', 'pse_created_at', 'pse_updated_at'], 'safe'],
            [['pse_credit_earned', 'pse_CGPA_obtained'], 'number'],
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
        $query = PostSecondaryEducation::find();

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
            'pse_ei_relId' => $this->pse_ei_relId,
            'pse_nationality_relId' => $this->pse_nationality_relId,
            'pse_from_year' => $this->pse_from_year,
            'pse_to_year' => $this->pse_to_year,
            'pse_credit_earned' => $this->pse_credit_earned,
            'pse_CGPA_obtained' => $this->pse_CGPA_obtained,
            'pse_created_by' => $this->pse_created_by,
            'pse_updated_by' => $this->pse_updated_by,
            'pse_created_at' => $this->pse_created_at,
            'pse_updated_at' => $this->pse_updated_at,
        ]);

        $query->andFilterWhere(['like', 'pse_institute_name', $this->pse_institute_name])
            ->andFilterWhere(['like', 'pse_certificate_earned', $this->pse_certificate_earned]);

        return $dataProvider;
    }
}
