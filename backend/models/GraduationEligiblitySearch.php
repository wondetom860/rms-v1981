<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\GraduationEligiblity;

/**
 * GraduationEligiblitySearch represents the model behind the search form about `\frontend\models\GraduationEligiblity`.
 */
class GraduationEligiblitySearch extends GraduationEligiblity
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ge_adm_relId', 'ge_prog_relId', 'ge_num_of_F', 'ge_min_crdthr', 'ge_created_by', 'ge_updated_by'], 'integer'],
            [['ge_min_CGPA', 'ge_min_CGPA_major'], 'number'],
            [['ge_created_at', 'ge_updated_at'], 'safe'],
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
        $query = GraduationEligiblity::find();

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
            'ge_adm_relId' => $this->ge_adm_relId,
            'ge_prog_relId' => $this->ge_prog_relId,
            'ge_num_of_F' => $this->ge_num_of_F,
            'ge_min_crdthr' => $this->ge_min_crdthr,
            'ge_min_CGPA' => $this->ge_min_CGPA,
            'ge_min_CGPA_major' => $this->ge_min_CGPA_major,
            'ge_created_at' => $this->ge_created_at,
            'ge_created_by' => $this->ge_created_by,
            'ge_updated_at' => $this->ge_updated_at,
            'ge_updated_by' => $this->ge_updated_by,
        ]);

        return $dataProvider;
    }
}
