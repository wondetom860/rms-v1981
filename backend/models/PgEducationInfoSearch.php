<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PgEducationInfo;

/**
 * PgEducationInfoSearch represents the model behind the search form about `backend\models\PgEducationInfo`.
 */
class PgEducationInfoSearch extends PgEducationInfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['pgei_UG_university_name', 'pgei_field_of_study', 'pgei_major', 'pgei_minor', 'pgei_work_experience', 'pgei_reserches', 'pgei_awards'], 'safe'],
            [['pgei_CGPA'], 'number'],
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
        $query = PgEducationInfo::find();

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
            'pgei_CGPA' => $this->pgei_CGPA,
        ]);

        $query->andFilterWhere(['like', 'pgei_UG_university_name', $this->pgei_UG_university_name])
            ->andFilterWhere(['like', 'pgei_field_of_study', $this->pgei_field_of_study])
            ->andFilterWhere(['like', 'pgei_major', $this->pgei_major])
            ->andFilterWhere(['like', 'pgei_minor', $this->pgei_minor])
            ->andFilterWhere(['like', 'pgei_work_experience', $this->pgei_work_experience])
            ->andFilterWhere(['like', 'pgei_reserches', $this->pgei_reserches])
            ->andFilterWhere(['like', 'pgei_awards', $this->pgei_awards]);

        return $dataProvider;
    }
}
