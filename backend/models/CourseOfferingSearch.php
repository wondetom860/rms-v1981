<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CourseOffering;

/**
 * CourseOfferingSearch represents the model behind the search form about `backend\models\CourseOffering`.
 */
class CourseOfferingSearch extends CourseOffering
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'coff_cocat_relId', 'coff_course_relId', 'coff_acadYear_relId', 'coff_group_relId', 'coff_instr_relId', 'coff_TA_relId', 'coff_updated_by', 'coff_created_by'], 'integer'],
            [['coff_updated_at', 'coff_created_at'], 'safe'],
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
        $query = CourseOffering::find();

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
            'coff_cocat_relId' => $this->coff_cocat_relId,
            'coff_course_relId' => $this->coff_course_relId,
            'coff_acadYear_relId' => $this->coff_acadYear_relId,
            'coff_group_relId' => $this->coff_group_relId,
            'coff_instr_relId' => $this->coff_instr_relId,
            'coff_TA_relId' => $this->coff_TA_relId,
            'coff_updated_by' => $this->coff_updated_by,
            'coff_updated_at' => $this->coff_updated_at,
            'coff_created_by' => $this->coff_created_by,
            'coff_created_at' => $this->coff_created_at,
        ]);

        return $dataProvider;
    }
}
