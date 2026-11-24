<?php

namespace frontend\modules\GC\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\GC\models\GcSpecialMention;

/**
 * GcSpecialMentionSearch represents the model behind the search form of `frontend\modules\GC\models\GcSpecialMention`.
 */
class GcSpecialMentionSearch extends GcSpecialMention
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'gsm_created_by', 'gsm_updated_by'], 'integer'],
            [['gsm_maxCGPA', 'gsm_minCGPA'], 'number'],
            [['gsm_name', 'gsm_created_at', 'gsm_updated_at'], 'safe'],
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
        $query = GcSpecialMention::find();

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
            'gsm_maxCGPA' => $this->gsm_maxCGPA,
            'gsm_minCGPA' => $this->gsm_minCGPA,
            'gsm_created_by' => $this->gsm_created_by,
            'gsm_created_at' => $this->gsm_created_at,
            'gsm_updated_at' => $this->gsm_updated_at,
            'gsm_updated_by' => $this->gsm_updated_by,
        ]);

        $query->andFilterWhere(['like', 'gsm_name', $this->gsm_name]);

        return $dataProvider;
    }
}
