<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Specialization;

/**
 * SpecializationSearch represents the model behind the search form about `backend\models\Specialization`.
 */
class SpecializationSearch extends Specialization
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spec_adm_relId', 'spec_prog_relId', 'spec_dep_relId', 'spec_status', 'spec_created_by', 'spec_updated_by'], 'integer'],
            [['spec_name', 'spec_desc', 'spec_created_at', 'spec_updated_at','spec_code'], 'safe'],
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
        $query = Specialization::find();

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
            'spec_code' => $this->spec_code,
            'spec_adm_relId' => $this->spec_adm_relId,
            'spec_prog_relId' => $this->spec_prog_relId,
            'spec_dep_relId' => $this->spec_dep_relId,
            'spec_status' => $this->spec_status,
        ]);

        $query->andFilterWhere(['like', 'spec_name', $this->spec_name])
            ->andFilterWhere(['like', 'spec_desc', $this->spec_desc]);

        return $dataProvider;
    }
}
