<?php

namespace frontend\modules\CourseAssessment\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\CourseAssessment\models\ContinuousAssessment;

/**
 * ContinuousAssessmentSearch represents the model behind the search form of `frontend\modules\CourseAssessment\models\ContinuousAssessment`.
 */
class ContinuousAssessmentSearch extends ContinuousAssessment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ca_std_relid', 'ca_semreg_relId', 'ca_markAssignment_relId', 'ca_catagorySetup_relId', 'ca_coff_relId', 'ca_created_by', 'ca_updated_by'], 'integer'],
            [['ca_markObtained'], 'number'],
            [['ca_created_at', 'ca_updated_at'], 'safe'],
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
        $query = ContinuousAssessment::find();

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
            'ca_std_relid' => $this->ca_std_relid,
            'ca_semreg_relId' => $this->ca_semreg_relId,
            'ca_markAssignment_relId' => $this->ca_markAssignment_relId,
            'ca_catagorySetup_relId' => $this->ca_catagorySetup_relId,
            'ca_markObtained' => $this->ca_markObtained,
            'ca_coff_relId' => $this->ca_coff_relId,
            'ca_created_by' => $this->ca_created_by,
            'ca_created_at' => $this->ca_created_at,
            'ca_updated_by' => $this->ca_updated_by,
            'ca_updated_at' => $this->ca_updated_at,
        ]);

        return $dataProvider;
    }
}
