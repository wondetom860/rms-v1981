<?php

namespace frontend\modules\DepartmentSelection\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\DepartmentSelection\models\SelectionSettingEntry;

/**
 * SelectionSettingEntrySearch represents the model behind the search form of `frontend\modules\DepartmentSelection\models\SelectionSettingEntry`.
 */
class SelectionSettingEntrySearch extends SelectionSettingEntry
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'acadYear_relId', 'sem_relId', 'clslvl_relId', 'max_count_dept', 'status', 'approved_by', 'updated_by', 'created_by'], 'integer'],
            [['approved_at', 'updated_at', 'created_at', 'data', 'date_from','date_to'], 'safe'],
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
        $query = SelectionSettingEntry::find();

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
            'acadYear_relId' => $this->acadYear_relId,
            'sem_relId' => $this->sem_relId,
            'clslvl_relId' => $this->clslvl_relId,
            'max_count_dept' => $this->max_count_dept,
            'status' => $this->status,
            'approved_at' => $this->approved_at,
            'approved_by' => $this->approved_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
