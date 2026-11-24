<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EnteranceExamMark;

/**
 * EnteranceExamMarkSeasrch represents the model behind the search form about `backend\models\EnteranceExamMark`.
 */
class EnteranceExamMarkSearch extends EnteranceExamMark
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'eh_ei_relId', 'eem_markObtained', 'eem_created_by', 'eem_updated_by'], 'integer'],
            [['eem_subject', 'eem_reference_number', 'eem_year', 'eem_created_at', 'eem_updated_at'], 'safe'],
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
        $query = EnteranceExamMark::find();

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
            'eh_ei_relId' => $this->eh_ei_relId,
            'eem_year' => $this->eem_year,
            'eem_markObtained' => $this->eem_markObtained,
            'eem_created_at' => $this->eem_created_at,
            'eem_created_by' => $this->eem_created_by,
            'eem_updated_at' => $this->eem_updated_at,
            'eem_updated_by' => $this->eem_updated_by,
        ]);

        $query->andFilterWhere(['like', 'eem_subject', $this->eem_subject])
            ->andFilterWhere(['like', 'eem_reference_number', $this->eem_reference_number]);

        return $dataProvider;
    }
}
