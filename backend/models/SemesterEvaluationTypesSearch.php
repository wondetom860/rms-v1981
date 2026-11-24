<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SemesterEvaluationTypes;

/**
 * SemesterEvaluationTypesSearch represents the model behind the search form about `backend\models\SemesterEvaluationTypes`.
 */
class SemesterEvaluationTypesSearch extends SemesterEvaluationTypes
{
    /**
     * @inheritdoc
     */
    public function rules()
    { //
        return [
            [['id', 'semev_type_level'], 'integer'],
            [['id', 'semev_type_maxPoint'], 'number'],
            [['semev_type_desc'], 'safe'],
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
        $query = SemesterEvaluationTypes::find();

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
            'semev_type_level' => $this->semev_type_level,
            'semev_type_maxPoint'=>$this->semev_type_maxPoint,
        ]);

        $query->andFilterWhere(['like', 'semev_type_desc', $this->semev_type_desc]);

        return $dataProvider;
    }
}
