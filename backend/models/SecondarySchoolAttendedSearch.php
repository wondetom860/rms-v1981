<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SecondarySchoolAttended;

/**
 * SecondarySchoolAttendedSeasrch represents the model behind the search form about `backend\models\SecondarySchoolAttended`.
 */
class SecondarySchoolAttendedSearch extends SecondarySchoolAttended
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ssa_ei_relId', 'ssa_created_by', 'ssa_updated_by'], 'integer'],
            [['ssa_school_name', 'ssa_town', 'ssa_year', 'ssa_gradeLevels', 'ssa_created_at', 'ssa_updated_at'], 'safe'],
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
        $query = SecondarySchoolAttended::find();

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
            'ssa_ei_relId' => $this->ssa_ei_relId,
            'ssa_year' => $this->ssa_year,
            'ssa_created_by' => $this->ssa_created_by,
            'ssa_created_at' => $this->ssa_created_at,
            'ssa_updated_by' => $this->ssa_updated_by,
            'ssa_updated_at' => $this->ssa_updated_at,
        ]);

        $query->andFilterWhere(['like', 'ssa_school_name', $this->ssa_school_name])
            ->andFilterWhere(['like', 'ssa_town', $this->ssa_town])
            ->andFilterWhere(['like', 'ssa_gradeLevels', $this->ssa_gradeLevels]);

        return $dataProvider;
    }
}
