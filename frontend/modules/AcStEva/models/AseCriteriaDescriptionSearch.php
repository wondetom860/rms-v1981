<?php

namespace frontend\modules\AcStEva\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\AcStEva\models\AseCriteriaDescription;

/**
 * AseCriteriaDescriptionSearch represents the model behind the search form of `frontend\modules\AcStEva\models\AseCriteriaDescription`.
 */
class AseCriteriaDescriptionSearch extends AseCriteriaDescription
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_relId', 'max_rating_scale', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['code', 'description', 'data'], 'safe'],
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
        $query = AseCriteriaDescription::find();

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
            'category_relId' => $this->category_relId,
            'max_rating_scale' => $this->max_rating_scale,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
