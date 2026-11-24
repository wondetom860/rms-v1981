<?php

namespace frontend\modules\GC\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\GC\models\GcEntry;

/**
 * GcEntrySearch represents the model behind the search form of `frontend\modules\GC\models\GcEntry`.
 */
class GcEntrySearch extends GcEntry
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'admission_relId', 'program_relId', 'dept_relId', 'spec_relId', 'acad_year_relId', 'senate_approve_date', 'created_at', 'updated_at', 'deleted_at', 'deleted_by', 'created_by', 'updated_by'], 'integer'],
            [['header_title', 'data'], 'safe'],
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
        $query = GcEntry::find();

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
            'admission_relId' => $this->admission_relId,
            'program_relId' => $this->program_relId,
            'dept_relId' => $this->dept_relId,
            'spec_relId' => $this->spec_relId,
            'acad_year_relId' => $this->acad_year_relId,
            'senate_approve_date' => $this->senate_approve_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'deleted_by' => $this->deleted_by,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'header_title', $this->header_title])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
