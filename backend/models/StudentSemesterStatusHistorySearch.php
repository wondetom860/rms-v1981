<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\StudentSemesterStatusHistory;

/**
 * StudentSemesterStatusHistorySearch represents the model behind the search form about `backend\models\StudentSemesterStatusHistory`.
 */
class StudentSemesterStatusHistorySearch extends StudentSemesterStatusHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sssh_std_relId', 'sssh_acadYear_relId', 'sssh_sem_relId', 'sssh_clslvl_relId', 'sssh_sssn_relId', 'sssh_detault_sssn_relId', 'sssh_created_by', 'sssh_updated_by'], 'integer'],
            [['sssh_created_at', 'sssh_updated_at'], 'safe'],
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
        $query = StudentSemesterStatusHistory::find();

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
            'sssh_std_relId' => $this->sssh_std_relId,
            'sssh_acadYear_relId' => $this->sssh_acadYear_relId,
            'sssh_sem_relId' => $this->sssh_sem_relId,
            'sssh_clslvl_relId' => $this->sssh_clslvl_relId,
            'sssh_sssn_relId' => $this->sssh_sssn_relId,
            'sssh_detault_sssn_relId' => $this->sssh_detault_sssn_relId,
            'sssh_created_by' => $this->sssh_created_by,
            'sssh_updated_by' => $this->sssh_updated_by,
            'sssh_created_at' => $this->sssh_created_at,
            'sssh_updated_at' => $this->sssh_updated_at,
        ]);

        return $dataProvider;
    }
}
