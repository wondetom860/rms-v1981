<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PgPostSecondarySchool;

/**
 * PgPostSecondarySchoolSearch represents the model behind the search form about `backend\models\PgPostSecondarySchool`.
 */
class PgPostSecondarySchoolSearch extends PgPostSecondarySchool
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ppss_pei_relId'], 'integer'],
            [['pgpss_institute_name', 'pgpss_location', 'pgpss_deploma_awarded', 'pgpss_date_of_award'], 'safe'],
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
        $query = PgPostSecondarySchool::find();

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
            'ppss_pei_relId' => $this->ppss_pei_relId,
            'pgpss_date_of_award' => $this->pgpss_date_of_award,
        ]);

        $query->andFilterWhere(['like', 'pgpss_institute_name', $this->pgpss_institute_name])
            ->andFilterWhere(['like', 'pgpss_location', $this->pgpss_location])
            ->andFilterWhere(['like', 'pgpss_deploma_awarded', $this->pgpss_deploma_awarded]);

        return $dataProvider;
    }
}
