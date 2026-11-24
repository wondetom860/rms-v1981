<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PgSecondarySchoolsAttended;

/**
 * PgSecondarySchoolsAttendedSearch represents the model behind the search form about `backend\models\PgSecondarySchoolsAttended`.
 */
class PgSecondarySchoolsAttendedSearch extends PgSecondarySchoolsAttended
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pssa_pei_relId'], 'integer'],
            [['pgssa_name_of_school', 'pgssa_location', 'pgssa_years_from', 'pgssa_years_to'], 'safe'],
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
        $query = PgSecondarySchoolsAttended::find();

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
            'pssa_pei_relId' => $this->pssa_pei_relId,
            'pgssa_years_from' => $this->pgssa_years_from,
            'pgssa_years_to' => $this->pgssa_years_to,
        ]);

        $query->andFilterWhere(['like', 'pgssa_name_of_school', $this->pgssa_name_of_school])
            ->andFilterWhere(['like', 'pgssa_location', $this->pgssa_location]);

        return $dataProvider;
    }
}
