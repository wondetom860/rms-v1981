<?php

namespace frontend\modules\Reassessment\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\Reassessment\models\ReassessmentCommittee;

/**
 * ReassessmentCommitteeSearch represents the model behind the search form of `frontend\modules\Reassessment\models\ReassessmentCommittee`.
 */
class ReassessmentCommitteeSearch extends ReassessmentCommittee
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ras_relId', 'inst_relId', 'approval_status', 'approved_at', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['data'], 'safe'],
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
        $query = ReassessmentCommittee::find();

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
            'ras_relId' => $this->ras_relId,
            'inst_relId' => $this->inst_relId,
            'approval_status' => $this->approval_status,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
