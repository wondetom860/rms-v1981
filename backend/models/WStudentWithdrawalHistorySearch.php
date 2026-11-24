<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\WStudentWithdrawalHistory;

/**
 * WStudentWithdrawalHistorySearch represents the model behind the search form about `backend\models\WStudentWithdrawalHistory`.
 */
class WStudentWithdrawalHistorySearch extends WStudentWithdrawalHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'swh_acadYear_relId', 'swh_sem_relId', 'swh_std_relId', 'swh_reason_relId', 'swh_course_removed', 'swh_approved', 'swh_approved_by', 'swh_created_by', 'swh_updated_by'], 'integer'],
            [['swh_approved_at', 'swh_created_at', 'swh_updated_at'], 'safe'],
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
        $query = WStudentWithdrawalHistory::find();

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
            'swh_acadYear_relId' => $this->swh_acadYear_relId,
            'swh_sem_relId' => $this->swh_sem_relId,
            'swh_std_relId' => $this->swh_std_relId,
            'swh_reason_relId' => $this->swh_reason_relId,
            'swh_course_removed' => $this->swh_course_removed,
            'swh_approved' => $this->swh_approved,
            'swh_approved_by' => $this->swh_approved_by,
            'swh_approved_at' => $this->swh_approved_at,
            'swh_created_at' => $this->swh_created_at,
            'swh_created_by' => $this->swh_created_by,
            'swh_updated_at' => $this->swh_updated_at,
            'swh_updated_by' => $this->swh_updated_by,
        ]);

        return $dataProvider;
    }
}
