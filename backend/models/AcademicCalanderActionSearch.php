<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AcademicCalanderAction;

/**
 * AcademicCalanderActionSearch represents the model behind the search form about `backend\models\AcademicCalanderAction`.
 */
class AcademicCalanderActionSearch extends AcademicCalanderAction
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'aca_created_by', 'aca_updated_by'], 'integer'],
            [['aca_name','aca_link', 'aca_created_at', 'aca_updated_at'], 'safe'],
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
        $query = AcademicCalanderAction::find();

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
            'aca_created_by' => $this->aca_created_by,
            'aca_created_at' => $this->aca_created_at,
            'aca_updated_by' => $this->aca_updated_by,
            'aca_updated_at' => $this->aca_updated_at,
        ]);

        $query->andFilterWhere(['like', 'aca_name', $this->aca_name])->andFilterWhere(['like', 'aca_link', $this->aca_link]);

        return $dataProvider;
    }
}
