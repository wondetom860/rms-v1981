<?php

namespace frontend\modules\Reassessment\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\Reassessment\models\ReassessorMark;

/**
 * ReassessorMarkSearch represents the model behind the search form of `frontend\modules\Reassessment\models\ReassessorMark`.
 */
class ReassessorMarkSearch extends ReassessorMark
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ras_relId', 'ras_ima_relId', 'created_by', 'created_at', 'updated_at', 'updated_by'], 'integer'],
            [['ras_original_mark', 'ras_changed_to'], 'number'],
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
        $query = ReassessorMark::find();

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
            'ras_ima_relId' => $this->ras_ima_relId,
            'ras_original_mark' => $this->ras_original_mark,
            'ras_changed_to' => $this->ras_changed_to,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
