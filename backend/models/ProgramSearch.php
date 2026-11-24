<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Program;

/**
 * ProgramSearch represents the model behind the search form about `backend\models\Program`.
 */
class ProgramSearch extends Program
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['total_crdt_hrs', 'program_adm_relId','program_duration', 'program_status', 'program_created_by', 'program_updated_by'], 'integer'],
            [['program_name', 'program_desc', 'program_created_at', 'program_updated_at','program_prefix'], 'safe'],
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
        $query = Program::find();

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
            'program_adm_relId' => $this->program_adm_relId,
            'program_status' => $this->program_status,
            'program_prefix' => $this->program_prefix,
        ]);

        $query->andFilterWhere(['like', 'program_name', $this->program_name])
            ->andFilterWhere(['like', 'program_desc', $this->program_desc]);

        return $dataProvider;
    }
}
