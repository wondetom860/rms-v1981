<?php

namespace frontend\modules\ExitExam\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\ExitExam\models\EtTestDetail;

/**
 * EtTestDetailSearch represents the model behind the search form of `frontend\modules\ExitExam\models\EtTestDetail`.
 */
class EtTestDetailSearch extends EtTestDetail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'test_relId', 'acadYear_relId', 'pass_status', 'date_submitted', 'grade_accepted', 'grade_accepted_at', 'grade_accepted_by', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['mark_obtained'], 'number'],
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
        $query = EtTestDetail::find();

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
            'test_relId' => $this->test_relId,
            'acadYear_relId' => $this->acadYear_relId,
            'mark_obtained' => $this->mark_obtained,
            'pass_status' => $this->pass_status,
            'date_submitted' => $this->date_submitted,
            'grade_accepted' => $this->grade_accepted,
            'grade_accepted_at' => $this->grade_accepted_at,
            'grade_accepted_by' => $this->grade_accepted_by,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
