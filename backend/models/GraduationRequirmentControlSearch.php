<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\GraduationRequirmentControl;

/**
 * GraduationRequirmentControlSearch represents the model behind the search form about `backend\models\GraduationRequirmentControl`.
 */
class GraduationRequirmentControlSearch extends GraduationRequirmentControl
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'gr_program_relId', 'gr_updated_by'], 'integer'],
            [['gr_maj', 'gr_min'], 'number'],
            [['gr_created_at', 'gr_created_by', 'gr_updated_at'], 'safe'],
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
        $query = GraduationRequirmentControl::find();

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
            'gr_program_relId' => $this->gr_program_relId,
            'gr_maj' => $this->gr_maj,
            'gr_min' => $this->gr_min,
            'gr_created_at' => $this->gr_created_at,
            'gr_created_by' => $this->gr_created_by,
            'gr_updated_at' => $this->gr_updated_at,
            'gr_updated_by' => $this->gr_updated_by,
        ]);

        return $dataProvider;
    }
}
