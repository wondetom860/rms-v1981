<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\WStudentReadmissionHistory;

/**
 * WStudentReadmissionHistorySearch represents the model behind the search form about `backend\models\WStudentReadmissionHistory`.
 */
class WStudentReadmissionHistorySearch extends WStudentReadmissionHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'srh_acadYear_relId', 'srh_sem_relId', 'srh_swh_relId', 'srh_std_relId', 'srh_HOD_approved', 'srh_HOD_approved_by', 'srh_approved', 'srh_approved_by'], 'integer'],
            [['srh_return_back_on', 'srh_HOD_approved_at', 'srh_approved_at', 'srh_return_back_letter', 'remark_HOD', 'remark_registrar'], 'safe'],
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
        $query = WStudentReadmissionHistory::find();

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
            'srh_acadYear_relId' => $this->srh_acadYear_relId,
            'srh_sem_relId' => $this->srh_sem_relId,
            'srh_swh_relId' => $this->srh_swh_relId,
            'srh_std_relId' => $this->srh_std_relId,
            'srh_return_back_on' => $this->srh_return_back_on,
            'srh_HOD_approved' => $this->srh_HOD_approved,
            'srh_HOD_approved_at' => $this->srh_HOD_approved_at,
            'srh_HOD_approved_by' => $this->srh_HOD_approved_by,
            'srh_approved' => $this->srh_approved,
            'srh_approved_at' => $this->srh_approved_at,
            'srh_approved_by' => $this->srh_approved_by,
        ]);

        $query->andFilterWhere(['like', 'srh_return_back_letter', $this->srh_return_back_letter])
            ->andFilterWhere(['like', 'remark_HOD', $this->remark_HOD])
            ->andFilterWhere(['like', 'remark_registrar', $this->remark_registrar]);

        return $dataProvider;
    }
}
